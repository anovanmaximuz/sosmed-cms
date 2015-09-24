<?php

#Load Configration For This Plugin
require_once dirname(__DIR__).'/libs/twitteroauth.php';
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Twitter_Post extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}
	   
    public function data()
    {
        $this->_mandatory( array('message') );
		$message    = $this->input->post('message');
        
        $config = new Controllers_Api_Twitter_Config_App;
        $connection = new TwitterOAuth($config->config['consumer_key'], $config->config['consumer_secret'], $_REQUEST['token_twitter'], $_REQUEST['token_secret_twitter']);

        //Post text to twitter
        $my_update = $connection->post('statuses/update', array('status' => $message));

        echo json_encode($my_update);

    }
    
}
