<?php
/**
 * File that holds the Ultimeter widget class.
 */

/**
 * Class that creates and controls our Ultimeter widget.
 */
class Ultimeter_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'ultimeter_widget',
			'Ultimeter',
			array( 'description' => 'Add an Ultimeter to another page in your site.' )
		);
	}
	public function widget( $args, $instance ) {
		if ( '' == $instance['id'] ) {
			return;
		}
		$shortcode = '[ultimeter id="' . $instance['id'] . '"]';
		echo $args['before_widget'];
		echo do_shortcode( $shortcode );
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		// outputs the options form on the admin screen
		if ( isset( $instance['id'] ) ) {
			$id = $instance['id'];
		} else {
			$id = '';
		}
		?>
		<p>
			<label for="ultimeter_widget_dropdown"><?php _e( 'Choose an Ultimeter:' ); ?></label>
			<?php
			// Create our arguments for getting our post
			$args = array(
				'post_type' => 'ultimeter',
			);

			// we get an array of posts objects
			$posts = get_posts( $args );

			// start our string
			$str = '<select id="' . $this->get_field_id( 'id' ) . '" name="' . $this->get_field_name( 'id' ) . '" >';
			// then we create an option for each post
			foreach ( $posts as $key => $post ) {
				$str .= '<option value="' . $post->ID . '"' . selected( $instance['id'], $post->ID, false ) . '>' . esc_attr( $post->post_title ) . '</option>';
			}
			$str .= '</select>';
			echo $str;
			?>

		</p>
		<?php
	}
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance       = array();
		$instance['id'] = ( ! empty( $new_instance['id'] ) ) ? strip_tags( $new_instance['id'] ) : '';
		return $instance;
	}
}

add_action( 'widgets_init', 'ultimeter_register_widgets' );

/**
 * Register the new widget.
 *
 * @see 'widgets_init'
 */
function ultimeter_register_widgets() {
	register_widget( 'Ultimeter_Widget' );
}
