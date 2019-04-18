<?php
/**
 * Created by IntelliJ IDEA.
 * User: CMH
 * Date: 2018/12/13
 * Time: 14:30
 */

namespace App\Http\Controllers\Recharge;

use App\Http\Controllers\Helper\Config;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Omnipay\Omnipay;


class PayController extends BasePayController
{

    /**
     * PayController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        if ($this->clientType() == "weixin") {
            $wxPay = new WeJsApiPay(
                Config::getMchId(),
                Config::getAppId(),
                Config::getAppSecret(),
                Config::getApiKey()
            );
            $openId = $wxPay->GetOpenid();   //获取openid
            if (isset($openId))
                session(['openid' => $openId]);

            return view('client_wx');
        } else if($this->clientType() == "phone"){
            return view("client_phone");
        } else {
            return view("client_pc");
        }
    }


    /**
     * 生成一条用户信息并同时生成一个订单号
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRegister(Request $request)
    {
        $mobile = trim($request->get('mobile'));
        $sms_code = trim($request->get('sms_code'));
        $pay_type = trim($request->get('pay_type'));
        $product = $request->get('product');
        $client_ip = $request->getClientIp();

        $rules = [
            'mobile' => 'required|digits:11',
            'sms_code' => 'required|digits:4',
            'product' => 'required|exists:le_product,id',
            'pay_type' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules, config('validator'));
        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->messages()]);

        if ($sms_code != session('smscode') || strtotime('now') > session('smscode_expire_time', 0))
            return response()->json(['success' => false, 'msg' => '请输入正确的验证码']);

        $user = User::firstOrNew(['mobile' => $mobile]);
        if ($user->recharge_status == 1) {
            return response()->json(['success' => false, 'msg' => '您已经充值过了，请稍后登录']);
        } else {
            $user->member_status = "非会员";
            $user->recharge_status = 0;
            $user->client_ip = $client_ip;
            $user->add_time = time();
        }

        if ($user->save()) {
            //初始化支付参数
            $this->setPayParams($product, $client_ip);
            if ($this->createOrder($user->id, $pay_type)) {
                if ($pay_type == "wechatpay") {
                    if ($this->isWeixinClient()) {
                        // ** 微信自带浏览器支付结果 **
                        $weJsParamters = $this->getWeJsApiPay();
                        return response()->json(['success' => true,
                            'wejspay' => $weJsParamters]);
                    } else {
                        $this->getWechatPay();
                    }
                } else if ($pay_type == "alipay") {
                    // TODO
                } else {
                    return response()->json(['success' => false, 'msg' => '注册失败，请稍后重试或联系客服']);
                }
            }
        }

        // ** 微信外浏览器支付结果 **
        if ($this->getResponse()->isSuccessful()) {
            return response()->json(['success' => true,
                'mweb_url' => $this->getResponse()->getMwebUrl(),
                'code_url' => $this->getResponse()->getCodeUrl(),
                'pay_money' => $this->getMoney()]);
        } else {
            Order::updateReturnMsg($this->getOutTradeNo(),
                $this->getResponse()->getData()['return_code'] . '-'
                . $this->getResponse()->getData()['return_msg']);

            return response()->json(['success' => false, 'msg' => '注册失败，请稍后重试或联系客服']);
        }
    }


    /**
     * 设置微信支付的参数
     * @param $product
     * @param $client_ip
     */
    protected function setPayParams($product, $client_ip)
    {
        $product = Product::where('id', '=', $product)->first();
        $this->setBody($product->flag);
        $this->setMoney($product->price);
        $this->setClientIp($client_ip);
        $this->setOutTradeNo(date('YmdHis') . mt_rand(1000, 9999));
    }


    /**
     * gateways: WechatPay_App, WechatPay_Native, WechatPay_Js, WechatPay_Pos, WechatPay_Mweb
     * @param $gateway
     */
    protected function initWechatGateways($gateway)
    {
        $gateway->setAppId(Config::getAppId());
        $gateway->setMchId(Config::getMchId());
        $gateway->setApiKey(Config::getApiKey());
    }


    /**
     * 发起微信支付
     */
    protected function getWechatPay()
    {
        if ($this->clientType() == 'phone')
            $gateway = Omnipay::create('WechatPay_Mweb');
        if ($this->clientType() == 'pc')
            $gateway = Omnipay::create('WechatPay_Native');

        $this->initWechatGateways($gateway);
        $order = [
            'body' => $this->getBody(),
            'out_trade_no' => $this->getOutTradeNo(),
            'total_fee' => $this->getMoney() * 100,
            'spbill_create_ip' => $this->getClientIp(),
            'fee_type' => 'CNY',
            'notify_url' => Config::getWxPayNotify(),
        ];

        $response = $gateway->purchase($order)->send();

        $this->setResponse($response);
    }


    /**
     * 微信内浏览器支付
     * @return string
     */
    protected function getWeJsApiPay()
    {
        $outTradeNo = $this->getOutTradeNo();    //商品订单号
        $payAmount = $this->getMoney() * 100;    //付款金额，单位:分
        $orderName = $this->getBody();    //订单标题
        $notifyUrl = Config::getWxPayJsApiNotify();
        $payTime = time();      //付款时间

        $wxPay = new WeJsApiPay(
            Config::getMchId(), Config::getAppId(),
            Config::getAppSecret(), Config::getApiKey()
        );
        $jsApiParameters = $wxPay->CreateJsBizPackage(session('openid'), $payAmount, $outTradeNo,
            $this->getClientIp(), $orderName, $notifyUrl, $payTime);

        return $jsApiParameters;
    }


    /**
     * 生成支付订单
     *
     * @param $user_id
     * @param $pay_type
     * @return bool
     */
    protected function createOrder($user_id, $pay_type)
    {
        $order = new Order();
        $order->out_trade_no = $this->getOutTradeNo();
        $order->user_id = $user_id;
        $order->recharge_status = 'AWAIT';  //待充值
        $order->pay_type = $pay_type;
        $order->body = $this->getBody();
        $order->money = $this->getMoney();
        $order->add_time = time();
        $order->client_ip = $this->getClientIp();
        $order->trade_type = $this->clientType();

        if ($order->save())
            return true;

        return false;
    }

}
