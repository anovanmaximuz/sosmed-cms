<?php

#Load Configration For This Plugin
require_once dirname(__DIR__).'/libs/linkedinoauth.php'; 
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Linkedin_Login extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}
	
    public function get()
    {
        $config     = new Controllers_Api_Linkedin_Config_App;
        $linkedin   = new LinkedinOAuth(
                            $config->config['client_id'], 
                            $config->config['client_secret'], 
                            $this->config->get('domain').'api/linkedin/callback/data',
                            "r_basicprofile");
               
        if($this->input->get('test')){
            echo '<a href="'.$linkedin->getAuthorizationCode().'">Log in with Linkedin!</a>';
        }else{
            echo json_encode(array('status'=>true,'url'=>$linkedin->getAuthorizationCode()));
        }

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
