<?php

namespace Revosystems\RedsysPayment\Lib\Utils;

class RESTSignatureUtils
{
    public static function createMerchantSignature($key, $ent)
    {
        $key = RESTSignatureUtils::encrypt_3DES(static::getOrder($ent), base64_decode($key));
        return base64_encode(RESTSignatureUtils::mac256($ent, $key));
    }

    public static function createMerchantSignatureNotif($key, $datos)
    {
        $key = RESTSignatureUtils::encrypt_3DES(static::getOrderNotif($datos), base64_decode($key));
        return RESTSignatureUtils::base64_url_encode(RESTSignatureUtils::mac256($datos, $key));
    }

    private static function getOrder($datos)
    {
        $vars = json_decode(base64_decode($datos), true);
        return $vars['DS_MERCHANT_ORDER'] ?? $vars['Ds_Merchant_Order'];
    }

    private static function getOrderNotif($datos)
    {
        $vars = json_decode(base64_decode($datos), true);
        return $vars['Ds_Order'] ?? $vars['DS_ORDER'];
    }

    private static function encrypt_3DES($message, $key)
    {
        // Se establece un IV por defecto
        $bytes = [0,0,0,0,0,0,0,0]; //byte [] IV = {0, 0, 0, 0, 0, 0, 0, 0}
        $iv    = implode(array_map("chr", $bytes)); //PHP 4 >= 4.0.2

        // Se cifra
        if (phpversion() < 7) {
            $ciphertext = mcrypt_encrypt(MCRYPT_3DES, $key, $message, MCRYPT_MODE_CBC, $iv); //PHP 4 >= 4.0.2
            return $ciphertext;
        }
        $l       = ceil(strlen($message) / 8) * 8;
        $message = $message.str_repeat("\0", $l - strlen($message));
        return substr(openssl_encrypt($message, 'des-ede3-cbc', $key, OPENSSL_RAW_DATA, "\0\0\0\0\0\0\0\0"), 0, $l);
    }

    private static function base64_url_encode($input)
    {
        return strtr(base64_encode($input), '+/', '-_');
    }

    private static function mac256($ent, $key)
    {
        return hash_hmac('sha256', $ent, $key, true);
    }
}
