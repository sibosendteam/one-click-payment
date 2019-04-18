<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 2019/1/24
 * Time: 18:32
 */

namespace App\Http\Controllers\Recharge;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\SmsCodeHelper;
use App\Models\SmsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SmsCodeController extends Controller
{

    /**
     * 获取验证码
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postSmsCode(Request $request)
    {
        $mobile = trim($request->get('mobile'));

        $rules = [
            'mobile' => 'required|digits:11',
        ];
        $validator = Validator::make($request->all(), $rules, config('validator'));
        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->messages()]);

        // ** 同一个手机号1分钟内不得超过1次 **
        $lastMinutes = strtotime("now") - 60;
        $lastLogCnt = SmsLog::where('mobile', '=', $mobile)
            ->where('addtime', '>', date('Y-m-d H:i:s', $lastMinutes))
            ->count();
        if ($lastLogCnt > 0) {
            return response()->json(['success' => false, "msg" => "发送间隔不得小于60s，请稍候再次提交"]);
        }

        // ** 同一个手机号一天之内不得提交超过5次 **
        $alreadyCnt = SmsLog::where('mobile', '=', $mobile)
            ->where('addtime', '>', date('Y-m-d 00:00:00'))
            ->count();
        if ($alreadyCnt >= 5) {
            return response()->json(['success' => false, "msg" => "同一手机号验证码短信发送超出5条"]);
        }

        //同一个ip 6小时内不得提交超过5次
        $last5Minutes = strtotime("now") - 6 * 60 * 60;
        $lastIpCnt = SmsLog::where("client_ip", "=", $request->getClientIp())
            ->where("addtime", ">", date("Y-m-d H:i:s", $last5Minutes))
            ->count();
        if ($lastIpCnt >= 5) {
            return response()->json(['success' => false, "msg" => "该ip提交过于频繁，请稍候再次提交"]);
        }

        $sms = new SmsCodeHelper();
        $smsCode = mt_rand(1000, 9999);
        session(['smscode' => $smsCode]);
        session(['smscode_expire_time' => strtotime("now") + 1 * 60]);
        $response = $sms->sendSmsAuthCode($mobile, $smsCode);


        if ($response["Message"] == "OK" && $response["Code"] == "OK") {
            $log = new SmsLog();
            $log->mobile = $mobile;
            $log->client_ip = $request->getClientIp();
            $log->addtime = date('Y-m-d H:i:s');
            $log->updatetime = date('Y-m-d H:i:s');
            $log->message = $response["Message"];
            $log->code = $response["Code"];
            $log->save();

            return response()->json(['success' => true, 'msg' => "验证码发送成功，请注意查收"]);
        }

        return response()->json(['success' => false, 'msg' => "验证码发送失败，请稍后重试"]);
    }

}