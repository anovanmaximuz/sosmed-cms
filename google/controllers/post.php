<?php

#Load Configration For This Plugin
require_once dirname(__DIR__).'/libs/Google/autoload.php';
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Google_Post extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}
	
    public function data()
    {
        //$this->dependencies();
        $this->_mandatory( array('message') );
		$message    = $this->input->post('message');

        $config = new Controllers_Api_Google_Config_App;
        
        
        echo json_encode($reply);

    }
    
    public function call_back()
    {
        $config = new Controllers_Api_Google_Config_App;
        $client = new Google_Client();
        $client->setClientId($config->config['client_id']);
        $client->setClientSecret($config->config['client_secret']);
        $client->setRedirectUri($config->config['redirect_uri']);
        $client->addScope("email");
        $client->addScope("profile");
        
        $service = new Google_Service_Oauth2($client);
        
        if (isset($_GET['code'])) {
          $client->authenticate($_GET['code']);
          $_SESSION['access_token'] = $client->getAccessToken();
          header('Location: ' . filter_var($config->config['redirect_uri'], FILTER_SANITIZE_URL));
          exit;
        }
        
        /************************************************
          If we have an access token, we can make
          requests, else we generate an authentication URL.
         ************************************************/
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
          $client->setAccessToken($_SESSION['access_token']);
        } else {
          $authUrl = $client->createAuthUrl();
        }
        
        if (isset($authUrl)){ 
            //show login url
            echo json_encode(array('status'=>false,'data'=>$authUrl)); 
        }else{ 
            $user = $service->userinfo->get(); //get user info 
            echo json_encode(array('status'=>true,'data'=>$user));
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
