<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 2018/12/21
 * Time: 16:45
 */

namespace App\Http\Controllers\Recharge;

use App\Http\Controllers\Controller;

class BasePayController extends Controller
{
    private $body;             //商品描述
    private $money;            //充值金额
    private $out_trade_no;     //交易流水号
    private $client_ip;        //客户端IP
    private $response;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getClientIp()
    {
        return $this->client_ip;
    }

    /**
     * @param mixed $client_ip
     */
    public function setClientIp($client_ip)
    {
        $this->client_ip = $client_ip;
    }

    /**
     * @return mixed
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @param mixed $money
     */
    public function setMoney($money)
    {
        $this->money = $money;
    }

    /**
     * @return mixed
     */
    public function getOutTradeNo()
    {
        return $this->out_trade_no;
    }

    /**
     * @param mixed $out_trade_no
     */
    public function setOutTradeNo($out_trade_no)
    {
        $this->out_trade_no = $out_trade_no;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function clientType()
    {
        if ($this->isWeixinClient()) {
            return 'weixin';
        } else if ($this->isMobileClient()) {
            return 'phone';
        } else {
            return 'pc';
        }
    }

    /**
     * 是否移动端访问访问.
     *
     * @return bool
     */
    protected function isMobileClient()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }

        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            //找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
        }

        //判断手机发送的客户端标志,兼容性有待提高
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp',
                'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu',
                'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi',
                'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile',
            );

            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match('/(' . implode('|', $clientkeywords) . ')/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }

        //协议法，因为有可能不准确，放到最后判断
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((false !== strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml'))
                && (false === strpos($_SERVER['HTTP_ACCEPT'], 'text/html')
                    || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))
                )) {
                return true;
            }
        }

        return false;
    }


    /**
     * 是否为微信内部浏览器
     *
     * @return bool
     */
    protected function isWeixinClient()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        } else {
            return false;
        }
    }

}