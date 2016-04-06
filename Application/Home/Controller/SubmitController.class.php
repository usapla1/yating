<?php
/**
 * Created by PhpStorm.
 * User: greatwall
 * Date: 2016/4/1
 * Time: 19:06
 */
namespace Home\Controller;
use Think\Controller;
use Think\Verify;
use Home\Common;
use Home\Controller\Base;

class SubmitController extends BaseController{
	//查看投递记录
	public function index()
	{
		if(empty($_SESSION['user_id'])){
			$this->ajaxReturn("Submit","请先登录",false);
		}
		if($_SESSION['role'] !== 1 ){
		$this->ajaxReturn("Submit","对不起，您没有权限进入该页面",false);
		}
		$map = array();
		$map['user_id'] = $_SESSION['user_id'];
		$SubmitDao = M('Submit');
		$list = $SubmitDao->where($map)->order('add_time')->select();
		$this->assign('list',$list);
		$this->display();
	}
}