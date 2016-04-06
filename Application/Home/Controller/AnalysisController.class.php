<?php
/**
 * Created by PhpStorm.
 * User: greatwall
 * Date: 2016/4/1
 * Time: 18:24
 */
namespace Home\Controller;
use Think\Controller;
use Think\Verify;
use Home\Common;
use Home\Controller\Base;

class AnalysisController extends BaseController{
	//企业岗位表首页
	public function index(){
		if (empty($_SESSION['user_id'])) {
			$this->ajaxReturn("Work", "请先登录", false);
		}
		if ($_SESSION['role'] !== 2) {
			$this->ajaxReturn("Work", "对不起，您没有权限进入该页面", false);
		}

		$ResumeDao = M('Resume');
		$count_man = $ResumeDao->where('sex = 1')->count();
		$count_women = $ResumeDao->where('sex = 0')->count();
		$count_15 = $ResumeDao->where('age>10 AND age < 20')->count();
		$count_25 = $ResumeDao->where('age>20 AND age < 30')->count();
		$count_edu = $ResumeDao->where('edu = 本科')->count();
		$this->assign('count_man', $count_man);
		$this->assign('count_women', $count_women);
		$this->assign('count_15', $count_15);
		$this->assign('count_25', $count_25);
		$this->assign('count_edu', $count_edu);
		$this->display();
	}
}