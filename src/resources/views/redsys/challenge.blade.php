<iframe name="redsys_iframe_acs" name="redsys_iframe_acs" src="" id="redsys_iframe_acs" target="_parent"
        referrerpolicy="origin" sandbox="allow-same-origin allow-scripts allow-top-navigation allow-forms"
        height="95%" width="100%" style="border: none; display: none;">
</iframe>

<form name="redsysAcsForm" id="redsysAcsForm" action="{{$acsURL}}" method="POST" target="redsys_iframe_acs" style="border: none;">
    <table name="dataTable" style="border: 0; padding: 0">
        @if (! $creq)
            <input type="hidden" name="TermUrl" value="{{$termUrl}}">
            <input type="hidden" name="PaReq" value="{{$PaReq}}">
            <input type="hidden" name="MD" value="{{$md}}">
        @else
            <input type="hidden" name="creq" value="{{$creq}}">
        @endif
        @include('redsys::livewire.includes.loading-ring')
    </table>
</form>
