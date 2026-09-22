<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a short RSS link list for the Enterprise dashboard.
 *
 * @param string $feed_url      Feed URL.
 * @param string $empty_message Message shown when the feed is empty.
 * @return void
 */
function ultimeter_render_admin_rss_feed( $feed_url, $empty_message ) {
	require_once ABSPATH . WPINC . '/feed.php';

	$ultimeter_rss      = fetch_feed( $feed_url );
	$ultimeter_maxitems = 0;
	$ultimeter_items    = array();

	if ( ! is_wp_error( $ultimeter_rss ) ) {
		$ultimeter_maxitems = $ultimeter_rss->get_item_quantity( 5 );
		$ultimeter_items    = $ultimeter_rss->get_items( 0, $ultimeter_maxitems );
	}

	if ( 0 === $ultimeter_maxitems ) {
		echo '<p>' . esc_html( $empty_message ) . '</p>';
		return;
	}

	foreach ( $ultimeter_items as $ultimeter_item ) {
		printf(
			'<p><a href="%1$s" style="text-decoration: none;" target="_blank" rel="noopener noreferrer">%2$s</a></p>',
			esc_url( $ultimeter_item->get_permalink() ),
			esc_html( $ultimeter_item->get_title() )
		);
	}
}

if ( is_ssl() ) {
	$ultimeter_ssl_light  = 'ultimeter-status-green dashicons-yes';
	$ultimeter_ssl_header = 'SSL is enabled';
	$ultimeter_ssl_desc   = 'SSL helps to keep your data private, by encrypting any data between a user and a web server. Congratulations.';
} else {
	$ultimeter_ssl_light  = 'ultimeter-status-red dashicons-no';
	$ultimeter_ssl_header = 'SSL is disabled';
	$ultimeter_ssl_desc   = 'SSL helps to keep your data private, by encrypting any data between a user and a web server. Some services that want to talk to Ultimeter may not let you until SSL is enabled.';
}

if ( wp_is_application_passwords_available() ) {
	$ultimeter_ap_light  = 'ultimeter-status-green dashicons-yes';
	$ultimeter_ap_header = 'Application Passwords are enabled';
	$ultimeter_ap_desc   = 'Use Application Passwords to authenticate users without providing their passwords directly. Instead, a unique password is generated for each application without revealing the user’s main password.';
} else {
	$ultimeter_ap_light  = 'ultimeter-status-red dashicons-no';
	$ultimeter_ap_header = 'Application Passwords are disabled';
	$ultimeter_ap_desc   = 'Use Application Passwords to authenticate users without providing their passwords directly. Instead, a unique password is generated for each application without revealing the user’s main password. Some services that want to talk to Ultimeter will require an Application Password. You may have a security plugin that has turned this service off.';
}

$ultimeter_interval = get_option( 'ultimeter_refresh_interval' );
?>

