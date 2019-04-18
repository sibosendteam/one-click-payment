
# 一键快捷支付（One-Click Payment）

这是一个Laravel+Vue项目，只需要将编译的js引入html文件，即可享受任何页面的一键快捷支付(目前只支持微信)，包括PC端扫码支付，手机端浏览器跳转支付，微信内置浏览器支付，欢迎Star。

## 概览

* [效果预览](#效果预览)
* [搭建指南](#搭建指南)
* [JS编译](#JS编译)
* [JS引入指南](#JS引入指南)
* [贴心提示](#贴心提示)

## 效果预览

**[在线预览 &rarr;](https://le.lebandu.com)**

1. PC

   ![screenshot](/screenshots/pc.png)
   
   ![screenshot](/screenshots/scan_pay.png)
2. Phone

   ![screenshot](/screenshots/phone.png)

## 搭建指南

本项目需要进行一些配置才能让你的页面「正确」跑起来。

**linux系统（nginx+php+mysql）**

1. 新建mysql数据库，导入 database/sql/oneclick_pay.sql 文件
2. 安装php composer依赖：composer install
3. 安装node modules: npm install
4. php的env文件中生成Key：php artisan key:generate
5. nginx配置   详见 xxx.xxx.com.config

**windows系统**

依次执行上述步骤1-4，[详情参考](https://learnku.com/docs/laravel/5.6/installation/1216)

## JS编译

* 一键支付核心代码位于目录 resources/assets/js/common_mobile 和 resources/assets/js/common_pc，这只是比较简单的案列，具体自行修改。
* webpack.mix.js配置编译目录，执行 npm run production 即可。

## JS引入指南

```
<!doctype html>
<head>
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
```

**注意：** 上述id和@click事件需要和resources/assets/js/common_pc/payment.js中相对应。

## 贴心提示

* 微信支付使用[omnipay-wechatpay](https://github.com/lokielse/omnipay-wechatpay)
* 短信使用阿里云短信
* 配置位于目录 config/wechat.php config/smscode.php 
* 微信内置浏览器获取需要openid，参考WeJsApiPay.php，第40行需要手动修改(遗留bug)
* 定时短信发送位于目录 app/Console/Commands/SmsNotifyCommand.php (php artisan sms-notify)
* 由于本人是php新手，代码不严谨，供参考即可

### 核心内容即编译支付js文件，任何页面都可享受一键快捷支付