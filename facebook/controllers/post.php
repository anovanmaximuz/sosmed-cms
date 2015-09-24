<?php

#Load Configration For This Plugin
require_once dirname(__DIR__).'/libs/facebook/autoload.php';
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Facebook_Post extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}
	
    public function data()
    {
        $this->_mandatory( array('message','user_id','token') );
		$user_id    = $this->input->post('user_id');
		$token      = $this->input->post('token');
		$message    = $this->input->post('message');
        $config = new Controllers_Api_Facebook_Config_App;

        $fb = new Facebook\Facebook([
                          'app_id' => $config->config['app_id'],
                          'app_secret' => $config->config['app_secret'],
                          'default_graph_version' => $config->config['default_graph_version']
                          ]);

        $linkData = [
          'message' => $message
          ];
        $result = array();
        try {
          // Returns a `Facebook\FacebookResponse` object
          $response = $fb->post('/'.$user_id.'/feed', $linkData, $token);
          $graphNode = $response->getGraphNode();
          $result = array('status'=>true,'data'=>$graphNode['id']);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          $result = array('status'=>false,'data'=>$e->getMessage());
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          $result = array('status'=>false,'data'=>$e->getMessage());
        }

        echo json_encode($result);

    }
    
    public function login(){
        $config = new Controllers_Api_Facebook_Config_App;
        $this->_mandatory( array('redirect_uri') );
        $redirect_uri    = $this->input->post('redirect_uri');
        $fb = new Facebook\Facebook([
                          'app_id' => $config->config['app_id'],
                          'app_secret' => $config->config['app_secret'],
                          'default_graph_version' => $config->config['default_graph_version']
                          ]);
                          
        $fb = new Facebook\Facebook([
                          'app_id' => $config->config['app_id'],
                          'app_secret' => $config->config['app_secret'],
                          'default_graph_version' => $config->config['default_graph_version']
                          ]);
        $helper         = $fb->getRedirectLoginHelper();
        $permissions    = ['email', 'user_likes','publish_actions','public_profile','user_friends']; // optional
        $call_back      = $this->config->get('domain').'api/facebook/post/call_back';
        $loginUrl       = $helper->getLoginUrl($call_back, $permissions);
        echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
        echo json_encode(array('url'=>$loginUrl));
    
    }
    
    public function call_back(){
        $config = new Controllers_Api_Facebook_Config_App;

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
                                'data'=>array(
                                    'error'=>$helper->getError(),
                                    'code'=>$helper->getErrorCode(),
                                    'reason'=>$helper->getErrorReason(),
                                    'description'=>$helper->getErrorDescription()                            
                                    ));
              } else {
                $result = array('status'=>false,
                                'data'=>array(
                                    'error'=>'Bad request'                           
                                    ));
              }
            }else{
                $oAuth2Client = $fb->getOAuth2Client();

                // Get the access token metadata from /debug_token
                $tokenMetadata = $oAuth2Client->debugToken($accessToken);
                $result = array('status'=>true,
                                'data'=>array(
                                    'token'=>$accessToken->getValue(),
                                    'meta'=>json_decode($tokenMetadata)                                
                                    ));
                //session 2
                try {
                  // Returns a `Facebook\FacebookResponse` object
                  $response = $fb->get('/me?fields=id,name', $accessToken->getValue());
                  $user = $response->getGraphUser();
                  $result = array_merge($result,array('detail'=>$user['id']));
                } catch(Facebook\Exceptions\FacebookResponseException $e) {
                  $result = array_merge($result,array('detail'=>$e->getMessage()));  
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                  $result = array_merge($result,array('detail'=>$e->getMessage()));  
                }
            }
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          // When Graph returns an error
          $result = array('status'=>false,
                                'data'=>array(
                                    'error'=>$e->getMessage()                          
                                    ));
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          // When validation fails or other local issues
          $result = array('status'=>false,
                                'data'=>array(
                                    'error'=>$e->getMessage()                          
                                    ));
        }
        echo json_encode($result);
    }
    

}
