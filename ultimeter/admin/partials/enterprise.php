<?php

if ( is_ssl() ) {
	$ssl_light  = 'ultimeter-status-green dashicons-yes';
	$ssl_header = 'SSL is enabled';
	$ssl_desc   = 'SSL helps to keep your data private, by encrypting any data between a user and a web server. Congratulations.';
} else {
	$ssl_light  = 'ultimeter-status-red dashicons-no';
	$ssl_header = 'SSL is disabled';
	$ssl_desc   = 'SSL helps to keep your data private, by encrypting any data between a user and a web server. Some services that want to talk to Ultimeter may not let you until SSL is enabled.';
}

if ( wp_is_application_passwords_available() ) {
	$ap_light  = 'ultimeter-status-green dashicons-yes';
	$ap_header = 'Application Passwords are enabled';
	$ap_desc   = 'Use Application Passwords to authenticate users without providing their passwords directly. Instead, a unique password is generated for each application without revealing the user’s main password.';
} else {
	$ap_light  = 'ultimeter-status-red dashicons-no';
	$ap_header = 'Application Passwords are disabled';
	$ap_desc   = 'Use Application Passwords to authenticate users without providing their passwords directly. Instead, a unique password is generated for each application without revealing the user’s main password. Some services that want to talk to Ultimeter will require an Application Password. You may have a security plugin that has turned this service off.';
}


$interval = get_option('ultimeter_refresh_interval');
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
	                    <form method="post" id="ultimeter-enterprise-settings" data-nonce="<?php echo wp_create_nonce('ultimeter_enterprise_settings') ?>">
		                    <label for="refresh-interval"><?php esc_html_e( 'Refresh Interval (seconds)', 'ultimeter'); ?></label>
		                    <input type="number"
		                           id="refresh-interval"
		                           name="refresh_interval"
		                           value="<?php echo isset( $interval ) ? esc_attr( $interval ) : '' ?>">
		                    <input id="submit" type="submit" value="<?php esc_attr_e('Save', 'ultimeter'); ?>">
		                    <span id="settings-result" style="color: green; display: none; font-weight: 500;"><?php esc_html_e('Saved!'); ?></span>
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
				            // Get RSS Feed(s)
				            require_once ABSPATH . WPINC . '/feed.php';

				            // Get a SimplePie feed object from the specified feed source.
				            $rss = fetch_feed( 'https://ultimeter.app/tag/news/feed/ ' );

				            $maxitems = 0;

				            if ( ! is_wp_error( $rss ) ) { // Checks that the object is created correctly

					            // Figure out how many total items there are, but limit it to 5.
					            $maxitems = $rss->get_item_quantity( 5 );

					            // Build an array of all the items, starting with element 0 (first element).
					            $rss_items = $rss->get_items( 0, $maxitems );

				            }

				            if ( $maxitems == 0 ) {
					            echo '<p>Nope. No news. But check back soon!</p>';
				            } else {
					            foreach ( $rss_items as $item ) {
						            $url   = esc_url( $item->get_permalink() );
						            $title = esc_html( $item->get_title() );
						            echo "<p><a href='{$url}' style='text-decoration: none;' target='_blank'>{$title}</a></p>";
					            }
				            }
				            ?>

			            </div>

			            <div class="ultimeter-enterprise-hints">
				            <p><strong>Hints and Tips</strong></p>

				            <?php
				            // Get RSS Feed(s)
				            require_once ABSPATH . WPINC . '/feed.php';

				            // Get a SimplePie feed object from the specified feed source.
				            $rss = fetch_feed( 'https://ultimeter.app/tag/help/feed/ ' );

				            $maxitems = 0;

				            if ( ! is_wp_error( $rss ) ) { // Checks that the object is created correctly

					            // Figure out how many total items there are, but limit it to 5.
					            $maxitems = $rss->get_item_quantity( 5 );

					            // Build an array of all the items, starting with element 0 (first element).
					            $rss_items = $rss->get_items( 0, $maxitems );

				            }

				            if ( $maxitems == 0 ) {
					            echo '<p>Nope. No hints, tips or new knowledgebase articles to show. But check back soon!</p>';
				            } else {
					            foreach ( $rss_items as $item ) {
						            $url   = esc_url( $item->get_permalink() );
						            $title = esc_html( $item->get_title() );
						            echo "<p><a href='{$url}' style='text-decoration: none;' target='_blank'>{$title}</a></p>";
					            }
				            }
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
                        <p><script src="https://zapier.com/apps/embed/widget.js?services=ultimeter&limit=10"></script></p>
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
					<h4 class=""><span class="dashicons <?php echo $ssl_light; ?>"></span><?php echo $ssl_header; ?></h4>
					<p><?php echo $ssl_desc; ?></p>
				</div>
			</div>
			<div class="ultimeter-enterprise-card ultimeter-enterprise-table ultimeter-enterprise-analytics__card ultimeter-enterprise-leaderboard">
				<div class="ultimeter-enterprise-card__header">
					<div class="ultimeter-enterprise-card__title-wrapper"><h2
								class="ultimeter-enterprise-card__title ultimeter-enterprise-card__header-item">
							Authentication</h2></div>
				</div>
				<div class="ultimeter-enterprise-card__body">
					<h4 class=""><span class="dashicons <?php echo $ap_light; ?>"></span><?php echo $ap_header; ?></h4>
					<p><?php echo $ap_desc; ?></p>
				</div>
			</div>
		</div>
	</div>
</div>
