<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
/**
 *
 * Field: connected_image_select
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'CSF_Field_connected_image_select' ) ) {
	class CSF_Field_connected_image_select extends CSF_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {
			$args = wp_parse_args( $this->field, array(
				'multiple' => false,
				'sets'     => array(),
			) );

			$value = ( is_array( $this->value ) ) ? $this->value : array_filter( (array) $this->value );

			echo $this->field_before();

			echo '<div class="csf-connected-image-select-group-container" data-depend-id="' . esc_attr( $this->field['id'] ) . '">';

			foreach ( $this->field['sets'] as $key => $set ) {
				echo '<div class="csf-connected-image-select-group" style="order: ' . esc_attr( $set['order'] ) . '">';

				$icon = ( ! empty( $set['icon'] ) ) ? 'csf--icon ' . $set['icon'] : 'csf-set-icon fas fa-angle-right';

				echo '<h4 class="csf-connected-image-select-group-title">';
				echo '<i class="' . esc_attr( $icon ) . '"></i>';
				echo esc_html( $set['title'] );
				echo '</h4>';

				echo '<div class="csf-connected-image-select-group-content">';

				$num = 1;

				foreach ( $set['items'] as $item ) {
					$type    = ( $args['multiple'] ) ? 'checkbox' : 'radio';
					$extra   = ( $args['multiple'] ) ? '[]' : '';
					$active  = ( in_array( $item['id'], $value, true ) ) ? ' csf--active' : '';
					$checked = ( in_array( $item['id'], $value, true ) ) ? ' checked' : '';

					if ( upgm_fs()->can_use_premium_code() || ! $item['premium']  ) {
						echo '<div class="csf--sibling csf--image' . esc_attr( $active ) . '" style="order: ' . esc_attr( $item['order'] ) . '">';
						echo '<figure>';
						echo '<img src="' . esc_url( $item['src'] ) . '" alt="img-' . esc_attr( $num ++ ) . '-' . esc_attr( $item['id'] ) . '" />';
						echo '<input type="' . esc_attr( $type ) . '" name="' . esc_attr( $this->field_name( $extra ) ) . '" value="' . esc_attr( $item['id'] ) . '"' . $this->field_attributes() . esc_attr( $checked ) . '/>';
					} else {
						echo '<div title="A valid licence is required to use this meter type" class="csf--image csf--free" style="order: ' . esc_attr( $item['order'] ) . '">';
						echo '<figure>';
						echo '<img src="' . esc_url( $item['src'] ) . '" alt="img-' . esc_attr( $num ++ ) . '-' . esc_attr( $item['id'] ) . '" />';
					}
					echo '</figure>';
					echo '<label>' . esc_attr( $item['title'] ) . '</label>';
					echo '</div>';
				}

				echo '</div>';

				echo '</div>';
			}

			echo '</div>';

			echo $this->field_after();
		}

		public function output() {
			$output    = '';
			$bg_image  = array();
			$important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
			$elements  = ( is_array( $this->field['output'] ) ) ? join( ',',
				$this->field['output'] ) : $this->field['output'];

			if ( ! empty( $elements ) && isset( $this->value ) && $this->value !== '' ) {
				$output = $elements . '{background-image:url(' . $this->value . ')' . $important . ';}';
			}

			$this->parent->output_css .= $output;

			return $output;
		}

	}
}
