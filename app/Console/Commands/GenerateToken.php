<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'l5bbs:generate-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '输入 ID 返回 token  有效期一年(仅供测试使用)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle ()
    {
        $userId = $this->ask('请输入用户 ID', 1);

        if ( env('APP_DEBUG') ) {
            return $this->error('仅供测试使用');
        }

        $user = User::find(trim($userId));

        if ( !$user ) {
            return $this->error('该用户不存在啊!');
        }
        $ttl = 365 * 24 * 60;//一年  单位为秒

        $token = \Auth::guard('api')->setTTL($ttl)->fromUser($user);

        $this->info($token);
    }
}
