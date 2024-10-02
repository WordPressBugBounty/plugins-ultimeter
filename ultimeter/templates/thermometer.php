<?php

/**
 * Creates a thermometer type meter.
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$size = $meter['styles']['size'];
?>
<div class="ultimeter_meter ultimeter_thermometer <?php 
echo  esc_attr( $size ) ;
?>">
	<div class="ultimeter_thermometer_tube_topper secondary-color"></div>
	<div class="ultimeter_thermometer_tube secondary-color">
		<div class="ultimeter_thermometer_tube_scale"></div>
		<div class="ultimeter_meter_goal ultimeter_thermometer_goal">
			<div class="ultimeter_meter_amount total-label ultimeter_thermometer_amount <?php 
echo  esc_attr( $meter['output_type'] ) ;
?>">
				<?php 
?>
				<span class="calculated"><?php 
echo  esc_html( $meter['total'] ) ;
?></span>
				<?php 
?>
			</div>
	</div>
	<?php 
?>
	<div class="ultimeter_meter_progress ultimeter_thermometer_progress primary-color">
		<div class="ultimeter_meter_amount current-label ultimeter_thermometer_amount <?php 
echo  esc_attr( $meter['output_type'] ) ;
?>">
			<?php 
?>
			<span class="calculated"><?php 
echo  esc_html( $meter['current'] ) ;
?></span>
			<?php 
?>
		</div>
	</div>
	</div>
	<div class="ultimeter_thermometer_bulb secondary-color">
		<div class="ultimeter_thermometer_bulb_mercury primary-color"
			style="background: radial-gradient(circle at bottom right, #ffffff 0%, <?php 
echo  esc_html( $meter['styles']['primary_color'] ) ;
?> 75%, <?php 
echo  esc_attr( $meter['styles']['primary_color'] ) ;
?> 100%);"></div>
		<div class="ultimeter_thermometer_glue primary-color"></div>
	</div>
</div>
