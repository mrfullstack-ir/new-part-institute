<?php

class TestAPI {
	function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( 'namespace/v1', '/test/', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'api_callback' ),
			) );
		} );
	}
	function api_callback( WP_REST_Request $request ) {
		return get_post_meta(95 , "count_like" , true);
	}

}

new TestAPI();