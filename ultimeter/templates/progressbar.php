<?php

/**
 * Creates a progress bar type meter.
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$class = '';
$image = false;
$icon = false;
$goal = get_post_meta( $meter['id'], '_ultimeter_progressbar_goal_toggle', true );
?>

<div class="ultimeter_meter ultimeter_progressbar <?php 
echo  $class ;
?>">
	<?php 

if ( !empty($goal) && 1 == $goal ) {
    ?>
	<div class="ultimeter_meter_goal ultimeter_progressbar_goal">
		<div class="ultimeter_meter_amount total-label ultimeter_progressbar_amount <?php 
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
}

?>
	<div class="ultimeter_meter_outer ultimeter_progressbar_outer secondary-color">
		<?php 
?>
		<div class="ultimeter_meter_progress ultimeter_progressbar_progress primary-color">
			<?php 
?>
			<div class="ultimeter_meter_amount current-label ultimeter_progressbar_amount">
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
</div>
