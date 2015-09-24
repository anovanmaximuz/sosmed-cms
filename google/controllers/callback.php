<?php

#Load Configration For This Plugin
require_once dirname(__DIR__).'/libs/Google/autoload.php';
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Google_Callback extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}

    public function data()
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
          //$_SESSION['access_token'] = $client->getAccessToken();
          $client->setAccessToken($client->getAccessToken());
          $user         = $service->userinfo->get();
          $token_info   = json_decode($client->getAccessToken());

          $result = array('status'=>true,
                          'token'=>$token_info->access_token,
                          'expired'=>date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s', $token_info->created))+$token_info->expires_in),
                          'issued'=>date('Y-m-d H:i:s',$token_info->created),
                          'user_id'=>$user['id'],
                          'email'=>$user['email'],
                          'picture'=>$user['picture']
                        );
        }else{
            $result = array('status'=>false,
                                    'error'=>'failed'                          
                                    );
        }
        
        $result['social'] = 'google'; 
        echo json_encode($result);
        
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
