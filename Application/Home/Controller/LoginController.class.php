<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-03-30
 * Time: 21:59
 */
namespace Home\Controller;
use Think\Controller;
use Think\Verify;
use Home\Common;
use Home\Controller\Base;

class LoginController extends BaseController{
    //登录首页
    public function index(){
        $this->display();
    }

    //登录操作
    public function login(){
        session_start();
        $verify = I('post.Verify');
        if(check_verify($verify)){
            $this->ajaxReturn("Verify","验证码错误，请重新登录",false);
        }
        $map = array();
        $map['username'] = I('post.username');
        $map['password'] = I('post.password');
        $Users = M('Users');
        $result =  $Users->where($map)->find();
        if(!empty($result)){
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['role'] = $result['role'];
            $this->ajaxReturn("login","登录成功",true);
        }else{
            $this->ajaxReturn("login","登录失败",true);
        }
        $this->display();
    }
}