<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 2019/1/22
 * Time: 19:38
 */

namespace App\Http\Controllers\Helper;


class Config
{

    /**
     * 微信分配的公众账号ID
     *
     * @return mixed
     */
    public static function getAppId()
    {
        return config('wechat')['app_id'];
    }


    /**
     * 微信支付分配的商户号
     *
     * @return mixed
     */
    public static function getMchId()
    {
        return config('wechat')['mch_id'];
    }

    /**
     * Api Key
     *
     * @return mixed
     */
    public static function getApiKey()
    {
        return config('wechat')['api_key'];
    }


    /**
     * 微信分配的公众账号secret
     *
     * @return mixed
     */
    public static function getAppSecret()
    {
        return config('wechat')['app_secret'];
    }


    /**
     * 微信浏览器外支付通知
     *
     * @return mixed
     */
    public static function getWxPayNotify()
    {
        return config('wechat')['notify_url'];
    }


    /**
     * 微信自带浏览器支付通知
     *
     * @return mixed
     */
    public static function getWxPayJsApiNotify()
    {
        return config('wechat')['notify_js_url'];
    }


    /**
     * 阿里云短信key
     *
     * @return mixed
     */
    public static function getSmsCodeKeyId()
    {
        return config('smscode')['accessKeyId'];
    }


    /**
     * 阿里云短信secret
     *
     * @return mixed
     */
    public static function getSmsCodeSecret()
    {
        return config('smscode')['accessSecret'];
    }
}