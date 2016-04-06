<?php
namespace Home\Controller;
use Think\Controller;
use Home\Controller\Base;

class IndexController extends BaseController{
    //主页
    public function index(){
        $Users = M('users');
        $Works = M('work');
        $lists1 = $Users->select();
        $lists2 = $Works->select();
        $lists=array();
        $lists[] = $lists1;
        $lists[] = $lists2;
        $this->ajaxReturn($lists,0,array());
    }

    //投递简历
    public function submitResume(){
        if(empty($_SESSION['user_id'])){
            $this->ajaxReturn("Index","请先登录",false);
        }
        if($_SESSION['role'] !== 1 ){
            $this->ajaxReturn("Index","对不起，您不是求职者",false);
        }
        $map = array();
        $map['user_id'] = $_SESSION['user_id'];
        $ResumeDao = M('Resume');
        $result1 = $ResumeDao->where($map)->find();
        $countRe = $ResumeDao->where($map)->count();
        if($countRe>=2){
            $this->ajaxReturn("Index","你的简历过多，请联系管理员删除",false);
        }
        $data = array();
        $data['work_id'] = I('post.work_id');
        $data['user_id'] = $_SESSION['user_id'];
        $data['resume_id'] = $result1['resume_id'];
        $data['is_see'] = 0;
        $result2 = M('Submit')->add($data);//查看简历
        if(!empty($result2)){
            $this->ajaxReturn("Index","投递成功",true);
        }else{
            $this->ajaxReturn("Index","无法向该岗位投递简历",false);
        }
    }
    /**
     * 返回给 DataTable 的接口
     * @param int $sEcho
     * @param int $count
     * @param array $list
     */
    protected function dataTable($sEcho,$count,$list,$extra=''){
        $data = array();
        $data['sEcho'] = intval($sEcho);
        $data['iTotalRecords'] = intval($count);
        $data['iTotalDisplayRecords'] = intval($count);
        if(!empty($list)){
            $data['Data'] = $list;
        }else{
            $data['Data'] = array();
        }
        $data['extra'] = trim($extra);
        echo json_encode($data);exit;
    }

    /**
     * 简易输出
     * @param string/array $data
     */
    private function output($data){
        header("Content-Type:text/html; charset=utf-8");
        if(is_array($data)){
            echo json_encode($data);exit;
        }else{
            echo $data;exit;
        }
    }
}