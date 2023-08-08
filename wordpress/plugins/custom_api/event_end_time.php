<?php

add_action( 'rest_api_init', function () {
	register_rest_route( 'namespace/v1', '/category/', array(
		'methods'  => 'GET',
		'callback' => 'custom_category_api_callback',
	) );
} );

function custom_category_api_callback() {
	$categories = get_categories();

	$data = [];

	if ( ! empty( $categories ) ) {
		foreach ( $categories as $category ) {

			$sub_category_name = false;
			$sub_category_slug = false;

			if ( $category->parent ) {
				$sub_category = get_category( $category->parent );

				if ( $sub_category instanceof WP_Term ) {
					$sub_category_name = $sub_category->name;
					$sub_category_slug = $sub_category->slug;
				}
			}

			$simple_category = array(
				'categoryID'          => $category->term_id,
				'categoryName'        => $category->name,
				'categorySlug'        => $category->slug,
				'categoryDescription' => $category->description,
				'subCategoryName'     => $sub_category_name,
				'subCategorySlug'     => $sub_category_slug,
			);

			$data[] = $simple_category;
		}

	} else {
		$data = array(
			'error'   => 'no category',
			'message' => 'write some post',
		);
	}

	return $data;
}