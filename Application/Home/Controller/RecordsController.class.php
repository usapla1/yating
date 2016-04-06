<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-03-30
 * Time: 22:47
 */
namespace Home\Controller;
use Think\Controller;
use Think\Verify;
use Home\Common;
use Home\Controller\Base;

class RecordsController extends BaseController{
    //��ְ�߲鿴����Ͷ�ݼ�¼
    public function index()
    {
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Resume","���ȵ�¼",false);
        }
        $map = array();
        $map['cb_submit.user_id'] = $_SESSION['user_id'];
        $Submit = M('Submit');
        $list =  $Submit->field('cb_work.title','cb_work.area','cb_work.money','cb_submit.add_time','cb_submit.is_see')->where($map)->join('cb_work ON cb_submit.work_id = cb_work.work_id')->select();
        $this->assign('list',$list);
        $this->display();
    }
}