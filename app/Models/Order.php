<?php
/**
 * Created by IntelliJ IDEA.
 * User: CMH
 * Date: 2018/12/13
 * Time: 15:07
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{

    protected $table = "le_order";

    public $timestamps = false;

    /**
     * 提交订单后的错误信息
     *
     * @param $out_trade_no
     * @param $return_msg
     */
    public static function updateReturnMsg($out_trade_no, $return_msg)
    {
        $result = self::where('out_trade_no', '=', $out_trade_no)
            ->first();
        if ($result) {
            $result->return_msg = $return_msg;
            $result->recharge_status = 'FALL';
            $result->save();
        }
    }

    /**
     * 支付成功后更新订单信息
     * @param $out_trade_no
     * @param $transaction_id
     * @param $time_end
     * @param $cash_fee
     * @param $total_fee
     * @param $bank_type
     * @param $is_subscribe
     * @param $result_code
     * @param $err_code_des
     */
    public static function updatePayOrderMsg($out_trade_no, $transaction_id, $time_end, $cash_fee, $total_fee,
                                        $bank_type, $is_subscribe, $result_code, $err_code_des)
    {
        $result = self::where('out_trade_no', '=', $out_trade_no)
            ->first();
        if ($result) {
            $result->transaction_id = $transaction_id;
            $result->time_end = $time_end;
            $result->cash_fee = $cash_fee;
            $result->total_fee = $total_fee;
            $result->bank_type = $bank_type;
            $result->is_subscribe = $is_subscribe;
            $result->recharge_status = $result_code;
            $result->err_code_des = $err_code_des;
            $result->save();
        }
    }
}
