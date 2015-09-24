<?php

#Load Configration For This Plugin
require_once dirname(__DIR__).'/libs/Google/autoload.php';
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Google_Login extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}
    
    public function get()
    {
        $config = new Controllers_Api_Google_Config_App;
        $client = new Google_Client();
        $client->setClientId($config->config['client_id']);
        $client->setClientSecret($config->config['client_secret']);
        $client->setRedirectUri($config->config['redirect_uri']);
        $client->addScope("email");
        $client->addScope("profile");
        
        $service = new Google_Service_Oauth2($client);
        
        $authUrl = $client->createAuthUrl();
 
        
        if (isset($authUrl)){ 
            //show login url
            
            if($this->input->get('test')){
                echo '<a href="' . $authUrl.'">Log in with Google!</a>';
            }else{
                echo json_encode(array('status'=>true,'url'=>$authUrl)); 
            }
        }else{ 
            echo json_encode(array('status'=>false));
        }
        
    }
    
    private function dependencies()
    {
        $config = new Controllers_Api_Google_Config_App;
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
