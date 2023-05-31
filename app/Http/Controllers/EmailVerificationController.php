<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\Request;
use Exception;
use Cache;
use Mail;


class EmailVerificationController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * @throws Exception
     */
    public function verify(Request $request)
    {
        // 从 url 中获取 email、token两个参数
        $email = $request->input('email');
        $token = $request->input('token');
        // 如果有一个为空，则不是一个合法的链接，抛出异常
        if(!$email || !$token){
            throw new Exception('验证链接不正确');
        }
        // 从缓存中读取数据，把从url中获取的token与缓存中比较
        // 如果缓存不存在或返回的值不一致，抛出异常
        if($token != Cache::get('email_verification_'.$email)){
            throw new Exception('验证链接不正确或已过期');
        }
        // 根据email从数据库中获取对应的用户
        // 通常来说能通过 token 校验的情况下不可能出现用户不存在
        // 但是为了代码的健壮性我们还是需要做这个判断
        if(!$user = User::where('email', $email)->first()){
            throw new Exception('用户不存在');
        }
        // 将指定的 key 从缓存中删除
        Cache::forget('email_verification_'.$email);
        // 更新用户email_verified字段值
        $user->update(['email_verified' => true]);
        return view('pages.success', ['msg' => '邮箱验证成功']);
    }

    /**
     * 手动发送激活邮件
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * @throws Exception
     */
    public function send(Request $request)
    {
        $user = $request->user();
        // 判断是否已经激活
        if($user->email_verified){
            throw new Exception('你已经验证过邮箱了');
        }
        // 调用notify()发送我们定义好的通知类
        $user->notify(new EmailVerificationNotification());

        return view('pages.success', ['msg' => '邮件发送成功']);
    }
}
