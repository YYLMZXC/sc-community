<?php

/**
 * 
 * API登陆
 * */
$user = I('user');
$pass = I('pass');

if(empty($user)||empty($pass)){
    PR(300, '参数不能为空');
}
$data = M('user')->where(['user' => $user])->find();
if ($data == false) { //不存在的账号
    PR(300, '不存在的账号');
} else {
    if ($data['islock'] == 1) {
        PR(300, '账号被锁定,不能登录');
    }
    $pwd = $data['passwd'];
    if (md5($pass) != $pwd) {
        if ($data['error_times'] < 3) {
            M('user')->where(['id' => $data['id']])->setInc('error_times', 1);
        } else {
            M('user')->where(['id' => $data['id']])->save(['error_times' => 0, 'islock' => 1]);
        }
        PR(300, '密码错误，您还有' . (3 - $data['error_times']) . '次尝试机会');
    } else {
        M('user')->where(['id' => $data['id']])->save(['error_times' => 0, 'last_login_time' => time()]);
        SS('uid',$data['id']);
        setcookie('user-id',$data['token'],time()+24*3600*30);
        PR(200, '登录成功', ['accessToken' => $data['token']]);
    }
}
