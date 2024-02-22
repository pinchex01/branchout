<form id="moodleform" name="moodleform" method="post" action="{{ config('pesaflow.url')}}" target="my_frame">
    <input type="hidden" name="apiClientID" value="{{ $config['apiClientId'] }}" >
    <input type="hidden" name="secureHash" value="{{ $signature }}" >
    <input type="hidden" name="billDesc" value="{{ $payment->notes }}" >
    <input type="hidden" name="billRefNumber" value="{{ $payment->payment_key }}" >
    <input type="hidden" name="currency" value="{{ $config['currency'] }}" >
    <input type="hidden" name="serviceID" value="{{ $config['apiServiceId'] }}" >
    <input type="hidden" name="clientMSISDN" value="{{ $user->phone }}" >
    <input type="hidden" name="clientName" value="{{ $user->full_name}}" >
    <input type="hidden" name="clientIDNumber" value="{{ $user->id_number }}" >
    <input type="hidden" name="clientEmail" value="{{ $user->email }}" >
    <input type="hidden" name="pictureURL" value="{{ $user->getAvatar() }}" >
    <input type="hidden" name="callBackURLOnSuccess" value="{{ route('app.orders.list',['success' => 'true']) }}" >
    <input type="hidden" name="callBackURLOnFail" value="{{ route('app.orders.list',['success' => 'failed'])}}" >
    <input type="hidden" name="notificationURL" value="{{  config('pesaflow.ipn_endpoint') }}" >
    <input type="hidden" name="amountExpected" value="{{ $payment->total }}" >
</form>


<iframe style="border: none;" scrolling="no" id="my_frame" width="100%" height="900px" name="my_frame" ></iframe>
<script>
    let $form  = document.getElementById('moodleform');
    $form.submit();
</script>