<?php 

class Controllers_Api_Google_Config_App {

    // API connection
	public $config = array(
			'client_id'	     => '38162746172-3k383j4peinkplas038514s62nojhkhg.apps.googleusercontent.com',
			'client_secret'	 => 'fCqD4rvl1gBv1Fwsm3WWAqvH',
			'redirect_uri'	     => 'http://core.mainpmp.com/api/google/callback/data'
	);
    
    public $dependency = array(
                        'framework'=>array(
                                    'app_core'	=> 'lite360',
                                    'version'	=> '1.1'
                                    )
                        );

}