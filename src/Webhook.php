<?php


namespace Revosystems\RedsysGateway;

use Revosystems\RedsysGateway\Models\ChargeResult;
use Revosystems\RedsysGateway\Lib\Constants\RESTConstants;
use Revosystems\RedsysGateway\Lib\Model\Message\RESTAuthenticationRequestOperationMessage;
use Revosystems\RedsysGateway\Lib\Model\Message\RESTResponseMessage;
use Revosystems\RedsysGateway\Lib\Service\Impl\RESTTrataRequestService;
use Revosystems\RedsysGateway\Models\ChargeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class Webhook
{
    /**
     * @var RedsysConfig
     */
    protected $config;

    public function __construct(RedsysConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @throws RedsysException
     */
    public function handle($operation, $posOrderId, ChargeRequest $data, Request $request)
    {
        $challengeRequest = $this->getRequestOperation($data, $posOrderId, $operation->getAmount(), $operation->getCurrency());
        $this->challenge($challengeRequest, $operation, $request);
        return $this->sendAuthenticationConfirmationOperation($challengeRequest, $data);
    }

    abstract protected function challenge(RESTAuthenticationRequestOperationMessage $challengeRequest, $operation, Request $request): void;

    protected function sendAuthenticationConfirmationOperation($challengeRequest, ChargeRequest $data)
    {
        if ($data->shouldSaveCard) {
            $challengeRequest->createReference();
        }
        $response   = RedsysRest::make(RESTTrataRequestService::class, $this->config->key, $this->config->test)->sendOperation($challengeRequest);
        $result     = $response->getResult();

        Log::debug("[REDSYS] Getting webhook authentication response {$result}");
        if ($result == RESTConstants::$RESP_LITERAL_KO) {
            Log::error("[REDSYS] Operation webhook authentication was not OK");
            return new ChargeResult(false, $this->getResponse($response));
        }
        $operation = $response->getOperation();
        if ($data->shouldSaveCard && $operation->getMerchantIdentifier()) {
            Redsys::tokenizeCards($operation, $data->customerToken);
        }
        return new ChargeResult(true, $this->getResponse($response));
    }

    protected function getResponse(RESTResponseMessage $response)
    {
        return [
            "result"    => $response->getResult(),
            "operation" => $response->getResult() !== 'KO' ? $response->getOperation() : null,
        ];
     }

    protected function getRequestOperation(ChargeRequest $data, $posOrderId, $amount, $currency)
    {
        return (new RESTAuthenticationRequestOperationMessage)
            ->setMerchant($this->config->code)
            ->setTerminal($this->config->terminal)
            ->generate($data, $posOrderId, $amount, $currency);
    }
}
