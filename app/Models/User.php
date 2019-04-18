<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 2018/12/13
 * Time: 15:57
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "le_user";

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = ['mobile'];


    /**
     * 获取非会员用户
     *
     * @param $mobile
     * @return mixed
     */
    public static function getUserOfNotMember()
    {
        $result = self::where('member_status', '=', 0)
            ->orderBy('id', 'asc')
            ->limit(20)
            ->get();

        return $result;
    }

    /**
     * 将用户充值状态改为已充值
     *
     * @param $out_trade_no
     */
    public static function updateRechargeStatus($out_trade_no)
    {
        $result = self::join('le_order as order', 'order.user_id', '=', 'le_user.id')
            ->where('order.out_trade_no', '=', $out_trade_no)
            ->select('le_user.*')
            ->first();
        if ($result) {
            $result->recharge_status = 1;
            $result->save();
        }
    }


    /**
     * 获取会员用户发送短信通知用户名密码
     *
     * @return mixed
     */
    public static function getRechargeSuccessUser()
    {
        $result = self::where('member_status', '=', "会员")
            ->where('recharge_status', '=', '1')
            ->where('sms_notify_status', '=', '0')
            ->limit(20)
            ->get();

        return $result;
    }


    /**
     * 更新短信发送状态
     *
     * @param $mobile
     */
    public static function updateSmsNotifyStatus($mobile)
    {
        self::where('mobile', '=', $mobile)
            ->where('sms_notify_status', '=', '0')
            ->update(['sms_notify_status' => 1]);;
    }
}
