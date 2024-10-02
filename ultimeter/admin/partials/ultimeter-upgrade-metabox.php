<div id="ultimeter-upgrade-panel">

	<?php if ( ! upgm_fs()->is_plan( 'pro' ) ) { ?>
    <div id="ultimeter-upgrade-panel-pro" class="ultimeter-upgrade-panel">
        <div class="ultimeter-upgrade-panel-header">
            <h2><?php esc_html_e( 'Professional Edition', 'ultimeter' ) ?></h2>
        </div>
        <div class="ultimeter-upgrade-panel-body">
            <h3><?php esc_html_e( 'Integrates with:', 'ultimeter' ) ?></h3>
            <div class="ultimeter-upgrade-panel-body-logos">
                <p><img src="<?php echo( plugin_dir_url( __FILE__ ) . '../assets/images/give-logo.png' ) ?>"
                        alt="GiveWP logo"></p>
                <p><img src="<?php echo( plugin_dir_url( __FILE__ ) . '../assets/images/charitable-logo.png' ) ?>"
                        alt="Charitable logo"></p>
                <p><img src="<?php echo( plugin_dir_url( __FILE__ ) . '../assets/images/gravity.png' ) ?>"
                        alt="Gravity logo"></p>
                <p><img src="<?php echo( plugin_dir_url( __FILE__ ) . '../assets/images/woo.png' ) ?>"
                        alt="WooCommerce logo"><span
                            class="ultimeter-upgrade-panel-small">(Single Product Sales Only)</span></p>
            </div>
            <p>Plus, any data that can be used inside a hook</p>
            <h3><?php esc_html_e( 'Lots More Meters', 'ultimeter' ) ?></h3>
            <h3><?php esc_html_e( 'Milestones', 'ultimeter' ) ?></h3>
            <h3><?php esc_html_e( 'Use your Own Logo or Design', 'ultimeter' ) ?></h3>
            <h3><?php esc_html_e( 'Celebrations', 'ultimeter' ) ?></h3>
            <h3><?php esc_html_e( 'More Styling Options', 'ultimeter' ) ?></h3>
            <a href="<?php echo upgm_fs()->get_upgrade_url() ?>" class="button button-primary button-large">Upgrade
                Now!</a>
        </div>
    </div>
	<?php } ?>

    <?php if ( ! upgm_fs()->is_plan( 'enterprise' ) ) { ?>
    <div id="ultimeter-upgrade-panel-enterprise" class="ultimeter-upgrade-panel">
        <div class="ultimeter-upgrade-panel-header">
            <h2><?php esc_html_e( 'Enterprise Edition', 'ultimeter' ) ?></h2>
        </div>
        <div class="ultimeter-upgrade-panel-body">
            <h3><?php esc_html_e( 'Integrates with:', 'ultimeter' ) ?></h3>
            <p><img src="
	<?php echo( plugin_dir_url( __FILE__ ) . '../assets/images/zapier.png' ) ?>"
                    alt="Zapier logo">(2000+ apps)</p>
            <p><img src="<?php echo( plugin_dir_url( __FILE__ ) . '../assets/images/woo.png' ) ?>"
                    alt="WooCommerce logo"></p>
            <h3><?php esc_html_e( 'WordPress REST API', 'ultimeter' ) ?></h3>
            <h3><?php esc_html_e( 'Real-time Updating', 'ultimeter' ) ?></h3>
            <p style="padding: 0 20px; text-align: center; font-size: 11px; margin-top: -5px;"><?php esc_html_e( 'No page refreshes required. Leave your meter on a screen and it will update by itself.', 'ultimeter' ) ?></p>
            <a href="<?php echo upgm_fs()->get_upgrade_url() ?>" class="button button-primary button-large">Upgrade
                Now!</a>
        </div>
    </div>
    <?php } ?>

</div>
