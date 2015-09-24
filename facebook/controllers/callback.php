<?php

#Load Configration For This Plugin
require_once dirname(__DIR__).'/libs/facebook/autoload.php';
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Facebook_Callback extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}
	
    public function data(){
        $config         = new Controllers_Api_Facebook_Config_App;
        $fb = new Facebook\Facebook([
                          'app_id' => $config->config['app_id'],
                          'app_secret' => $config->config['app_secret'],
                          'default_graph_version' => $config->config['default_graph_version']
                          ]);
                          
        $helper = $fb->getRedirectLoginHelper();
        $result = array();
        try {
          $accessToken = $helper->getAccessToken();
            if (! isset($accessToken)) {
              if ($helper->getError()) {
                $result = array('status'=>false,
                                    'error'=>$helper->getError(),
                                    'code'=>$helper->getErrorCode(),
                                    'reason'=>$helper->getErrorReason(),
                                    'description'=>$helper->getErrorDescription()                            
                                    );
              } else {
                $result = array('status'=>false,
                                    'error'=>'Bad request'                           
                                    );
              }
            }else{
                $oAuth2Client = $fb->getOAuth2Client();

                // Get the access token metadata from /debug_token
                $tokenMetadata = $oAuth2Client->debugToken($accessToken);
                $expired       = $tokenMetadata->metadata['expires_at'];
                $issued        = $tokenMetadata->metadata['issued_at'];
                $result['status'] = true;
                $result['data']['token'] = $tokenMetadata;
                //session 2
                try {
                  // Returns a `Facebook\FacebookResponse` object
                  $response = $fb->get('/me?fields=id,name,email', $accessToken->getValue());
                  $user = json_decode($response->getGraphUser());
                  $result['data']['user'] = $user;
                } catch(Facebook\Exceptions\FacebookResponseException $e) {
                  $result['data']['user'] = $e->getMessage();  
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                  $result['data']['user'] = $e->getMessage();  
                }
            }
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          // When Graph returns an error
          $result = array('status'=>false,
                                
                                    'error'=>$e->getMessage()                          
                                    );
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          // When validation fails or other local issues
          $result = array('status'=>false,
                                
                                    'error'=>$e->getMessage()                          
                                    );
        }
        
        $result['social'] = 'facebook'; 
        //$redirect_uri    = $this->input->get('redirect_uri'); 
        //if(isset($_GET['code'])){
        //    header("Location: ".$redirect_uri."?".http_build_query($result, '', '&'));
       // }else{
            echo json_encode($result);
       // }
    }
    
/*     {
	"status": true,
	"token": "CAAVFxVWtypQBAGIOGoihIh19isJgGkycAZCUmdEwcaZApGLdjGZCmTIToOVfNbk1X0xZB8ssCxqKdO0mNsWNktmC3MIn96bZB7WOOJOfPhZBmg1p5CP4onnxF7CZArPOjQ1RjeT2ZCKKH1CBxfZAC7Rkl0hc2E9gYuZCda5rQZAQZCMBg3Q9ZAsqpVnQklEZB6ue93XOkZD",
	"meta": {
		"app_id": "1484088731880084",
		"application": "Quiz",
		"expires_at": {
			"date": "2015-11-22 15:04:48.000000",
			"timezone_type": 3,
			"timezone": "Asia\/Jakarta"
		},
		"is_valid": true,
		"issued_at": {
			"date": "2015-09-23 15:04:48.000000",
			"timezone_type": 3,
			"timezone": "Asia\/Jakarta"
		},
		"scopes": ["user_birthday",
		"user_location",
		"user_likes",
		"user_friends",
		"user_status",
		"user_posts",
		"read_stream",
		"email",
		"publish_actions",
		"public_profile"],
		"user_id": "1503252479957924"
	},
	"user_id": "1503252479957924"
} */

}
