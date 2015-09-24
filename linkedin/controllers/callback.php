<?php

#Load Configration For This Plugin 
require_once dirname(__DIR__).'/libs/linkedinoauth.php'; 
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Linkedin_Callback extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}
	
    public function data()
    {

        $config = new Controllers_Api_Linkedin_Config_App;
        $result = array();
        
        $linkedin   = new LinkedinOAuth(
                            $config->config['client_id'], 
                            $config->config['client_secret'], 
                            $this->config->get('domain').'api/linkedin/callback/data',
                            "r_basicprofile");
       
        if (isset($_GET['error'])) {
            // LinkedIn returned an error
            $result['status']   = false;
            $result['error']    = $_GET['error_description'];
        } elseif (isset($_GET['code'])) {
            // User authorized your application
            if ($_GET['state']) {
                // Get token so you can make API calls
                $result['status']    = true;
                $token_info          = $linkedin->getAccessToken($_GET['code']);
                $result['data']['token']     = $token_info;
                $user = $linkedin->fetch('GET', '/v1/people/~:(id,firstName,lastName,picture-url)','',$token_info->access_token);
                $result['data']['user']  = $user;
            } else {
                // CSRF attack? Or did you mix up your states?
                $result['status']   = false;
                $result['error']    = 'error state';
            }
        } else { 
            // Start authorization process
            
            $result['status']   = false;
            $result['error']    = 'token expired';
            $result['url']      = $linkedin->getAuthorizationCode();
        }
        
        echo json_encode($result);
    }

}
