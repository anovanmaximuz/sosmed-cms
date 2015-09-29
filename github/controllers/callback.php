<?php

#Load Configration For This Plugin 
require_once dirname(__DIR__).'/config/app.php';

class Controllers_Api_Github_Callback extends Modules_Plugin_Base {
	
	public function __construct() {
		parent::__construct();
		$this->logger->write('debug', 'HTTP REQUEST: '.print_r($_REQUEST,1));
	}
	
    public function data()
    {

        $config = new Controllers_Api_Github_Config_App;
        $result = array();
        if(isset($_GET['code'])){
            $fields = array( 'client_id'=>$config->config['client_id'], 'client_secret'=>$config->config['client_secret'], 'code'=>$_GET['code']);
            $postvars = '';
            foreach($fields as $key=>$value) {
                $postvars .= $key . "=" . $value . "&";
            }
            $data = array('url' => 'https://github.com/login/oauth/access_token',
                  'data' => $postvars,
                  'header' => array("Content-Type: application/x-www-form-urlencoded","Accept: application/json"),
                  'method' => 'POST');
            $gitResponce = json_decode($this->curlRequest($data));
            
            if($gitResponce->access_token)
            {
                $data = array('url' => 'https://api.github.com/user?access_token='.$gitResponce->access_token,
                              'header' => array("Content-Type: application/x-www-form-urlencoded","User-Agent: ".appName,"Accept: application/json"),
                              'method' => 'GET');
                
                $gitUser = json_decode($this->curlRequest($data));
                
                $result['status']   = true;
                $result['user']     = $gitUser;                
            }
            else
            {
                $result = array('status'=>false,'error'=>'error, try again later');
            }
        }else{
            $result = array('status'=>false,'error'=>'error, try again later');
        }
        
        echo json_encode($result);
    }
    
    protected function curlRequest($data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if($data['header'] and is_array($data['header']))
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $data['header']);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if($data['method'] == 'POST' and !empty($data['data']))
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data['data']);
        }
        else
        {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }
        curl_setopt($ch, CURLOPT_URL, $data['url']);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
        curl_setopt($ch,CURLOPT_TIMEOUT, 20);
        
        $data = curl_exec($ch);
        return $data;
    }

}
