<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>one-click payment</title>

    <!-- Fonts -->
    <link rel="stylesheet" type="text/css" href="/css/app.css">

</head>
<body>
<div class="text-center">

    <div id="oneclickpayment">
        <button class="btn btn-info btn-lg" @click="isShowPay=true">Phone（one-click experience for quick payment）</button>
        <payment-modal></payment-modal>
    </div>
</div>


<script src="/payment/payment_mobile.js"></script>

</body>
</html>