<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-03-31
 * Time: 22:07
 */
namespace Home\Controller;
use Think\Controller;
use Think\Verify;
use Home\Common;
use Home\Controller\Base;

class WorkController extends BaseController{
    //企业岗位表首页
    public function index(){
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Work","请先登录",false);
        }
        if($_SESSION['role'] !== 2 ){
            $this->ajaxReturn("Work","对不起，您没有权限进入该页面",false);
        }
        $map = array();
        $map['user_id'] = $_SESSION['user_id'];
        $WorkDao = M('Work');
        $list =  $WorkDao->where($map)->select();
        $this->assign('list',$list);
        $this->display();
    }

    //新增职位
    public function add(){
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Work","请先登录",false);
        }
        if($_SESSION['role'] !== 2 ){
            $this->ajaxReturn("Work","对不起，您没有权限进入该页面",false);
        }
        $data = array();
        $data['user_id'] = $_SESSION['user_id'];
        $data['work_title'] = I('post.work_title');
        $data['work_area'] = I('post.work_title');
        $data['job_category'] = I('post.work_title');
        $data['min_edu'] = I('post.work_title');
        $data['work_type'] = I('post.work_title');
        $data['work_num'] = I('post.work_title');
        $data['work_year'] = I('post.work_title');
        $data['work_describe'] = I('post.work_title');
        $data['work_pay'] = I('post.work_title');
        $data['create_time'] = time();
        $data['update_time'] = time();
        $WorkDao = M('Work');
        $result =  $WorkDao->add($data);
        if(!empty($result)){
            $this->ajaxReturn("Work","岗位保存成功",true);
        }else{
            $this->ajaxReturn("Work","岗位保存失败",false);
        }
    }

    //职位编辑
    public function edit(){
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Work","请先登录",false);
        }
        if($_SESSION['role'] !== 2 ){
            $this->ajaxReturn("Work","对不起，您没有权限进入该页面",false);
        }
        $map = array();
        $map['user_id'] = $_SESSION['user_id'];
        $map['work_id'] = I('post.work_id');
        $data = array();
        $data['work_title'] = I('post.work_title');
        $data['work_area'] = I('post.work_title');
        $data['job_category'] = I('post.work_title');
        $data['min_edu'] = I('post.work_title');
        $data['work_type'] = I('post.work_title');
        $data['work_num'] = I('post.work_title');
        $data['work_year'] = I('post.work_title');
        $data['work_describe'] = I('post.work_title');
        $data['work_pay'] = I('post.work_title');
        $data['create_time'] = time();
        $data['update_time'] = time();
        $WorkDao = M('Work');
        $result =  $WorkDao->where($map)->save($data);
        if(!empty($result)){
            $this->ajaxReturn("Work","岗位编辑成功",true);
        }else{
            $this->ajaxReturn("Work","岗位编辑失败",false);
        }
    }

    //岗位删除
    public function del(){
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Work","请先登录",false);
        }
        if($_SESSION['role'] !== 2 ){
            $this->ajaxReturn("Work","对不起，您没有权限进入该页面",false);
        }
        $map = array();
        $map['user_id'] = $_SESSION['user_id'];
        $map['work_id'] = I('post.work_id');
        $WorkDao = M('Work');
        $result = $WorkDao->where($map)->delete();
        if(!empty($result)){
            $this->ajaxReturn("Work","岗位删除成功",true);
        }else{
            $this->ajaxReturn("Work","岗位删除失败",false);
        }
    }

    //查看职位下求职者的列表
    public function seejob(){
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Work","请先登录",false);
        }
        if($_SESSION['role'] !== 2 ){
            $this->ajaxReturn("Work","对不起，您没有权限进入该页面",false);
        }
        $sEcho = 0;
        $map = array();
        $map['cb_submit.work_id'] = I('post.work_id');
        $SubmitDao = M('Submit');
        $list = $SubmitDao->where($map)->join('cb_users ON cb_submit.user_id = cb_users.user_id')->select();
        $count = $SubmitDao->where($map)->count();
        $this->dataTable($sEcho,$count,$list);
    }

    //查看应聘者详细信息
    public function seeUserinfo(){
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Work","请先登录",false);
        }
        if($_SESSION['role'] !== 2 ){
            $this->ajaxReturn("Work","对不起，您没有权限进入该页面",false);
        }
        $map = array();
        $map['cb_submit.work_id'] = I('post.work_id');
        $SubmitDao = M('Submit');
        $list = $SubmitDao->where($map)->join('cb_user on cb_submit.user_id = cb_user.user_id')->select();
        $this->assign('list',$list);
        $this->display();
    }

    //查看应聘者简历信息
    public function seeResumeinfo(){
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Work","请先登录",false);
        }
        if($_SESSION['role'] !== 2 ){
            $this->ajaxReturn("Work","对不起，您没有权限进入该页",false);
        }
        $map = array();
        $map['cb_submit.work_id'] = I('post.work_id');
        $SubmitDao = M('Submit');
        $list = $SubmitDao->where($map)->join('cb_user on cb_submit.resume_id = cb_user.resume_id')->select();
        $data = array();
        $data['is_see'] = 1;
        $result = M('Submit')->save($data);//查看简历
        $this->assign('list',$list);
        $this->display();
    }

}