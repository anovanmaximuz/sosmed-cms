<?php

#Load Configration For This Plugin
require_once dirname(__DIR__).'/libs/facebook/autoload.php';
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Facebook_Login extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}

    public function get(){
        $config = new Controllers_Api_Facebook_Config_App;
        $redirect_uri    = ($this->input->get('redirect_uri')) ? $this->input->get('redirect_uri') : '';
        $fb = new Facebook\Facebook([
                          'app_id' => $config->config['app_id'],
                          'app_secret' => $config->config['app_secret'],
                          'default_graph_version' => $config->config['default_graph_version']
                          ]);

        $helper         = $fb->getRedirectLoginHelper();
        $permissions    = ['email', 'user_likes','publish_actions','public_profile','user_friends']; // optional
        $call_back      = $this->config->get('domain').'api/facebook/callback/data';
        $loginUrl       = $helper->getLoginUrl($call_back, $permissions);
        
        if($this->input->get('test')){
            echo '<a href="' . $loginUrl.'">Log in with Facebook!</a>';
        }else{
            echo json_encode(array('status'=>true,'url'=>$loginUrl));
        }
        
    
    }
}
