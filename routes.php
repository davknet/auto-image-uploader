<?php 




add_action( 'rest_api_init', function () {
    
    	$namespace = "api-extend";	
	
    
    	register_rest_route( $namespace, '/auto-image-upload', array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'=>'autoImageUploader',
				// 'permission_callback' => function( WP_REST_Request $request ) {
    //                       return merchanIdentifyApiPermmision($request);                  
    //             },
            	'permission_callback' => '__return_true',	    
				'args'                      => array(
					'format'                => array(
						'sanitize_callback' => 'sanitize_key',
						)
					),
	) );
    
    
    
});