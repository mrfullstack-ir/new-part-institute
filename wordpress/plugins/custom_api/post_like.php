<?php

class LikeAPI {
	function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( 'namespace/v1', '/like/', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'api_callback' ),
			) );
		} );
	}

	private $redis_database;
	private $index_database = 0;
	private $redis_address = "127.0.0.1";
	private $redis_port = 6379;

	private $now_post_liked = 0;

	function api_callback( WP_REST_Request $request ) {
		$status_code = 201;

		// Check Parameter (POST ID) is Exist and Set !
		if ( ! isset( ( $request->get_query_params() )["post"] ) && empty( ( $request->get_query_params() )["post"] ) ) {
			$status_code = 400;
		}
		// Check Type of Parameter (POST ID)
		if ( ! is_numeric( (int) ( $request->get_query_params() )["post"] ) ) {
			$status_code = 400;
		}
		// Set In Var for Clean Code.
		$post_id = (int) ( $request->get_query_params() )["post"];


		// Connect to Redis Database & Check Connection
		if ( $this->connect_to_redis() ) {

			// Getting Value By Key : UserAgent + IP Address
			$key          = ( $_SERVER['HTTP_USER_AGENT'] . "+" . $_SERVER['REMOTE_ADDR'] );
			$value_in_key = $this->redis_database->get( $key );

			// Check Result
			if ( $value_in_key ) {

				// Convert From JSON To Array
				$post_liked = json_decode( $value_in_key );

				// Check IF User Liked This Post
				if ( in_array( $post_id, $post_liked ) ) {
					// Return Status Code
					$status_code = 400;
				} else {

					$post_liked[] = $post_id;
					$value_must_set = json_encode( $post_liked );

					if ( ! $this->redis_database->set( $key, $value_must_set ) ) {
						// Return Error to Request
						$status_code = 500;
					}

					$this->now_post_liked = $post_id;

					if ( ! $this->save_post_like() ) {
						$status_code = 400;
					}

					$this->now_post_liked = 0;

				}

			} else {

				// IF User is Newcomer , Set (UserAgent + IP Address) in Key and JSON Array That Have POST ID in Value
				$value_must_set = json_encode( array( $post_id ) );
				if ( ! $this->redis_database->set( $key, $value_must_set ) ) {
					// Return Status Code
					$status_code = 500;
				}
			}

		} else {
			// Return Status Code
			$status_code = 500;
		}

		$this->disconnect_to_redis();

		// Response to Request === $data
		wp_send_json( 0, $status_code );
	}


	/*
	 * connect_to_redis = this function for Establish Connection to Redis Server
	 */
	function connect_to_redis(): bool {
		try {

			$this->redis_database = new Redis();
			$this->redis_database->connect( $this->redis_address, $this->redis_port );
			$this->redis_database->select( $this->index_database );

			if ( ! $this->redis_database->ping() ) {
				return false;
			}

			return true;
		} catch ( Exception $error ) {
			return false;
		}
	}

	/*
    * disconnect_to_redis = this function for Break Redis Server Connection
    */
	function disconnect_to_redis(): bool {
		try {
			$this->redis_database->close();

			return true;
		} catch ( Exception $error ) {
			return false;
		}
	}

	/*
	 * add_like_to_post = this function for Adding One Like to POST
	 */
	function add_like_to_post(): bool {

		$count_like = get_post_meta( $this->now_post_liked, "count_like", true );

		if ( $count_like ) {
			$count_like = ( (int) $count_like ) + 1;
			if ( update_post_meta( $this->now_post_liked, "count_like", $count_like ) ) {
				$data = true;
			} else {
				$data = false;
			}
		} else {
			add_post_meta( $this->now_post_liked, "count_like", 1 );

			$data = true;
		}

		return $data;
	}

	function save_post_like() {
		add_action( "save_post", array($this , "add_like_to_post") );
	}

}

new LikeAPI();