<?php

class LinkedinOAuth {

    public $client_id;
    public $client_secret;
    public $redirect_uri;
    public $scope;
    
    function __construct($client_id, $client_secret, $redirect_uri,$scope="r_basicprofile") {
        $this->client_id        = $client_id;
        $this->client_secret    = $client_secret;
        $this->redirect_uri     = $redirect_uri;
        $this->scope            = $scope;
    }
    
    
    public function getAuthorizationCode() {
        $params = array(
            'response_type' => 'code',
            'client_id' => $this->client_id,
            'scope' => $this->scope,
            'state' => uniqid('', true), // unique long string
            'redirect_uri' => $this->redirect_uri,
        );
     
        // Authentication request
        $url = 'https://www.linkedin.com/uas/oauth2/authorization?' . http_build_query($params);
         
        // Needed to identify request when it returns to us
        $_SESSION['state'] = $params['state'];
     
        // Redirect user to authenticate
        return $url;
    }
    
    public function getAccessToken($code) {
        $params = array(
            'grant_type' => 'authorization_code',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'code' => $code,
            'redirect_uri' => $this->redirect_uri,
        );
         
        // Access Token request
        $url = 'https://www.linkedin.com/uas/oauth2/accessToken?' . http_build_query($params);
         
        // Tell streams to make a POST request
        $context = stream_context_create(
            array('http' => 
                array('method' => 'POST',
                )
            )
        );
     
        // Retrieve access token information
        $response = file_get_contents($url, false, $context);
     
        // Native PHP object, please
        $token = json_decode($response);
         
        return $token;
    }
    
    public function fetch($method, $resource, $body = '',$access_token) {
        $opts = array(
            'http'=>array(
                'method' => $method,
                'header' => "Authorization: Bearer " . $access_token . "\r\n" . "x-li-format: json\r\n"
            )
        );
     
        // Need to use HTTPS
        $url = 'https://api.linkedin.com' . $resource;
     
        // Append query parameters (if there are any)
        //if (count($params)) { $url .= '?' . http_build_query($params); }
     
        // Tell streams to make a (GET, POST, PUT, or DELETE) request
        // And use OAuth 2 access token as Authorization
        $context = stream_context_create($opts);
     
        // Hocus Pocus
        $response = file_get_contents($url, false, $context);
     
        // Native PHP object, please
        return json_decode($response);
    }

}

     

 
