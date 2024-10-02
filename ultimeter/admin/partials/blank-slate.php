<?php
/**
 * Displays onboarding message in the event of an empty Ultimeter list table.
 *
 * @since 2.2
 */

?>

<div class="ultimeter-blank-slate">
	<img class="ultimeter-blank-slate-image" src="<?php echo esc_url_raw( plugin_dir_url( __FILE__ ) . '../assets/images/logo.png' ); ?>" alt="<?php esc_html_e( 'Ultimeter Logo', 'ultimeter' ); ?>">
	<h2 class="ultimeter-blank-slate-heading"><?php esc_html_e( 'No Ultimeters found.', 'ultimeter' ); ?></h2>
	<p class="ultimeter-blank-slate-message"><?php esc_html_e( 'The first step towards displaying your progress is to create an Ultimeter.', 'ultimeter' ); ?></p>
	<a class="ultimeter-blank-slate-cta button button-primary" href="post-new.php?post_type=ultimeter"><?php esc_html_e( 'Create Ultimeter', 'ultimeter' ); ?></a>
	<p class="ultimeter-blank-slate-help"><?php esc_html_e( 'Need help? Get started at our ', 'ultimeter' ); ?><a target="_blank" href="<?php echo esc_attr( ULTIMETER_SUPPORT ); ?>"><?php esc_html_e( 'support page', 'ultimeter' ); ?></a>.</p>
</div>
