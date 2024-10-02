<?php
/**
 * File that holds our REST controller
 */

/**
 * Class that holds our REST controller
 */
class Ultimeter_REST_Controller extends WP_REST_Controller {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace     = 'ultimeter/v1';
		$this->resource_name = 'ultimeters';
		$this->post_type     = 'ultimeter';

	}

	/**
	 * Register our routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name,
			array(
				// Here we register the readable endpoint for collections.
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
				),
				// Register our schema callback.
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/(?P<id>[\d]+)',
			array(
				// Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				array(
					'methods'             => 'PUT',
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				// Register our schema callback.
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	/**
	 * Check permissions for the posts.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function get_items_permissions_check( $request ) {

		return true;
	}

	/**
	 * Check permissions for the posts.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function create_item_permissions_check( $request ) {

		if ( ! empty( $request['id'] ) ) {
			return new WP_Error(
				'rest_post_exists',
				__( 'Cannot create existing Ultimeter.' ),
				array( 'status' => 400 )
			);
		}

		$post_type = get_post_type_object( 'ultimeter' );

		if ( ! empty( $request['author'] ) && get_current_user_id() !== $request['author'] && ! current_user_can( $post_type->cap->edit_others_posts ) ) {
			return new WP_Error(
				'rest_cannot_edit_others',
				__( 'Sorry, you are not allowed to create Ultimeters as this user.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! empty( $request['sticky'] ) && ! current_user_can( $post_type->cap->edit_others_posts ) && ! current_user_can( $post_type->cap->publish_posts ) ) {
			return new WP_Error(
				'rest_cannot_assign_sticky',
				__( 'Sorry, you are not allowed to make Ultimeters sticky.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! current_user_can( $post_type->cap->create_posts ) ) {
			return new WP_Error(
				'rest_cannot_create',
				__( 'Sorry, you are not allowed to create Ultimeters as this user.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		// If this is a Zapier transaction, ensure Enterprise licence holder.
		if ( ! upgm_fs()->is_plan_or_trial( 'enterprise' ) ) {
			$key = $request->get_header( 'User-Agent' );
			if ( 'Zapier' === $key ) {
				return new WP_Error(
					'rest_cannot_edit',
					__( 'Only holders of an Ultimeter Enterprise licence can create Ultimeters using Zapier.' ),
					array( 'status' => 400 )
				);
			}
		}

		if ( ! upgm_fs()->is_plan_or_trial( 'enterprise' ) && ! class_exists( 'Ultimeter_REST_Addon' ) ) {
			return new WP_Error(
				'rest_cannot_create',
				__( 'Sorry, you are not allowed to create this post type.' ),
				array( 'status' => 400 )
			);
		}

		return true;
	}

	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new WP_Error(
				'rest_post_exists',
				__( 'Cannot create existing Ultimeter.' ),
				array( 'status' => 400 )
			);
		}

		$prepared_post  = new stdClass();
		$current_status = '';

		// Post ID.
		if ( isset( $request['id'] ) ) {
			$existing_post = $this->get_post( $request['id'] );
			if ( is_wp_error( $existing_post ) ) {
				return $existing_post;
			}

			$prepared_post->ID = $existing_post->ID;
			$current_status    = $existing_post->post_status;
		}

		$schema = $this->get_item_schema();

		// Post title.
		if ( ! empty( $schema['properties']['title'] ) && isset( $request['title'] ) ) {
			if ( is_string( $request['title'] ) ) {
				$prepared_post->post_title = wp_strip_all_tags( $request['title'] );
			} elseif ( ! empty( $request['title']['raw'] ) ) {
				$prepared_post->post_title = wp_strip_all_tags( $request['title']['raw'] );
			}
		}

		// Post type.
		if ( empty( $request['id'] ) ) {
			$prepared_post->post_type = $this->post_type;
		}

		// Post date.
		if ( ! empty( $schema['properties']['date'] ) && ! empty( $request['date'] ) ) {
			$current_date = isset( $prepared_post->ID ) ? get_post( $prepared_post->ID )->post_date : false;
			$date_data    = rest_get_date_with_gmt( $request['date'] );

			if ( ! empty( $date_data ) && $current_date !== $date_data[0] ) {
				list( $prepared_post->post_date, $prepared_post->post_date_gmt ) = $date_data;
				$prepared_post->edit_date                                        = true;
			}
		} elseif ( ! empty( $schema['properties']['date_gmt'] ) && ! empty( $request['date_gmt'] ) ) {
			$current_date = isset( $prepared_post->ID ) ? get_post( $prepared_post->ID )->post_date_gmt : false;
			$date_data    = rest_get_date_with_gmt( $request['date_gmt'], true );

			if ( ! empty( $date_data ) && $current_date !== $date_data[1] ) {
				list( $prepared_post->post_date, $prepared_post->post_date_gmt ) = $date_data;
				$prepared_post->edit_date                                        = true;
			}
		}

		// Sending a null date or date_gmt value resets date and date_gmt to their
		// default values (`0000-00-00 00:00:00`).
		if (
			( ! empty( $schema['properties']['date_gmt'] ) && $request->has_param( 'date_gmt' ) && null === $request['date_gmt'] ) ||
			( ! empty( $schema['properties']['date'] ) && $request->has_param( 'date' ) && null === $request['date'] )
		) {
			$prepared_post->post_date_gmt = null;
			$prepared_post->post_date     = null;
		}

		// Post slug.
		if ( ! empty( $schema['properties']['slug'] ) && isset( $request['slug'] ) ) {
			$prepared_post->post_name = $request['slug'];
		}

		// Author.
		if ( ! empty( $schema['properties']['author'] ) && ! empty( $request['author'] ) ) {
			$post_author = (int) $request['author'];

			if ( get_current_user_id() !== $post_author ) {
				$user_obj = get_userdata( $post_author );

				if ( ! $user_obj ) {
					return new WP_Error(
						'rest_invalid_author',
						__( 'Invalid author ID.' ),
						array( 'status' => 400 )
					);
				}
			}

			$prepared_post->post_author = $post_author;
		}

		$post_id = wp_insert_post( wp_slash( (array) $prepared_post ), true, false );

		wp_publish_post( $post_id );

		if ( is_wp_error( $post_id ) ) {

			if ( 'db_insert_error' === $post_id->get_error_code() ) {
				$post_id->add_data( array( 'status' => 500 ) );
			} else {
				$post_id->add_data( array( 'status' => 400 ) );
			}

			return $post_id;
		}

		$body = $request->get_json_params();

		if ( isset( $body['_ultimeter_currency'] ) ) {
			$sanitized = sanitize_text_field( $body['_ultimeter_currency'] );
			update_post_meta( $post_id, '_ultimeter_currency', $sanitized );
		}

		if ( isset( $body['_ultimeter_language'] ) ) {
			$sanitized = sanitize_text_field( $body['_ultimeter_language'] );
			update_post_meta( $post_id, '_ultimeter_language', $sanitized );
		}

		if ( isset( $body['_ultimeter_goal_amount'] ) ) {
			update_post_meta( $post_id, '_ultimeter_goal_amount', floatval( $body['_ultimeter_goal_amount'] ) );
		}

		if ( isset( $body['_ultimeter_goal_custom'] ) ) {
			update_post_meta( $post_id, '_ultimeter_goal_custom', floatval( $body['_ultimeter_goal_custom'] ) );
		}

		if ( isset( $body['_ultimeter_raised_amount'] ) ) {
			update_post_meta( $post_id, '_ultimeter_raised_amount', floatval( $body['_ultimeter_raised_amount'] ) );
		}

		if ( isset( $body['_ultimeter_raised_custom'] ) ) {
			update_post_meta( $post_id, '_ultimeter_raised_custom', floatval( $body['_ultimeter_raised_custom'] ) );
		}

		if ( isset( $body['_ultimeter_raised_percentage'] ) ) {
			update_post_meta(
				$post_id,
				'_ultimeter_raised_percentage',
				floatval( $body['_ultimeter_raised_percentage'] )
			);
		}

		if ( isset( $body['_ultimeter_meter_color'] ) ) {
			$sanitized = '#' . sanitize_hex_color_no_hash( $body['_ultimeter_meter_color'] );
			update_post_meta( $post_id, '_ultimeter_meter_color', $sanitized );
		}

		if ( isset( $body['_ultimeter_meter_type'] ) ) {
			$sanitized = sanitize_text_field( $body['_ultimeter_meter_type'] );
			update_post_meta( $post_id, '_ultimeter_meter_type', $sanitized );
		}

		$post = get_post( $post_id );

		$request->set_param( 'context', 'edit' );

		wp_after_insert_post( $post, false, null );

		$response = $this->prepare_item_for_response( $post, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header(
			'Location',
			rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $post_id ) )
		);

		return $response;
	}

	/**
	 * Grabs all Ultimeters and outputs them as a rest response.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function get_items( $request ) {
		$args  = array(
			'posts_per_page' => - 1,
			'post_type'      => 'ultimeter',
		);
		$posts = get_posts( $args );

		$data = array();

		if ( empty( $posts ) ) {
			return rest_ensure_response( $data );
		}

		foreach ( $posts as $post ) {
			$response = $this->prepare_item_for_response( $post, $request );
			$data[]   = $this->prepare_response_for_collection( $response );
		}

		// Return all of our comment response data.
		return rest_ensure_response( $data );
	}

	/**
	 * Check permissions for the posts.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function get_item_permissions_check( $request ) {

		return true;
	}

	/**
	 * Grabs the five most recent posts and outputs them as a rest response.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function get_item( $request ) {
		$id   = (int) $request['id'];
		$post = get_post( $id );

		if ( empty( $post ) ) {
			return rest_ensure_response( array() );
		}

		// Return all of our post response data.
		return $this->prepare_item_for_response( $post, $request );
	}

	public function update_item( $request ) {
		$valid_check = get_post( $request['id'] );
		if ( is_wp_error( $valid_check ) ) {
			return $valid_check;
		}

		$post_before    = get_post( $request['id'] );
		$post           = new stdClass();
		$current_status = '';
		$post->ID       = $post_before->ID;

		$schema = $this->get_item_schema();

		// Post title.
		if ( ! empty( $schema['properties']['title'] ) && isset( $request['title'] ) ) {
			if ( is_string( $request['title'] ) ) {
				$post->post_title = wp_strip_all_tags( $request['title'] );
			} elseif ( ! empty( $request['title']['raw'] ) ) {
				$post->post_title = wp_strip_all_tags( $request['title']['raw'] );
			}
		}

		// Post type.
		if ( empty( $request['id'] ) ) {
			$post->post_type = $this->post_type;
		}

		// Post status.
		if (
			! empty( $schema['properties']['status'] ) &&
			isset( $request['status'] ) &&
			( ! $current_status || $current_status !== $request['status'] )
		) {
			$status = $this->handle_status_param( $request['status'], $post_type );

			if ( is_wp_error( $status ) ) {
				return $status;
			}

			$post->post_status = $status;
		}

		// Post date.
		if ( ! empty( $schema['properties']['date'] ) && ! empty( $request['date'] ) ) {
			$current_date = isset( $post->ID ) ? get_post( $post->ID )->post_date : false;
			$date_data    = rest_get_date_with_gmt( $request['date'] );

			if ( ! empty( $date_data ) && $current_date !== $date_data[0] ) {
				list( $post->post_date, $post->post_date_gmt ) = $date_data;
				$post->edit_date                               = true;
			}
		} elseif ( ! empty( $schema['properties']['date_gmt'] ) && ! empty( $request['date_gmt'] ) ) {
			$current_date = isset( $post->ID ) ? get_post( $prepared_post->ID )->post_date_gmt : false;
			$date_data    = rest_get_date_with_gmt( $request['date_gmt'], true );

			if ( ! empty( $date_data ) && $current_date !== $date_data[1] ) {
				list( $post->post_date, $post->post_date_gmt ) = $date_data;
				$post->edit_date                               = true;
			}
		}

		// Sending a null date or date_gmt value resets date and date_gmt to their
		// default values (`0000-00-00 00:00:00`).
		if (
			( ! empty( $schema['properties']['date_gmt'] ) && $request->has_param( 'date_gmt' ) && null === $request['date_gmt'] ) ||
			( ! empty( $schema['properties']['date'] ) && $request->has_param( 'date' ) && null === $request['date'] )
		) {
			$post->post_date_gmt = null;
			$post->post_date     = null;
		}

		// Post slug.
		if ( ! empty( $schema['properties']['slug'] ) && isset( $request['slug'] ) ) {
			$post->post_name = $request['slug'];
		}

		// Author.
		if ( ! empty( $schema['properties']['author'] ) && ! empty( $request['author'] ) ) {
			$post_author = (int) $request['author'];

			if ( get_current_user_id() !== $post_author ) {
				$user_obj = get_userdata( $post_author );

				if ( ! $user_obj ) {
					return new WP_Error(
						'rest_invalid_author',
						__( 'Invalid author ID.' ),
						array( 'status' => 400 )
					);
				}
			}

			$post->post_author = $post_author;
		}

		if ( is_wp_error( $post ) ) {
			return $post;
		}

		// Convert the post object to an array, otherwise wp_update_post() will expect non-escaped input.
		$post_id = wp_update_post( wp_slash( (array) $post ), true, false );

		if ( is_wp_error( $post_id ) ) {
			if ( 'db_update_error' === $post_id->get_error_code() ) {
				$post_id->add_data( array( 'status' => 500 ) );
			} else {
				$post_id->add_data( array( 'status' => 400 ) );
			}

			return $post_id;
		}

		$post = get_post( $post_id );

		$schema = $this->get_item_schema();

		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $post->ID );

			if ( is_wp_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$body = $request->get_json_params();

		if ( isset( $body['_ultimeter_currency'] ) ) {
			$sanitized = sanitize_text_field( $body['_ultimeter_currency'] );
			update_post_meta( $post->ID, '_ultimeter_currency', $sanitized );
		}

		if ( isset( $body['_ultimeter_language'] ) ) {
			$sanitized = sanitize_text_field( $body['_ultimeter_language'] );
			update_post_meta( $post->ID, '_ultimeter_language', $sanitized );
		}

		if ( isset( $body['_ultimeter_goal_amount'] ) ) {
			update_post_meta( $post->ID, '_ultimeter_goal_amount', floatval( $body['_ultimeter_goal_amount'] ) );
		}

		if ( isset( $body['_ultimeter_goal_custom'] ) ) {
			update_post_meta( $post->ID, '_ultimeter_goal_custom', floatval( $body['_ultimeter_goal_custom'] ) );
		}

		if ( isset( $body['_ultimeter_goal_amount'] ) ) {
			update_post_meta( $post->ID, '_ultimeter_goal_amount', floatval( $body['_ultimeter_goal_amount'] ) );
		}

		if ( isset( $body['_ultimeter_raised_amount'] ) ) {
			update_post_meta( $post->ID, '_ultimeter_raised_amount', floatval( $body['_ultimeter_raised_amount'] ) );
		}

		if ( isset( $body['_ultimeter_raised_custom'] ) ) {
			update_post_meta( $post->ID, '_ultimeter_raised_custom', floatval( $body['_ultimeter_raised_custom'] ) );
		}

		if ( isset( $body['_ultimeter_raised_percentage'] ) ) {
			update_post_meta(
				$post->ID,
				'_ultimeter_raised_percentage',
				floatval( $body['_ultimeter_raised_percentage'] )
			);
		}

		if ( isset( $body['_ultimeter_meter_color'] ) ) {
			$sanitized = '#' . sanitize_hex_color_no_hash( $body['_ultimeter_meter_color'] );
			update_post_meta( $post->ID, '_ultimeter_meter_color', $sanitized );
		}

		if ( isset( $body['_ultimeter_meter_type'] ) ) {
			$sanitized = sanitize_text_field( $body['_ultimeter_meter_type'] );
			update_post_meta( $post->ID, '_ultimeter_meter_type', $sanitized );
		}

		$post = get_post( $post_id );

		$request->set_param( 'context', 'edit' );

		wp_after_insert_post( $post, true, $post_before );

		$response = $this->prepare_item_for_response( $post, $request );

		return rest_ensure_response( $response );
	}

	public function update_item_permissions_check( $request ) {
		$post = get_post( $request['id'] );
		if ( is_wp_error( $post ) ) {
			return $post;
		}

		$post_type = get_post_type_object( $this->post_type );

		if ( $post && ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error(
				'rest_cannot_edit',
				__( 'Sorry, you are not allowed to edit this Ultimeter. Please ensure you are correctly authenticated.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! empty( $request['author'] ) && get_current_user_id() !== $request['author'] && ! current_user_can( $post_type->cap->edit_others_posts ) ) {
			return new WP_Error(
				'rest_cannot_edit_others',
				__( 'Sorry, you are not allowed to update Ultimeters as this user. Please ensure you are correctly authenticated.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! empty( $request['sticky'] ) && ! current_user_can( $post_type->cap->edit_others_posts ) && ! current_user_can( $post_type->cap->publish_posts ) ) {
			return new WP_Error(
				'rest_cannot_assign_sticky',
				__( 'Sorry, you are not allowed to make Ultimeters sticky. Please ensure you are correctly authenticated.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		// If this is a Zapier transaction, ensure Enterprise licence holder.
		if ( ! upgm_fs()->is_plan_or_trial( 'enterprise' ) ) {
			$key = $request->get_header( 'User-Agent' );
			if ( 'Zapier' === $key ) {
				return new WP_Error(
					'rest_cannot_edit',
					__( 'Only holders of an Ultimeter Enterprise licence can edit Ultimeters using Zapier.' ),
					array( 'status' => 400 )
				);
			}
		}

		if ( ! upgm_fs()->is_plan_or_trial( 'enterprise' ) && ! class_exists( 'Ultimeter_REST_Addon' ) ) {
			return new WP_Error(
				'rest_cannot_edit',
				__( 'Sorry, you are not allowed to edit this post.' ),
				array( 'status' => 400 )
			);
		}

		return true;
	}

	/**
	 * Matches the post data to the schema we want.
	 *
	 * @param WP_Post         $post The comment object whose response is being prepared.
	 * @param WP_REST_Request $request REST Request.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function prepare_item_for_response( $post, $request ) {
		$post_data = array();

		$schema = $this->get_item_schema( $request );

		// We are also renaming the fields to more understandable names.
		if ( isset( $schema['properties']['id'] ) ) {
			$post_data['id'] = (int) $post->ID;
		}

		if ( isset( $schema['properties']['title'] ) ) {
			$post_data['title'] = apply_filters( 'the_title', $post->post_title, $post );
		}

		if ( isset( $schema['properties']['date'] ) ) {
			$post_data['date'] = get_the_time( 'F j, Y g:i a', $post->ID );
		}

		// Author.
		if ( isset( $schema['properties']['author'] ) ) {
			$author_id           = $post->post_author;
			$post_data['author'] = get_the_author_meta( 'user_login', $author_id );
		}

		$meta = get_post_meta( $post->ID );

		foreach ( $meta as $key => $value ) {
			$post_data[ $key ] = $value;
		}

		return rest_ensure_response( $post_data );
	}

	/**
	 * Prepare a response for inserting into a collection of responses.
	 *
	 * This is copied from WP_REST_Controller class in the WP REST API v2 plugin.
	 *
	 * @param WP_REST_Response $response Response object.
	 *
	 * @return array Response data, ready for insertion into collection data.
	 */
	public function prepare_response_for_collection( $response ) {
		if ( ! ( $response instanceof WP_REST_Response ) ) {
			return $response;
		}

		$data   = (array) $response->get_data();
		$server = rest_get_server();

		if ( method_exists( $server, 'get_compact_response_links' ) ) {
			$links = call_user_func( array( $server, 'get_compact_response_links' ), $response );
		} else {
			$links = call_user_func( array( $server, 'get_response_links' ), $response );
		}

		if ( ! empty( $links ) ) {
			$data['_links'] = $links;
		}

		return $data;
	}

	/**
	 * Get our sample schema for a post.
	 *
	 * @return array The sample schema for a post
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			// Since WordPress 5.3, the schema can be cached in the $schema property.
			return $this->schema;
		}

		$this->schema = array(
			// This tells the spec of JSON Schema we are using which is draft 4.
			'$schema'     => 'http://json-schema.org/draft-04/schema#',
			// The title property marks the identity of the resource.
			'title'       => 'Ultimeter',
			'description' => 'An Ultimeter created via the Ultimeter plugin',
			'type'        => 'object',
			// In JSON Schema you can specify object properties in the properties attribute.
			'properties'  => array(
				'id'     => array(
					'description' => esc_html__( 'Unique identifier for this Ultimeter.', 'ultimeter' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'title'  => array(
					'description' => esc_html__( 'The title for this Ultimeter.', 'ultimeter' ),
					'type'        => 'string',
				),
				'date'   => array(
					'description' => esc_html__( 'The date this Ultimeter was published.', 'ultimeter' ),
					'type'        => 'date-time',
				),
				'author' => array(
					'description' => esc_html__( 'The author who created this Ultimeter.', 'ultimeter' ),
					'type'        => 'integer',
				),
			),
		);

		return $this->schema;
	}

	/**
	 * Sets up the proper HTTP status code for authorization.
	 *
	 * @return int
	 */
	public function authorization_status_code() {

		$status = 401;

		if ( is_user_logged_in() ) {
			$status = 403;
		}

		return $status;
	}
}

/**
 * Function to register our new routes from the controller.
 *
 * @return void
 */
function ultimeter_register_rest_routes() {
	$controller = new Ultimeter_REST_Controller();
	$controller->register_routes();
}

add_action( 'rest_api_init', 'ultimeter_register_rest_routes' );
