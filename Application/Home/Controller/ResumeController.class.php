<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-03-30
 * Time: 22:33
 */
namespace Home\Controller;
use Think\Controller;
use Think\Verify;
use Home\Common;
use Home\Controller\Base;

class ResumeController extends BaseController{
    //个人简历首页
    public function index()
    {
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Resume","请先登录",false);
        }
        $map = array();
        $map['user_id'] = $_SESSION['user_id'];
        $Resume = M('Resume');
        $list =  $Resume->where($map)->find();
        $this->assign('list',$list);
        $this->display();
    }

    //新增简历
    public function add(){
        session_start();
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Resume","请先登录",false);
        }
        $data = array();
        $data['user_id'] = $_SESSION['user_id'];
        $data['name'] = I('post.name');
        $data['sex'] = I('post.sex');
        $data['degree'] = I('post.degree');
        $data['email'] = I('post.email');
        $data['telphone'] = I('post.telphone');
        $data['hopework'] = I('post.hopework');
        $data['edu'] = I('post.edu');
        $data['working'] = I('post.working');
        $data['reward'] = I('post.reward');
        $data['sckill'] = I('post.sckill');
        $data['self'] = I('post.self');
        $Resume = M('Resume');
        $result =  $Resume->add($data);
        if(!empty($result)){
            $_SESSION['resume_id'] = $result;
            $this->ajaxReturn("login","保存成功",true);
        }else{
            $this->ajaxReturn("login","保存失败",true);
        }
    }

    //编辑简历
    public function edit(){
        session_start();
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Resume","请先登录",false);
        }
        $data = array();
        $data['name'] = I('post.name');
        $data['sex'] = I('post.sex');
        $data['degree'] = I('post.degree');
        $data['email'] = I('post.email');
        $data['telphone'] = I('post.telphone');
        $data['hopework'] = I('post.hopework');
        $data['edu'] = I('post.edu');
        $data['working'] = I('post.working');
        $data['reward'] = I('post.reward');
        $data['sckill'] = I('post.sckill');
        $data['self'] = I('post.self');
        $map = array();
        $map['user_id'] = $_SESSION['user_id'];
        $Resume = M('Resume');
        $result = $Resume->where($map)->save($data);
        if(false !== $result || 0 !== $result){
            $this->ajaxReturn("Resume","编辑成功",false);
        }else{
            $this->ajaxReturn("Resume","编辑失败",false);
        }
    }
}