<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-03-30
 * Time: 22:10
 */
namespace Home\Controller;
use Think\Controller;
use Think\Verify;
use Home\Common;
use Home\Controller\Base;

class SeekerController extends BaseController{
    //
    public function index()
    {
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Seeker","���ȵ�¼",false);
        }
        $map = array();
        $map['user_id'] = $_SESSION['user_id'];
        $Users = M('Users');
        $list =  $Users->where($map)->find();
        $this->assign('list',$list);
        $this->display();
    }

    //
    public function edit()
    {
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Seeker","���ȵ�¼",false);
        }
        $map = array();
        $map['user_id'] = $_SESSION['user_id'];
        $data = array();
        $data['username'] = I('post.username');
        $data['sex'] = I('post.sex');
        $data['age_y'] = I('post.age_y');
        $data['age_m'] = I('post.age_m');
        $data['city'] = I('post.city');
        $data['idcard'] = I('post.idcard');
        $data['email'] = I('post.email');
        $data['telphone'] = I('post.telphone');
        $Users = M('Users');
        $list =  $Users->where($map)->find();
        $this->assign('list',$list);
        $this->display();
    }
}