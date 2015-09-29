<?php 

class Controllers_Api_Github_Config_App {

    // API connection
	public $config = array(
			'client_id'	     => 'f8787a17c654e62aa27b',
			'client_secret'	 => '4a714716be6f3f73b4c169158a970569e3ae1e2a',
			'app_name'	 => 'Pasar Kode'
	);
    
    public $dependency = array(
                        'framework'=>array(
                                    'app_core'	=> 'lite360',
                                    'version'	=> '1.1'
                                    )
                        );

}