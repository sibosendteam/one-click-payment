<?php
/**
 * Created by IntelliJ IDEA.
 * User: CMH
 * Date: 2018/12/13
 * Time: 18:54
 */

return [
    'app_id' =>          "xxxxxxxxxxx",               //微信分配的公众账号ID
    'mch_id' =>          "xxxxxxxxxxx",               //微信支付分配的商户号
    'api_key'=>          "xxxxxxxxxxxxxxxxxxxxxx", //Api Key
    'app_secret' =>      "xxxxxxxxxxxxxxxxxxxxxx", //微信分配的公众账号secret

    //异步通知地址
    'notify_url' =>      "https://xxx.xxx.com/pay/wechat/notify",
    //js api异步通知地址
    'notify_js_url' =>   "https://xxx.xxx.com/pay/wechat/japi/notify",
];
