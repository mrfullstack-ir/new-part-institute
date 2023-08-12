<?php

class PostByCategory {
	function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( 'namespace/v1', '/post_by_category/', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'api_callback' ),
			) );
		} );
	}

	function api_callback( WP_REST_Request $request ) {
		$data = array();

		$query = $request->get_query_params();

		if ( isset( $query['name'] ) && ! empty( $query['name'] ) ) {

			$category_id = $this->get_category_id_by_name( $query['name'] );

			$short = false;
			if ( isset( $query['short'] ) && ! empty( $query['short'] ) ) {
				$short = true;
			}

			$post_in_cateqory = $this->get_posts_by_category( $category_id, $short );

			$data = $post_in_cateqory;
		}

		return $data;
	}

	function get_posts_by_category( $category_id, $short = false ) {
		$args = array(
			'category__in'   => array( $category_id ),
			'posts_per_page' => - 1,
		);

		$query = new WP_Query( $args );

		$posts = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$first_name = get_user_meta( get_the_author_ID(), "first_name", true );
				$last_name  = get_user_meta( get_the_author_ID(), "last_name", true );


				$post = array(
					'title'  => get_the_title(),
					'author' => ( $first_name . " " . $last_name ),
				);

				if ( $short ) {
					$post['excerpt'] = get_the_excerpt();
				} else {
					$post['content'] = get_the_content();
				}

				$posts[] = $post;
			}
			wp_reset_postdata();
		}

		return $posts;
	}

	function get_category_id_by_name( $category_name ) {
		$category = get_term_by( 'name', $category_name, 'category' );

		if ( $category && ! is_wp_error( $category ) ) {
			return $category->term_id;
		} else {
			return false;
		}
	}

}

new PostByCategory();

// $data = array( 'count' => (int) wp_count_posts()->publish );
