<template>
    <modal name="payment-modal" transition="nice-modal-fade" width="760px" height="450px"
           :clickToClose="false" :delay="100" :adaptive="true" :backdrop="false"
           @before-open="beforeOpen">

        <div class="modal-header text-center">
            <button type="button" class="close" aria-label="Close" @click="$modal.hide('payment-modal')">
                <span aria-hidden="true">&times;</span></button>
            <span class="medal-title">{{modal_title}}</span>
        </div>

        <div class="modal-body">
            <div class="mar-top" v-show="isShowModal">
                <form name="registerForm" class="form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mobile">手机号码</label>
                        <div class="input-group col-md-6 col-sm-6 col-xs-12" style="padding-left: 10px">
                            <input class="form-control" style="border-radius: 4px" name="mobile" id="mobile"
                                   v-model="form.mobile"
                                   placeholder="输入手机号">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 30px">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="smscode">短信验证码</label>
                        <div class="input-group col-md-6 col-sm-6 col-xs-12" style="padding-left: 10px">
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

                <div class="mar-top text-center">
                    <button type="button" style="width: 180px" class="btn btn-primary" @click="register()">支付</button>
                </div>
            </div>

            <div v-show="!isShowModal">
                <div class="weixin-pay-cover">
                    <div class="weixin-pay">
                        <div class="need-pay text-center">支付<span class="bill-amount">{{pay_money}}</span>元</div>
                        <div class="qrcode-wrapper">
                            <div class="qrcode-block">
                                <div class="qrcode">
                                    <vue-qr :text="config.value"
                                            :size="config.size"></vue-qr>
                                </div>
                            </div>
                            <div class="desc">
                                <div class="icon"></div>
                                <div class="text"><p>打开手机微信</p>
                                    <p>扫一扫继续付款</p></div>
                            </div>
                        </div>
                        <div class="tips"></div>
                    </div>
                </div>
            </div>
        </div>

    </modal>
</template>

<script>
    import VueQr from 'vue-qr'

    export default {
        name: "PaymentModal",
        data() {
            return {
                isShowModal: undefined,
                modal_title: "One-click Payment",
                smsText: "获取验证码",
                pay_money: "00.00",
                isSendSms: false,
                auth_time: 0,
                auth_timer: undefined,
                form: {
                    mobile: undefined,
                    sms_code: undefined,
                    product: 1,
                    pay_type: "wechatpay",
                },
                config: {
                    value: "http://www.baidu.com",
                    size: 200,
                },
            }
        },
        methods: {
            beforeOpen() {
                this.isShowModal = true;
                this.form.mobile = undefined;
                this.form.sms_code = undefined;
                this.clearData();
            },
            clearData() {
                this.isSendSms = false;
                this.smsText = "获取验证码";
                this.auth_time = 0;
            },
            scanQrPay() {
                this.isShowModal = false;
            },
            smsCodeSendSuccess() {
                this.isSendSms = true;
                this.auth_time = 60;
                this.auth_timer = setInterval(() => {
                    this.auth_time--;
                    this.smsText = this.auth_time + "s 后重新获取";
                    if (this.auth_time <= 0) {
                        clearInterval(this.auth_timer);
                        this.clearData();
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
                    this.$loading('加载中...');
                    this.$http.post('https://xxx.xxx.com/register/vip', this.form).then(response => {
                        const data = response.data;
                        if (!data.success) {
                            if (data.hasOwnProperty("errors")) {
                                this.$toast.top('手机号或验证码错误');
                            } else if (data.hasOwnProperty('msg')) {
                                this.$toast.top(data.msg);
                            }
                        } else if (data.success) {
                            if (data.code_url != null) {
                                this.scanQrPay();    //pc扫码支付
                                this.config.value = data.code_url;
                                this.pay_money = data.pay_money;
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
        },
        components: {
            VueQr,
        },
    }
</script>

