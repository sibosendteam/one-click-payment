<?php
/**
 * Created by IntelliJ IDEA.
 * User: CMH
 * Date: 2018/12/20
 * Time: 11:45
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Logger extends Model
{

    protected $table = "le_log";


    public $timestamps = false;

    public static function insertLog($user_id, $out_trade_no, $add_time)
    {
        $r = self::where('user_id', '=', $user_id)->first();
        if($r){
            $r->out_trade_no = $out_trade_no;
            $r->add_time = $add_time;
            $r->save();
        }
    }
}