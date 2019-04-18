<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        // 加入可用命令列表
        Commands\SmsNotifyCommand::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // 批量执行时需要在这里定义，这里为每分钟执行
        $schedule->command('sms-notify')->everyMinute();
    }

}
