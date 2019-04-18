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
<div class="container text-center">

    <div id="oneclickpayment">
        <button class="btn btn-info btn-lg" @click="showPayModal()">PC（one-click experience for quick payment）</button>
        <payment-modal></payment-modal>
    </div>
</div>


<script src="/payment/payment_pc.js"></script>

</body>
</html>