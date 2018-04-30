<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWechatSessionKeyToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('weapp_openid')->nullable()->unique()->after('wechat_openid')->comment('小程序open_id');
            $table->string('weapp_session_key')->nullable()->unique()->after('wechat_openid')->comment('小程序session_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('weapp_openid','weapp_session_key');
        });
    }
}
