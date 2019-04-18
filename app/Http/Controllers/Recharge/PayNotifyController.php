<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 2019/1/22
 * Time: 19:55
 */

namespace App\Http\Controllers\Recharge;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\Config;
use App\Models\Order;
use App\Models\User;
use Omnipay\Omnipay;

class PayNotifyController extends Controller
{

    /**
     * 微信 notify 响应
     */
    public function postWechatNotify()
    {
        $gateway = Omnipay::create('WechatPay');
        $gateway->setAppId(Config::getAppId());
        $gateway->setMchId(Config::getMchId());
        $gateway->setApiKey(Config::getApiKey());

        $response = $gateway->completePurchase([
            'request_params' => file_get_contents('php://input')
        ])->send();

        try {
            $this->updatePayResult($response->getRequestData());
        } catch (\Exception $e) {
            echo $e;
        }
    }


    /**
     * 微信 js api notify 响应
     */
    public function postWeJsApiNotify()
    {
        $wxPay = new WeJsApiNotify(
            Config::getMchId(),
            Config::getAppId(),
            Config::getApiKey()
        );
        try {
            $result = $wxPay->notify();
            //更新支付结果，获取付款金额$result['cash_fee']，获取订单号$result['out_trade_no']，修改数据库中的订单状态等;
            $this->updatePayResult($result);
        } catch (\Exception $e) {
            echo $e;
        }
    }


    /**
     * 更新支付结果
     *
     * @param $result
     */
    protected function updatePayResult($result)
    {
        if ($result["mch_id"] == config('wechat')['mch_id']) {
            if ($result["return_code"] == "SUCCESS") {
                User::updateRechargeStatus($result["out_trade_no"]);
                Order::updatePayOrderMsg(
                    $result["out_trade_no"],
                    $result["transaction_id"],
                    $result["time_end"],
                    $result["cash_fee"],
                    $result["total_fee"],
                    $result["bank_type"],
                    $result["is_subscribe"],
                    $result["result_code"],
                    null);
            } else {
                Order::updatePayOrderMsg(
                    $result["out_trade_no"],
                    $result["transaction_id"],
                    $result["time_end"],
                    $result["cash_fee"],
                    $result["total_fee"],
                    $result["bank_type"],
                    $result["is_subscribe"],
                    $result["result_code"],
                    $result["err_code_des"]);
            }
        }
    }
}