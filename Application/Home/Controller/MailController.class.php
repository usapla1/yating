<?php
/**
 * Created by PhpStorm.
 * User: greatwall
 * Date: 2016/3/31
 * Time: 17:43
 */
namespace Home\Controller;
use Think\Controller;
use Home\Common;
use Home\Controller\Base;

class MailController extends Controller{
	//企业发送过的邮件主页
	public function index(){
		if(empty($_SESSION['user_id'])){
		$this->ajaxReturn("Mail","请先登录",false);
		}
		if($_SESSION['role'] !== 2 ){
			$this->ajaxReturn("Work","对不起，您没有权限进入该页面",false);
		}
		$MailsmsDao = M('Mailsms');
		$list = $MailsmsDao->join('cb_users ON cb_mailsms.user_id = cb_users.user_id')->order('add_time')->select();
		$this->assign('list',$list);
		$this->display();
	}

	//个人受到的邮件记录
	public function userMail(){
		if(empty($_SESSION['user_id'])){
			$this->ajaxReturn("Mail","请先登录",false);
		}
		if($_SESSION['role'] !== 1 ){
			$this->ajaxReturn("Mail","对不起，您没有权限进入该页面",false);
		}
		$map = array();
		$map['user_id'] = $_SESSION['user_id'];
		$MailsmsDao = M('Mailsms');
		$list = $MailsmsDao->join('cb_users ON cb_mailsms.user_id = cb_users.user_id')->where($map)->order('add_time')->select();
		$this->assign('list',$list);
		$this->display();
	}
	//发送邮件
	public function add(){
		$data = array();
		$data['user_id'] = I('post.user_id');
		$data['user_id'] = I('post.work_title');
		$data['work_id'] = I('post.work_id');
		$data['mail'] = I('post.mail');
		$data['title'] = I('post.title');
		$data['content'] = I('post.content');
		$data['add_time'] = time();
		$MailsmsDao = M('Mailsms');
		$result = $MailsmsDao->add($data);

		if(!empty($result) && SendMail($_POST['mail'],$_POST['title'],$_POST['content']))
			$this->success('发送成功！');
		else
			$this->error('发送失败');
	}
}