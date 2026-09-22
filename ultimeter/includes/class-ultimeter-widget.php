<?php
/**
 * File that holds the Ultimeter widget class.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
		if ( empty( $instance['id'] ) ) {
			return;
		}
		$shortcode = '[ultimeter id="' . absint( $instance['id'] ) . '"]';
		echo wp_kses_post( $args['before_widget'] );
		echo do_shortcode( $shortcode );
		echo wp_kses_post( $args['after_widget'] );
	}

	public function form( $instance ) {
		$id = isset( $instance['id'] ) ? absint( $instance['id'] ) : 0;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"><?php esc_html_e( 'Choose an Ultimeter:', 'ultimeter' ); ?></label>
			<?php
			$posts = get_posts(
				array(
					'post_type'      => 'ultimeter',
					'posts_per_page' => 100,
					'post_status'    => 'publish',
				)
			);
			?>
			<select id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>">
				<?php foreach ( $posts as $post ) : ?>
					<option value="<?php echo esc_attr( $post->ID ); ?>" <?php selected( $id, $post->ID ); ?>><?php echo esc_html( $post->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance       = array();
		$instance['id'] = ( ! empty( $new_instance['id'] ) ) ? absint( $new_instance['id'] ) : 0;
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
