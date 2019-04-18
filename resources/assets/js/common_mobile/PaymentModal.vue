<template>
    <div class="mobile" v-show="$parent.isShowPay">
        <div class="mobile-background" style="padding: 25px">
            <form name="registerForm" class="form-horizontal">
                <div class="form-group" style="padding: 0px 15px 0px 15px">
                    <input class="form-control" name="mobile" id="mobile" v-model="form.mobile"
                           placeholder="请输入手机号码">
                </div>
                <div class="form-group" style="padding: 10px 15px 10px 15px">
                    <div class="input-group">
                        <input class="form-control" name="smscode" id="smscode" v-model="form.sms_code"
                               placeholder="请输入4位数验证码">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-primary" style="width: 125px" @click="sendSmsCode"
                                    :disabled="isSendSms">
                                {{smsText}}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group" v-show="false">
                    <!--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mobile">产品类型</label>-->
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input class="form-control" name="product" id="product" v-model="form.product">
                    </div>
                </div>
                <div class="form-group" v-show="false">
                    <!--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mobile">支付方式</label>-->
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input class="form-control" name="pay_type" id="pay_type" v-model="form.pay_type">
                    </div>
                </div>
            </form>

            <div class="text-center">
                <button type="button" style="width: 150px" class="btn btn-primary" @click="register()">支付</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "PaymentModal",

        data() {
            return {
                smsText: "获取验证码",
                isSendSms: false,
                auth_time: 0,
                form: {
                    mobile: undefined,
                    sms_code: undefined,
                    product: 1,
                    pay_type: "wechatpay",
                },
            }
        },
        methods: {
            smsCodeSendSuccess() {
                this.isSendSms = true;
                this.auth_time = 60;
                var auth_timer = setInterval(() => {
                    this.auth_time--;
                    this.smsText = this.auth_time + "s 后重新获取";
                    if (this.auth_time <= 0) {
                        this.isSendSms = false;
                        clearInterval(auth_timer);
                        this.smsText = "获取验证码";
                    }
                }, 1000);
            },
            sendSmsCode() {
                if (this.validatorMobile()) {
                    this.$loading('加载中...');
                    this.$http.post('https://xxx.xxx.com/sms/code', {'mobile': this.form.mobile}).then(response => {
                        var data = response.data;
                        if (!data.success) {
                            if (data.hasOwnProperty('errors')) {
                                this.$toast.top("请输入正确的手机号码");
                            } else if (data.hasOwnProperty('msg')) {
                                this.$toast.top(data.msg);
                            }
                        } else if (data.success) {
                            this.smsCodeSendSuccess();
                            this.$toast.top(data.msg);
                        }
                        this.$loading.close();
                    }, () => {
                        this.$toast.top('请求异常，请稍后重试或联系客服');
                        this.$loading.close();
                    });
                }
            },
            register() {
                if (this.validatorMobile()) {
                    if (this.form.sms_code === undefined || this.form.sms_code.length != 4) {
                        this.$toast.top('请输入4位数验证码');
                        return;
                    }
                    this.$loading('努力加载中...');
                    this.$http.post('https://xxx.xxx.com/register/vip', this.form).then(response => {
                        const data = response.data;
                        if (!data.success) {
                            if (data.hasOwnProperty("errors")) {
                                this.$toast.top('手机号或验证码错误');
                            } else if (data.hasOwnProperty('msg')) {
                                this.$toast.top(data.msg);
                            }
                        } else if (data.success) {
                            if (data.hasOwnProperty('wejspay')) {
                                this.weixinPay(data.wejspay);  //微信自带浏览器支付
                            } else if (data.mweb_url != null) {
                                let url = data.mweb_url;
                                // url += '&redirect_url=' + encodeURIComponent(data.redirect_url); // redirect_url为回调地址
                                window.location = url;
                            } else {
                                this.$toast.top('注册失败，请稍后重试或联系客服');
                            }
                        }
                        this.$loading.close();
                    }, () => {
                        this.$toast.top('请求异常，请稍后重试或联系客服');
                        this.$loading.close();
                    });
                }
            },
            validatorMobile() {
                if (!/^1(3|4|5|7|8|9)\d{9}$/.test(this.form.mobile)) {
                    this.$toast.top('请输入正确的手机号码');
                    return false;
                }
                return true;
            },
            weixinPay: function (data) {
                var vm = this;
                if (typeof WeixinJSBridge == "undefined") {
                    if (document.addEventListener) {
                        document.addEventListener("WeixinJSBridgeReady", vm.onBridgeReady(data), false);
                    } else if (document.attachEvent) {
                        document.attachEvent("WeixinJSBridgeReady", vm.onBridgeReady(data));
                        document.attachEvent("onWeixinJSBridgeReady", vm.onBridgeReady(data));
                    }
                } else {
                    vm.onBridgeReady(data);
                }
            },
            onBridgeReady: function (data) {
                WeixinJSBridge.invoke(
                    "getBrandWCPayRequest",
                    {
                        'appId': data.appId, //公众号名称，由商户传入
                        'timeStamp': data.timeStamp, //时间戳，自1970年以来的秒数
                        'nonceStr': data.nonceStr, //随机串
                        'package': data.package, //订单详情扩展字符串
                        'signType': data.signType, //微信签名方式：
                        'paySign': data.paySign, //微信签名
                    },
                    function (res) {
                        if (res.err_msg == "get_brand_wcpay_request:ok") {
                            this.$toast.top('充值成功，请稍后登录');
                            window.location.reload();
                        } else {
                            this.$toast.top('充值失败，请稍后重试或联系客服');
                            // alert(res.err_desc);
                        }
                    }
                );
            }
        },
    }
</script>
