<?php 

class Controllers_Api_Facebook_Config_App {

    // API connection
	public $config = array(
			'app_id'	     => '1484088731880084',
			'app_secret'	 => '45e8bb34f26903954b47460bcf8e9787',
			'default_graph_version'	 => 'v2.2'
	);
    
    public $dependency = array(
                        'framework'=>array(
                                    'app_core'	=> 'lite360',
                                    'version'	=> '1.1'
                                    )
                        );

}