<?php

#Load Configration For This Plugin
require_once dirname(__DIR__).'/libs/twitteroauth.php'; 
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Twitter_Login extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}
	
    public function get()
    {
        if($this->input->get('test')){ 
            echo '<a href="'.$this->config->get('domain').'api/twitter/callback/data">Login Using Twitter</a>';
        }else{
            echo json_encode(array('status'=>true,'url'=>$this->config->get('domain').'api/twitter/callback/data'));
        }
    }
    
    public function out(){ 
        session_start();
        unset($_SESSION['userdata']);
        session_destroy();
        header("Location:".$this->config->get('domain')."api/twitter/login/get");
    }
    
    private function dependencies()
    {
        $config = new Controllers_Api_Twitter_Config_App;
        $core_config = $this->config->get('core_info');
        
        if(isset($config->dependency['framework']['version'])){
            if($config->dependency['framework']['version']!=$core_config['framework']['version']){
                echo json_encode(array('code'=>-2000,'message'=>'This Plugin is not compatible for core system, recommended version '.$core_config['framework']['version']));die();
            }else{
                return true;
            }
        }else{
            echo json_encode(array('code'=>-2002,'message'=>'Missing plugin configuration'));die();
        }
    }
}
