<?php

namespace App\Console\Commands;

use App\Http\Controllers\Helper\SmsCodeHelper;
use App\Models\Logger;
use App\Models\User;
use Illuminate\Console\Command;


/**
 * 注册成为会员后短信通知
 *
 *
 * Class SmsNotifyCommand
 * @package App\Console\Commands
 */
class SmsNotifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms-notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'member SMS notification';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
//        $log = new Logger();
//        $log->return_msg = "OK";
//        $log->save();
        $member = User::getRechargeSuccessUser();

        foreach ($member as $k => $v) {
            $sms = new SmsCodeHelper();

            try {
                $response = $sms->sendSmsNotify($member[$k]["mobile"]);

                if ($response["Message"] == "OK" && $response["Code"] == "OK") {
                    User::updateSmsNotifyStatus($member[$k]["mobile"]);
                }
            } catch (\Exception $e) {
                print $e;
            }
        }
    }
}
