<?php
/*
Plugin Name: Custom Author Info
Description: Insert Custom Data for Author in Database.
Version: 1.0
Author: Mahdi Najafzadeh
*/

class CustomUser {
	function __construct() {

		// load main page
		add_action( 'admin_menu', array( $this, 'loadAdminMenu' ) );
		// load API Endpoint
		add_action( 'rest_api_init', function () {
			register_rest_route( 'namespace/v1', '/author/', array(
				'methods'  => 'GET',
				'callback' => array( $this, "callback_api" ),
			) );
		} );

	}

	function loadAdminMenu() {
		add_menu_page( "Custom User", "Custom User",
			"manage_options", "custom-user",
			array( $this, "loadHtmlMainPage" ), "", 100 );
	}

	function loadHtmlMainPage() {

		$result_data = $this->fetchFromData();

		if ( $result_data !== array() ) {
			if ( $result_data['status'] ) {
				?>
                <div class="updated notice is-dismissible" style="height: max-content ; padding: 10px">
                    Change Data for `<?php echo $result_data['username']; ?>` Successfully.
                </div>
				<?php
			} else {
				?>
                <div class="error-message notice error is-dismissible" style="height: max-content ; padding: 10px">
                    Change Data for `<?php echo $result_data['username']; ?>` Failed. Error : <?php
					foreach ( $result_data['errors'] as $error ) {
						echo( "Attribut : " . $error['metadata_name'] . " , Error : " . $error['error'] );
					}

					?>.
                </div>
				<?php
			}
		}


		?>
        <h1> Custom User</h1>
        <div style="width: 100% ; display: flex; justify-items: auto ;">
		<?php


		$users = $this->get_all_authors();

		if ( empty( $users ) ) {
			?><h1 style="color: darkred">Error : No Users Found !</h1><?php
			return;
		}

		foreach ( $users as $user ) {
			$this->displayUserForm( $user->ID );
		}

		?></div><?php


	}

	function fetchFromData() {

		if ( isset( $_POST['user_id'] ) ) {
			$metadata_name_list = array(
				"first_name",
				"last_name",
				"instagram",
				"telegram",
				"category",
				"user_url"
			);

			$result_data        = array( "username" => $_POST['user_login'], "status" => true );
			$handle_first_error = true;
			foreach ( $metadata_name_list as $metadata_name ) {
				try {
					if ( ! ( update_user_meta( $_POST['user_id'], $metadata_name, $_POST[ $metadata_name ] ) ) ) {
						add_user_meta( 0, $metadata_name, $_POST[ $metadata_name ] );
					}
				} catch ( Exception $error ) {
					if ( $handle_first_error ) {
						$result_data        = array(
							"username" => $_POST['user_login'],
							"status"   => false,
							"errors"   => array()
						);
						$handle_first_error = false;
					}
					$result_data['errors'][] = array(
						'metadata_name' => $metadata_name,
						'error'         => ( $error->getMessage() )
					);
				}
			}

			return $result_data;
		} else {
			return array();
		}
	}

	function displayUserForm( $user_id ) {
		?>
        <div style="border: #0a4b78 solid 2px ; padding: 15px ; width: fit-content ; margin: 10px ;">
            <form action="" method="post">
                <table>
                    <thead>
                    <h2>User Info
                        : <?php echo esc_html( get_the_author_meta( 'user_login', $user_id ) ); ?></h2>
                    </thead>
                    <tr>
                        <td> Username</td>
                        <td><input type="text"
                                   name="user_login"
                                   id="user_login"
                                   disabled
                                   value="<?php echo esc_html( get_the_author_meta( 'user_login', $user_id ) ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td> Email</td>
                        <td>
                            <input type="text"
                                   name="user_email"
                                   id="user_email"
                                   disabled
                                   value="<?php echo esc_html( get_the_author_meta( 'user_email', $user_id ) ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td> Name</td>
                        <td><input type="text"
                                   name="first_name"
                                   id="first_name"

                                   value="<?php echo esc_html( get_the_author_meta( 'first_name', $user_id ) ); ?>">
                        </td>
                    </tr>
                    </tr>
                    <tr>
                        <td> Family</td>
                        <td><input type="text"
                                   name="last_name"
                                   id="last_name"

                                   value="<?php echo esc_html( get_the_author_meta( 'last_name', $user_id ) ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td> Instagram</td>
                        <td><input type="text"
                                   name="instagram"
                                   id="instagram"
                                   value="<?php echo esc_html( get_user_meta( $user_id, 'instagram', true ) ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td> Telegram</td>
                        <td><input type="text"
                                   name="telegram"
                                   id="telegram"
                                   value="<?php echo esc_html( get_user_meta( $user_id, 'telegram', true ) ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td> Category</td>
                        <td><input type="text"
                                   name="category"
                                   id="category"
                                   value="<?php echo esc_html( get_user_meta( $user_id, 'category', true ) ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td> Website</td>
                        <td><input type="text"
                                   name="user_url"
                                   id="user_url"
                                   value="<?php echo esc_html( get_user_meta( $user_id, 'user_url', true ) ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td style="text-align: right">
                            <input type="hidden" name="user_id" id="user_id"
                                   value="<?php echo $user_id ?>">
                            <input type="hidden" name="user_login" id="user_login"
                                   value="<?php echo esc_html( get_the_author_meta( 'user_login', $user_id ) ); ?>">
                            <input class="button" type="submit" name="submit" id="submit" value="Save Changes">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
		<?php
	}

	function get_all_authors() {
		$args = array(
			'role'    => 'author',
			'orderby' => 'display_name',
			'order'   => 'ASC',
		);

		$user_query = new WP_User_Query( $args );

		if ( ! empty( $user_query->get_results() ) ) {
			return $user_query->get_results();
		}

		return array();
	}

	function get_custom_author_data( $author_id ) {
		if ( ! $author_id ) {
			return array();
		}

		$metadata_name_list = array(
			"first_name",
			"last_name",
			"instagram",
			"telegram",
			"category",
			"user_url"
		);

		$author_data = array();

		foreach ( $metadata_name_list as $metadata ) {
			$author_data[ $metadata ] = get_user_meta( $author_id, $metadata, true );
		}

		return $author_data;
	}

	function get_last_5_posts_by_author( $author_id ) {
		$args = array(
			'author'         => $author_id,
			'post_type'      => 'post',
			'posts_per_page' => 5,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$query = new WP_Query( $args );

		$posts = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$tags      = get_the_tags();
				$categorys = get_the_category();
				$thumbnail_url = get_the_post_thumbnail_url(get_the_ID());

				$tag_names = array();
				if ( $tags ) {
					$tag_names = array_map( function ( $tag ) {
						return $tag->name;
					}, $tags );
				}

				$category_names = array();
				if ( $categorys ) {
					$category_names = array_map( function ( $category ) {
						return $category->name;
					}, $categorys );
				}

				$posts[] = array(
					'title'    => get_the_title(),
					'date'     => get_the_date(),
					'category' => $category_names,
					'tags'     => $tag_names,
                    'image' => $thumbnail_url
				);
			}
			wp_reset_postdata();
		}

		return $posts;
	}


	function callback_api() {
		$authors = $this->get_all_authors();
		$data    = array();

		foreach ( $authors as $author ) {
			$author_data         = $this->get_custom_author_data( $author->ID );
			$author_data['post'] = $this->get_last_5_posts_by_author( $author->ID );
			$data[]              = $author_data;
		}

		return $data;
	}

}

$cloneClass = new CustomUser();