<div class="ultimeter-enterprise-dashboard-section">
	<div class="ultimeter-enterprise-section-header">
		<h2 class="ultimeter-enterprise-section-header__title ultimeter-enterprise-section-header__header-item">
			Thanks for upgrading to the Enterprise Edition</h2>
		<hr role="presentation">
	</div>
	<div class="ultimeter-enterprise-dashboard">
		<div class="ultimeter-enterprise-dashboard__columns">
            <div class="left">
                <div class="ultimeter-enterprise-card">
                    <div class="ultimeter-enterprise-card__header">
                        <div class="ultimeter-enterprise-card__title-wrapper">
                            <h2	class="ultimeter-enterprise-card__title ultimeter-enterprise-card__header-item">Settings</h2>
                        </div>
                    </div>
                    <div class="ultimeter-enterprise-card__body">
	                    <form method="post" id="ultimeter-enterprise-settings" data-nonce="<?php echo esc_attr( wp_create_nonce( 'ultimeter_enterprise_settings' ) ); ?>">
		                    <label for="refresh-interval"><?php esc_html_e( 'Refresh Interval (seconds)', 'ultimeter' ); ?></label>
		                    <input type="number"
		                           id="refresh-interval"
		                           name="refresh_interval"
		                           value="<?php echo isset( $ultimeter_interval ) ? esc_attr( $ultimeter_interval ) : ''; ?>">
		                    <input id="submit" type="submit" value="<?php esc_attr_e( 'Save', 'ultimeter' ); ?>">
		                    <span id="settings-result" style="color: green; display: none; font-weight: 500;"><?php esc_html_e( 'Saved!', 'ultimeter' ); ?></span>
	                    </form>
                    </div>
                </div>
	            <div class="ultimeter-enterprise-card">
		            <div class="ultimeter-enterprise-card__header">
			            <div class="ultimeter-enterprise-card__title-wrapper">
				            <h2	class="ultimeter-enterprise-card__title ultimeter-enterprise-card__header-item">Latest News</h2>
			            </div>
		            </div>
		            <div class="ultimeter-enterprise-card__body">
			            <h4 class="">Stay tuned for the latest hints and tips, information on new integrations, knowledgebase highlights to get the most out of Ultimeter and some real-world examples of how Ultimeter is being used.</h4>
			            <div class="ultimeter-enterprise-news">
				            <p><strong>Latest News</strong></p>
				            <?php
				            ultimeter_render_admin_rss_feed(
				            	'https://ultimeter.app/tag/news/feed/',
				            	__( 'Nope. No news. But check back soon!', 'ultimeter' )
				            );
				            ?>
			            </div>

			            <div class="ultimeter-enterprise-hints">
				            <p><strong>Hints and Tips</strong></p>
				            <?php
				            ultimeter_render_admin_rss_feed(
				            	'https://ultimeter.app/tag/help/feed/',
				            	__( 'Nope. No hints, tips or new knowledgebase articles to show. But check back soon!', 'ultimeter' )
				            );
				            ?>
			            </div>
		            </div>
	            </div>
            </div>
            <div class="right">
                <div class="ultimeter-enterprise-card">
                    <div class="ultimeter-enterprise-card__header">
                        <div class="ultimeter-enterprise-card__title-wrapper"><h2
                                class="ultimeter-enterprise-card__title ultimeter-enterprise-card__header-item">
                                Latest Zapier Templates</h2></div>
                    </div>
                    <div class="ultimeter-enterprise-card__body">
                        <h4 class="">With Ultimeter Enterprise Edition and Zapier, you can connect to thousands of apps to control what your Ultimeter does. We will be showcasing some great examples here.</h4>
                        <p><a href="https://zapier.com/apps/ultimeter/integrations" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Browse Ultimeter Zapier templates', 'ultimeter' ); ?></a></p>
                    </div>
                </div>
            </div>


		</div>
	</div>
</div>

<div class="ultimeter-enterprise-dashboard-section">
	<div class="ultimeter-enterprise-section-header">
		<h2 class="ultimeter-enterprise-section-header__title ultimeter-enterprise-section-header__header-item">
			Status</h2>
		<hr role="presentation">
	</div>
	<div class="ultimeter-enterprise-dashboard">
		<div class="ultimeter-enterprise-dashboard__columns">
			<div class="ultimeter-enterprise-card ultimeter-enterprise-table ultimeter-enterprise-analytics__card ultimeter-enterprise-leaderboard">
				<div class="ultimeter-enterprise-card__header">
					<div class="ultimeter-enterprise-card__title-wrapper"><h2
								class="ultimeter-enterprise-card__title ultimeter-enterprise-card__header-item">REST
							API</h2></div>
				</div>
				<div class="ultimeter-enterprise-card__body">
					<h4 class=""><span class="dashicons <?php echo esc_attr( $ultimeter_ssl_light ); ?>"></span><?php echo esc_html( $ultimeter_ssl_header ); ?></h4>
					<p><?php echo esc_html( $ultimeter_ssl_desc ); ?></p>
				</div>
			</div>
			<div class="ultimeter-enterprise-card ultimeter-enterprise-table ultimeter-enterprise-analytics__card ultimeter-enterprise-leaderboard">
				<div class="ultimeter-enterprise-card__header">
					<div class="ultimeter-enterprise-card__title-wrapper"><h2
								class="ultimeter-enterprise-card__title ultimeter-enterprise-card__header-item">
							Authentication</h2></div>
				</div>
				<div class="ultimeter-enterprise-card__body">
					<h4 class=""><span class="dashicons <?php echo esc_attr( $ultimeter_ap_light ); ?>"></span><?php echo esc_html( $ultimeter_ap_header ); ?></h4>
					<p><?php echo esc_html( $ultimeter_ap_desc ); ?></p>
				</div>
			</div>
		</div>
	</div>
</div>
