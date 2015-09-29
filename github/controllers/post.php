<?php

#Load Configration For This Plugin
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Github_Post extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}
	   
    public function data()
    {
        $this->_mandatory( array('message') );
		$message                = $this->input->post('message');


        echo json_encode($my_update);

    }
    
}
