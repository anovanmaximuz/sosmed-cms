<?php

#Load Configration For This Plugin 
require_once dirname(__DIR__).'/libs/twitteroauth.php';
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Twitter_Callback extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}
	
    public function data()
    {

        $config = new Controllers_Api_Twitter_Config_App;
        $result = array();
        
        if(isset($_REQUEST['oauth_token'])) {

            //Successful response returns oauth_token, oauth_token_secret, user_id, and screen_name
            $connection = new TwitterOAuth($config->config['consumer_key'], $config->config['consumer_secret'], $_SESSION['token_twitter'], $_SESSION['token_secret_twitter']);
            $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
            if($connection->http_code == '200')
            {
                //Redirect user to twitter
                $result['status']   = true;
                $result['data']['oauth_token']          = $access_token['oauth_token'];
                $result['data']['oauth_token_secret']   = $access_token['oauth_token_secret'];
                $result['data']['oauth_verifier']       = $_REQUEST['oauth_verifier'];

                //Insert user into the database
                $user_info = $connection->get('account/verify_credentials'); 
                
                $result['data']['user']  = $user_info;
                $result['data']['social']   = 'twitter';

            }else{
                $result = array('status'=>false,'error'=>'error, try again later');
            }
                
        }else{
        
            if(isset($_GET["denied"]))
            {
                $result = array('status'=>false,'error'=>'denied');
            }else{

                //Fresh authentication
                $connection = new TwitterOAuth($config->config['consumer_key'], $config->config['consumer_secret']);
                $request_token = $connection->getRequestToken($this->config->get('domain').'api/twitter/callback/data');
                
                //Received token info from twitter
                $_SESSION['token_twitter'] 			= $request_token['oauth_token'];
                $_SESSION['token_secret_twitter'] 	= $request_token['oauth_token_secret'];
                
                //Any value other than 200 is failure, so continue only if http code is 200
                if($connection->http_code == '200')
                {
                    //redirect user to twitter
                    $twitter_url = $connection->getAuthorizeURL($request_token['oauth_token']);
                    $result = array('status'=>true,
                                    'url'=>$twitter_url,
                                    'token'=>$request_token['oauth_token'],
                                    'token_secret'=>$request_token['oauth_token_secret']);
                    header('Location: ' . $twitter_url); 
                }else{
                    $result = array('status'=>false,'error'=>'error connecting to twitter! try again later!');
                    //die("error connecting to twitter! try again later!");
                }
            }
        }
        
        echo json_encode($result);
    }

}
