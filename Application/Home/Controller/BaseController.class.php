<?php

/**
 *
 */
namespace Home\Controller;

use Think\Controller;
use Org\Rbac;

class BaseController extends Controller {

    protected function checkAjaxAccess($tree_id){
        if(!$this->checkAccess($tree_id)){
            $this->ajaxReturn("no_authority:".MODULE_NAME."->".ACTION_NAME."没有权限");
        }
    }

    private function checkAccess($tree_id){
        if(!is_array($tree_id)){
            $tree_id = array($tree_id);
        }
        if(Rbac::checkTreeListAllowed($_SESSION[C('USER_AUTH_LOGIN')]['admin_id'],$tree_id)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获得用户所有可访问的tree_id
     * @return array
     */
    protected function getUserAccessTree(){
        import("Rbac",APP_PATH.'/Lib/ORG');
        $tree_list = Rbac::getUserCacheTreeList($_SESSION[C('USER_AUTH_LOGIN')]['admin_id']);
        return $tree_list;
    }

    /**
     * 判断是否是超级管理员
     * @return boolean
     */
    protected function isAdmin(){
        session_start();
        if(1 == $_SESSION[C('USER_AUTH_LOGIN')]['is_admin']){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 实时获得可管理的网吧列表
     * @return array
     */
    protected function getNetbarList(){
        $chain_id = $_SESSION[C('USER_AUTH_LOGIN')]['chain_id'];
        $user_id  = $_SESSION[C('USER_AUTH_LOGIN')]['admin_id'];

        if(!$this->isAdmin()){//子账号加限制
            $netbars = array();
            $map = array();
            $map['user_id'] = $user_id;
            $BaseUserNetbarDao = M('BaseUserNetbar');
            $rows = $BaseUserNetbarDao->field("netbar_id")->where($map)->select();
            foreach($rows as $row){
                $netbars[] = $row['netbar_id'];
            }
            if(!empty($netbars)){
                $netbar_limit = array("IN",$netbars);
            }else{
                $netbar_limit = -1;//没有任务网吧可以管理
            }
        }else{//管理员
        }

        $netbar_list = array();
        $map = array();
        $map['chain_id'] = $chain_id;
        $map['del_flag'] = 0;
        if(!empty($netbar_limit)){
            $map['netbar_id'] = $netbar_limit;
        }
        $NetbarDao = D("Netbar");
        $netbars = $NetbarDao->field("netbar_id")->where($map)->order("convert(netbar_name USING gbk) COLLATE gbk_chinese_ci asc")->select();
        foreach($netbars as $netbar){
            $netbar_list[] = $netbar['netbar_id'];
        }
        return $netbar_list;
    }

    /**
     * 检查网吧是否可以访问
     * @param int $netbar_id
     * @return boolean
     */
    protected function checkNetbarSelected($netbar_id){
        $chain_id = $_SESSION[C('USER_AUTH_LOGIN')]['chain_id'];
        $user_id  = $_SESSION[C('USER_AUTH_LOGIN')]['admin_id'];
        if(!$this->isAdmin()){//子账号加限制
            $map = array();
            $map['user_id']   = $user_id;
            $map['netbar_id'] = $netbar_id;
            $BaseUserNetbarDao = M('BaseUserNetbar');
            $row = $BaseUserNetbarDao->field("netbar_id")->where($map)->find();
            if(empty($row)){
                return -1;//没有该网吧的管理权限
            }
        }else{//管理员
            return 1;
        }
    }

    /**
     * 网吧下拉列表
     */
    protected function getNetbarSelect(){
        session_start();
        $chain_id = $_SESSION[C('USER_AUTH_LOGIN')]['chain_id'];
        $netbar_list = $this->getNetbarList();

        $map = array();
        $map['chain_id'] = $chain_id;
        $map['del_flag'] = 0;
        if(!empty($netbar_list)){
            $map['netbar_id'] = array("IN",$netbar_list);
        }else{
            $map['netbar_id'] = -1;//没有任务网吧可以管理
        }
        $NetbarDao = D("Netbar");
        $rows = $NetbarDao
        ->field("netbar_id,netbar_number,netbar_name,trail,trail_time,agent_id,agent_bind_status,valid_time")
        ->where($map)
        ->table("cb_netbar")
        ->order("convert(netbar_name USING gbk) COLLATE gbk_chinese_ci asc")
        ->select();//按拼音首字母排序
        $list = array();
        if(!empty($rows)){
            foreach($rows as $v){
                list($vtype_id,$vtype_str,$expire_time) = getNetbarVType($v['trail'],$v['trail_time'],$v['agent_id'],$v['agent_bind_status'],$v['valid_time']);
                $t = array();
                $t['netbar_id']     = $v['netbar_id'];
                $t['netbar_number'] = $v['netbar_number'];
                $t['netbar_name']   = $v['netbar_name'];
                $t['vtype']         = $vtype_id;
                $t['is_selected']   = ($v['netbar_id']==$_SESSION[C('USER_AUTH_LOGIN')]['netbar_id']) ? true : false;
                $list[] = $t;
            }
        }
        return $list;
    }

    /**
     * 返回给 DataTable 的接口
     * @param int $sEcho
     * @param int $count
     * @param array $list
     */
    protected function dataTable($sEcho,$count,$list,$total){
        $data = array();
        $data['sEcho'] = intval($sEcho);
        $data['iTotalRecords'] = intval($count);
        $data['iTotalDisplayRecords'] = intval($count);
        if(!empty($list)){
            $data['aaData'] = $list;
        }else{
            $data['aaData'] = array();
        }
        $data['total'] = intval($total);
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


    /**
     * 返回给 url 的接口
     * @param array $url
     */
    protected function sendUrl($url){
        $data = array();
        $data['url'] = $url;
        echo json_encode($data);
    }

    protected function checkDTAccess($tree_id){
        if(!$this->checkAccess($tree_id)){
            //$this->ajaxReturns(array('sError'=>"没有权限",'data'=>"no_authority:".MODULE_NAME."->".ACTION_NAME));
        }
    }

    /**
     * 检查网吧ID
     * @param int $netbar_id
     */
    protected function checkNetbarID($netbar_id){
        if(0==$netbar_id){
            $this->ajaxReturn(array(),"没有选择网吧",false);
        }else if(-1==$netbar_id){
            $this->ajaxReturn(array(),"没有该网吧的管理权限",false);
        }else if(-2==$netbar_id){

            $this->ajaxReturn(array(),"该网吧版本需要升级",false);
        }else if(-3==$netbar_id){
            $this->ajaxReturn(array(),"该网吧已删除",false);
        }else if(-4==$netbar_id){
            $this->output(array('status'=>false,'data'=>"no_vip",'info'=>"VIP功能未开通",'url'=>C("WEB_YUN")."Login/index/?menu=&session=".session_id()));
        }else{
            //
        }
    }

    /**
     * 上传临时图片
     * @return array
     */
     protected function uploadTmpImage(){
        if(empty($_FILES)){
            return array(false,"请选择图片");
        }
        $saveTime = date('Ymd/');
        $config=array('rootPath'=>C('UPLOAD_PATH'));
        $upload = new \Think\Upload($config);// 实例化上传类
        $upload->maxSize   =     2000000 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','bmp');// 设置附件上传类型
        $upload->savePath  =     "tmp/"; // 设置附件上传目录
        $info   =   array_values($upload->upload());
        if(!$info) {// 上传错误提示错误信息
            $result = $upload->getError();
            return $result;
        }else{
             $info[0]['path'] = C('UPLOAD_DIR').$info[0]['savepath'].$info[0]['savename'];//预览图片的完整路径
             $info[0]['relativepath'] = $info[0]['savepath'].$info[0]['savename'];//素材相对于网站的路径
             $info[0]['filename'] = C('UPLOAD_PATH').$info[0]['savepath'].$info[0]['savename'];
            return array(true,$info);
        }
    }

    /**
     * 上传临时html文件
     * @return array
     */
    protected function uploadTmpHtml(){
        if(empty($_FILES)){
            return array(false,"请选择自定义网页");
        }
        $saveTime = date('Ymd/');
        $config=array('rootPath'=>C('WEBPAGE_PATH'));
        $upload = new \Think\Upload($config);// 实例化上传类
        $upload->maxSize   =     2000000 ;// 设置附件上传大小
        $upload->exts      =     array('html', 'htm');// 设置附件上传类型
        $upload->savePath  =     "tmp/"; // 设置附件上传目录
        $info   =   array_values($upload->upload());
        if(!$info) {// 上传错误提示错误信息
            $result = $upload->getError();
            return $result;
        }else{
            $info[0]['path'] = C('UPLOAD_DIR').$info[0]['savepath'].$info[0]['savename'];//预览图片的完整路径
            $info[0]['relativepath'] = $info[0]['savepath'].$info[0]['savename'];//素材相对于网站的路径
            $info[0]['filename'] = C('UPLOAD_PATH').$info[0]['savepath'].$info[0]['savename'];
            return array(true,$info);
        }
    }

    /**
     * 上传临时语音文件
     * @return array
     */
    protected function uploadTmpVoice(){
        if(empty($_FILES)){
            return array(false,"请选择语音文件");
        }
        $saveTime = date('Ymd/');
        $config=array('rootPath'=>C('UPLOAD_PATH'));
        $upload = new \Think\Upload($config);// 实例化上传类
        $upload->maxSize   =     5000000 ;// 设置附件上传大小
        $upload->exts      =     array('mp3', 'wma', 'wav', 'amr');// 设置附件上传类型
        $upload->savePath  =     "tmp/"; // 设置附件上传目录
        $info   =   array_values($upload->upload());
        if(!$info) {// 上传错误提示错误信息
            //return $this->error($upload->getError());
        }else{
             $info[0]['path'] = C('UPLOAD_DIR').$info[0]['savepath'].$info[0]['savename'];//预览图片的完整路径
             $info[0]['relativepath'] = $info[0]['savepath'].$info[0]['savename'];//素材相对于网站的路径
             $info[0]['filename'] = C('UPLOAD_PATH').$info[0]['savepath'].$info[0]['savename'];
             $info[0]['key'] = 'voice';
            return array(true,$info);
        }
    }

    /**
     * 判断文件是否合法
     * @param string $file
     * @return boolean
     */
    private function isAllowdFile($file){
        $rows = explode(".",$file);
        $ext = array_pop($rows);
        $ext = strtolower($ext);
        return in_array($ext, array('jpg', 'gif', 'png', 'jpeg'));
    }

    /**
     * 关联图片，按模块没有大小图
     * @param string $file_src
     * @param string $file_dir_name
     * @return boolean/string
     */
    protected function linkImage($file_src,$file_dir_name="message"){
        if(empty($file_src)){
            return false;
        }
        if(!file_exists($file_src)){
            return false;
        }
        if(!$this->isAllowdFile($file_src)){
            return false;
        }

        load("@.function");
        $upload_path = C('UPLOAD_PATH') ? C('UPLOAD_PATH') : "./Public/upload/";
        $file_name   = basename($file_src);
        $file_path   = date('Ymd/');
        $url_path    = $file_dir_name."/".$file_path;
        $save_path   = $upload_path.$url_path;
        $file_dst    = $save_path.$file_name;

        if(!mk_dir($save_path)){
            return false;
        }
        if(!copy($file_src,$file_dst)){
            return false;
        }

        Vendor("AliOSS.AliOSS");
        $AliOSS = new \AliOSS();
        $result = $AliOSS->uploadFile($url_path.$file_name, $file_dst);
        if(!$result){
            if(file_exists($file_dst)){
                unlink($file_dst);
            }
            return false;
        }

        $save_image = $file_path.$file_name;
        return $save_image;
    }

    /**
     * 取消图片关联
     * @param string $file_old
     */
    protected function unlinkImage($file_old,$file_dir_name="message"){
        if(!empty($file_old)){
            $upload_path = C('UPLOAD_PATH') ? C('UPLOAD_PATH') : "./Public/upload/";
            $file = $upload_path.$file_dir_name.'/'.$file_old;
            if(file_exists($file)){
                unlink($file);
            }
            Vendor("AliOSS.AliOSS");
            $AliOSS = new \AliOSS();
            $AliOSS->deleteFile($file_dir_name.'/'.$file_old);
        }
    }

    /**
     * 直接保存到OSS上
     * @return bool,url/error
     */
    protected function uploadOSS(){
        $upload_path = C('UPLOAD_PATH') ? C('UPLOAD_PATH') : "./Public/";
        $saveTime = date('Ymd/');
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->savePath  = $upload_path.'tmp/'.$saveTime;
        $upload->saveRule  = "getUploadImgName";
        if(!mk_dir($upload->savePath)){
            return array(false,"create path error");
        }
        if(!$upload->upload()){
            return array(false,"upload fail");
        }

        $info = $upload->getUploadFileInfo();
        $tmp_file = $info[0]['savepath'].$info[0]['savename'];
        $oss_file = "notice/".$saveTime.$info[0]['savename'];

        Vendor("AliOSS.AliOSS");
        $AliOSS = new \AliOSS();
        $result = $AliOSS->uploadFile($oss_file, $tmp_file);
        if(!$result){
            if(file_exists($tmp_file)){
                unlink($tmp_file);
            }
            return array(false,"upload oss fail");
        }

        return array(true,$oss_file);
    }

    /**
     * 直接保存到OSS上
     * @return bool,url/error
     */
    protected function uploadVoiceOSS(){
        if(empty($_FILES)){
            return array(false,"请选择语音文件");
        }
        $upload_path = C('UPLOAD_PATH') ? C('UPLOAD_PATH') : "./Public/";
        $saveTime = date('Ymd/');
        $config=array('rootPath'=>C('UPLOAD_PATH'));
        $upload = new \Think\Upload($config);// 实例化上传类
        $upload->maxSize   =     5000000 ;// 设置附件上传大小
        $upload->exts      =     array('mp3', 'wma', 'wav', 'amr');// 设置附件上传类型
        $upload->savePath  =     'tmpvoice/'; // 设置附件上传目录
        $info   =   array_values($upload->upload());

        if(!$info) {// 上传错误提示错误信息
            //return $this->error($upload->getError());
        }else{
            $tmp_file = $upload_path.$info[0]['savepath'].$info[0]['savename'];
            $oss_file = $saveTime.$info[0]['savename'];
        }
        Vendor("AliOSS.AliOSS");
        $AliOSS = new \AliOSS();
        $result = $AliOSS->uploadFile($oss_file,$tmp_file);

        if(!$result){
            if(file_exists($tmp_file)){
                unlink($tmp_file);
            }
            return array(false,"upload oss fail");
        }
        $info[0]['path'] = C('UPLOAD_DIR').$info[0]['savepath'].$info[0]['savename'];//预览图片的完整路径
        $info[0]['relativepath'] = $info[0]['savepath'].$info[0]['savename'];//素材相对于网站的路径
        $info[0]['filename'] = C('UPLOAD_PATH').$info[0]['savepath'].$info[0]['savename'];
        $info[0]['key'] = 'voice';
        $info[0]['url'] = C("OSS_PATH").$oss_file;

        return array(true,$info);
    }

    /**
     * 切换网吧
     * 当前选中的 netbar_id 保存在 session.netbar_id
     */
    protected function selectCurNetbar($netbar_id,$isChain){
        if(-99 == $netbar_id){//切换连锁奖品库
            if($isChain){
                $_SESSION[C('USER_AUTH_LOGIN')]['netbar_id'] = $netbar_id;
            }else{
                return 0;
            }
        }else if("" != $netbar_id){//切换网吧
            $result = $this->checkNetbarSelected($netbar_id);
            if(1==$result){
                $_SESSION[C('USER_AUTH_LOGIN')]['netbar_id'] = $netbar_id;
                import("Rbac",APP_PATH.'/Lib/ORG');
                $isVIP = Rbac::checkVIP($netbar_id);
                if(!$isVIP){
                    return -4;
                }
            }else{
                return $result;
            }
        }else if(!empty($_SESSION[C('USER_AUTH_LOGIN')]['netbar_id'])){//当前默认网吧
            if(!$isChain && -99==$_SESSION[C('USER_AUTH_LOGIN')]['netbar_id']){
                return 0;
            }else{
                $netbar_id = $_SESSION[C('USER_AUTH_LOGIN')]['netbar_id'];
                import("Rbac",APP_PATH.'/Lib/ORG');
                $isVIP = Rbac::checkVIP($netbar_id);
                if(!$isVIP){
                    return -4;
                }
            }
        }else{
            return 0;
        }
        return $netbar_id;
    }

    /**
     * 检查网吧ID
     * @param int $netbar_id
     */
    protected function checkDisplayNetbarID($netbar_id){
        header("Content-Type:text/html; charset=utf-8");
        if(0==$netbar_id){
            echo "没有选择网吧";exit;
        }else if(-1==$netbar_id){
            echo "没有该网吧的管理权限";exit;
        }else if(-2==$netbar_id){
            echo "该网吧版本需要升级";exit;
        }else if(-3==$netbar_id){
            echo "该网吧已删除";exit;
        }else if(-4==$netbar_id){
            echo "VIP功能未开通";exit;
        }else{
            //
        }
    }

    /**
     * 配置的保存
     * @param int $netbar_id
     * @param string $config_key
     * @param string $value
     * @return int
     */
     protected function saveWeiXinConfig($netbar_id,$config_key,$value){
        $ConfigDao = M('Config');
        $map = array();
        $map['netbar_id']  = $netbar_id;
        $map['config_key'] = $config_key;
        $result = $ConfigDao->field("config_id")->where($map)->select();
        $now_time = time();
        if(empty($result)){
            $data = array();
            $data['netbar_id']    = $netbar_id;
            $data['config_key']   = $config_key;
            $data['config_value'] = $value;
            $data['update_time']  = $now_time;
            $result = $ConfigDao->add($data);
        }else{
            $map = array();
            $map['config_id'] = $result['config_id'];
            $data = array();
            $data['config_value'] = $value;
            $data['update_time']  = $now_time;
            $result = $ConfigDao->where($map)->save($data);
        }
        return $result;
    }

    protected function get_client_ip(){
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = "unknown";
        return($ip);
    }

    protected function check_verify(){
        return false;
    }
}