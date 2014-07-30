<?php
/*
Plugin Name: Conditional Comments Message
Plugin URI: http://www.jimmyscode.com/wordpress/conditional-comments-message/
Description: Show a message when comments are set to close automatically
Version: 0.0.7
Author: Jimmy Pe&ntilde;a
Author URI: http://www.jimmyscode.com/
License: GPLv2 or later
*/
if (!defined('CCM_PLUGIN_NAME')) {
	// plugin constants
	define('CCM_PLUGIN_NAME', 'Conditional Comments Message');
	define('CCM_VERSION', '0.0.7');
	define('CCM_SLUG', 'conditional-comments-message');
	define('CCM_LOCAL', 'ccm');
	define('CCM_OPTION', 'ccm');
	define('CCM_OPTIONS_NAME', 'ccm_options');
	define('CCM_PERMISSIONS_LEVEL', 'manage_options');
	define('CCM_PATH', plugin_basename(dirname(__FILE__)));
	/* default values */
	define('CCM_DEFAULT_ENABLED', true);
	define('CCM_DEFAULT_TEXT', 'The comment form will be available for %NUMDAYS% days from the date the article was published.');
	define('CCM_DEFAULT_CLOSED_TEXT', 'This article is closed to any future comments.');
	define('CCM_DEFAULT_DYNAMIC_CLOSE_TIME', false);
	/* option array member names */
	define('CCM_DEFAULT_ENABLED_NAME', 'enabled');
	define('CCM_DEFAULT_TEXT_NAME', 'texttoshow');
	define('CCM_DEFAULT_CLOSED_TEXT_NAME', 'closedtext');
	define('CCM_DEFAULT_DYNAMIC_CLOSE_TIME_NAME', 'dynamictime');
}
	// oh no you don't
	if (!defined('ABSPATH')) {
		wp_die(__('Do not access this file directly.', ccm_get_local()));
	}

	// localization to allow for translations
	add_action('init', 'ccm_translation_file');
	function ccm_translation_file() {
		$plugin_path = ccm_get_path() . '/translations';
		load_plugin_textdomain(ccm_get_local(), '', $plugin_path);
	}
	// tell WP that we are going to use new options
	// also, register the admin CSS file for later inclusion
	add_action('admin_init', 'ccm_options_init');
	function ccm_options_init() {
		register_setting(CCM_OPTIONS_NAME, ccm_get_option(), 'ccm_validation');
		register_ccm_admin_style();
	}
	// validation function
	function ccm_validation($input) {
		// validate all form fields
		if (!empty($input)) {
			$input[CCM_DEFAULT_ENABLED_NAME] = (bool)$input[CCM_DEFAULT_ENABLED_NAME];
			$input[CCM_DEFAULT_TEXT_NAME] = wp_kses_post(force_balance_tags($input[CCM_DEFAULT_TEXT_NAME]));
			$input[CCM_DEFAULT_CLOSED_TEXT_NAME] = wp_kses_post(force_balance_tags($input[CCM_DEFAULT_CLOSED_TEXT_NAME]));
			$input[CCM_DEFAULT_DYNAMIC_CLOSE_TIME_NAME] = (bool)$input[CCM_DEFAULT_DYNAMIC_CLOSE_TIME_NAME];
		}
		return $input;
	} 

	// add Settings sub-menu
	add_action('admin_menu', 'ccm_plugin_menu');
	function ccm_plugin_menu() {
		add_options_page(CCM_PLUGIN_NAME, CCM_PLUGIN_NAME, CCM_PERMISSIONS_LEVEL, ccm_get_slug(), 'ccm_page');
	}
	// plugin settings page
	// http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
	function CCM_page() {
		// check perms
		if (!current_user_can(CCM_PERMISSIONS_LEVEL)) {
			wp_die(__('You do not have sufficient permission to access this page', ccm_get_local()));
		}
		?>
		<div class="wrap">
			<h2 id="plugintitle"><img src="<?php echo ccm_getimagefilename('comment_message.png'); ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php echo CCM_PLUGIN_NAME; _e(' by ', ccm_get_local()); ?><a href="http://www.jimmyscode.com/">Jimmy Pe&ntilde;a</a></h2>
			<div><?php _e('You are running plugin version', ccm_get_local()); ?> <strong><?php echo CCM_VERSION; ?></strong>.</div>

			<?php /* http://code.tutsplus.com/tutorials/the-complete-guide-to-the-wordpress-settings-api-part-5-tabbed-navigation-for-your-settings-page--wp-24971 */ ?>
			<?php $active_tab = (isset($_GET['tab']) ? $_GET['tab'] : 'settings'); ?>

			<h2 class="nav-tab-wrapper">
			  <a href="?page=<?php echo ccm_get_slug(); ?>&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', ccm_get_local()); ?></a>
				<a href="?page=<?php echo ccm_get_slug(); ?>&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>"><?php _e('Support', ccm_get_local()); ?></a>
			</h2>

			<form method="post" action="options.php">
			<?php settings_fields(CCM_OPTIONS_NAME); ?>
			<?php $options = ccm_getpluginoptions(); ?>
			<?php update_option(ccm_get_option(), $options); ?>
			<?php if ($active_tab == 'settings') { ?>
			<h3 id="settings"><img src="<?php echo ccm_getimagefilename('settings.png'); ?>" title="" alt="" height="61" width="64" align="absmiddle" /> <?php _e('Plugin Settings', ccm_get_local()); ?></h3>
				<table class="form-table" id="theme-options-wrap">
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Is plugin enabled? Uncheck this to turn it off temporarily.', ccm_get_local()); ?>" for="<?php echo ccm_get_option(); ?>[<?php echo CCM_DEFAULT_ENABLED_NAME; ?>]"><?php _e('Plugin enabled?', ccm_get_local()); ?></label></strong></th>
						<td><input type="checkbox" id="<?php echo ccm_get_option(); ?>[<?php echo CCM_DEFAULT_ENABLED_NAME; ?>]" name="<?php echo ccm_get_option(); ?>[<?php echo CCM_DEFAULT_ENABLED_NAME; ?>]" value="1" <?php checked('1', ccm_checkifset(CCM_DEFAULT_ENABLED_NAME, CCM_DEFAULT_ENABLED, $options)); ?> /></td>
					</tr>
					<?php ccm_explanationrow(__('Is plugin enabled? Uncheck this to turn it off temporarily.', ccm_get_local())); ?>
					<?php ccm_getlinebreak(); ?>
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Enter open comments message', ccm_get_local()); ?>" for="<?php echo ccm_get_option(); ?>[<?php echo CCM_DEFAULT_TEXT_NAME; ?>]"><?php _e('Enter open comments message', ccm_get_local()); ?></label></strong></th>
						<td><textarea rows="12" cols="75" id="<?php echo ccm_get_option(); ?>[<?php echo CCM_DEFAULT_TEXT_NAME; ?>]" name="<?php echo ccm_get_option(); ?>[<?php echo CCM_DEFAULT_TEXT_NAME; ?>]"><?php echo ccm_checkifset(CCM_DEFAULT_TEXT_NAME, CCM_DEFAULT_TEXT, $options); ?></textarea></td>
					</tr>
					<?php ccm_explanationrow(__('Enter the message you want users to see while the comment period is open. Go to <a href="' . admin_url() . 'options-discussion.php">Discussion</a> settings to configure auto-closing comment periods.<br />Template tag <strong>%NUMDAYS%</strong> is for the # of days before comments automatically close.', ccm_get_local())); ?>
					<?php ccm_getlinebreak(); ?>
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Show dynamic message instead', ccm_get_local()); ?>" for="<?php echo ccm_get_option(); ?>[<?php echo CCM_DEFAULT_DYNAMIC_CLOSE_TIME_NAME; ?>]"><?php _e('Show dynamic message instead', ccm_get_local()); ?></label></strong></th>
						<td><input type="checkbox" id="<?php echo ccm_get_option(); ?>[<?php echo CCM_DEFAULT_DYNAMIC_CLOSE_TIME_NAME; ?>]" name="<?php echo ccm_get_option(); ?>[<?php echo CCM_DEFAULT_DYNAMIC_CLOSE_TIME_NAME; ?>]" value="1" <?php checked('1', ccm_checkifset(CCM_DEFAULT_DYNAMIC_CLOSE_TIME_NAME, CCM_DEFAULT_DYNAMIC_CLOSE_TIME, $options)); ?> /></td>
					</tr>
					<?php ccm_explanationrow(__('Instead of the above message, show a message which dynamically changes the remaining days. Ex: 30 days, then 29 days, 28, 27, etc', ccm_get_local())); ?>
					<?php ccm_getlinebreak(); ?>
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Enter closed comments message', ccm_get_local()); ?>" for="<?php echo ccm_get_option(); ?>[<?php echo CCM_DEFAULT_CLOSED_TEXT_NAME; ?>]"><?php _e('Enter closed comments message', ccm_get_local()); ?></label></strong></th>
						<td><textarea rows="12" cols="75" id="<?php echo ccm_get_option(); ?>[<?php echo CCM_DEFAULT_CLOSED_TEXT_NAME; ?>]" name="<?php echo ccm_get_option(); ?>[<?php echo CCM_DEFAULT_CLOSED_TEXT_NAME; ?>]"><?php echo ccm_checkifset(CCM_DEFAULT_CLOSED_TEXT_NAME, CCM_DEFAULT_CLOSED_TEXT, $options); ?></textarea></td>
					</tr>
					<?php ccm_explanationrow(__('Enter the message you want users to see when the comments period is closed.', ccm_get_local())); ?>
					</table>
				<?php submit_button(); ?>
			<?php } else { ?>
			<h3 id="support"><img src="<?php echo ccm_getimagefilename('support.png'); ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php _e('Support', ccm_get_local()); ?></h3>
				<div class="support">
				<?php echo ccm_getsupportinfo(ccm_get_slug(), ccm_get_local()); ?>
				</div>
			<?php } ?>
			</form>
		</div>
		<?php }

	// main function and action
	add_action('comment_form_after', 'ccm_commentsclosingmsg'); // comment_form_comments_closed
  function ccm_commentsclosingmsg() {
		$options = ccm_getpluginoptions();
		$enabled = (bool)$options[CCM_DEFAULT_ENABLED_NAME];
		$output = '';
		
		if ($enabled) {
			if (get_option('close_comments_for_old_posts')) { // the checkbox!!!!
				$numdays = (int)get_option('close_comments_days_old'); 
				if ($numdays !== 0) { // comments are set to close in >0 days
					if (comments_open()) {
						$dynamictime = (bool)$options[CCM_DEFAULT_DYNAMIC_CLOSE_TIME_NAME];
						if (!$dynamictime) {
							// replace template tags
							$output = sanitize_text_field($options[CCM_DEFAULT_TEXT_NAME]); // do we need to sanitize here????
							$output = str_replace('%NUMDAYS%', $numdays, $output);
						} else { // show dynamically changing # of days
							// http://wpengineer.com/2692/inform-user-about-automatic-comment-closing-time/
							global $post;
							$expires = strtotime( "{$post->post_date_gmt} GMT" ) +  $numdays * DAY_IN_SECONDS;
							$output = printf(__( '(This topic will automatically close in %s. )', ccm_get_local()),  human_time_diff($expires));
						}
					} else { // they are closed
						$output = sanitize_text_field($options[CCM_DEFAULT_CLOSED_TEXT_NAME]);
					}
				}
			}
			if ($output !== '') {
				ccm_show_output($output);
			}
		}
	}
	add_action('comment_form_comments_closed', 'ccm_show_closed');
	function ccm_show_closed() {
		$options = ccm_getpluginoptions();
		$enabled = (bool)$options[CCM_DEFAULT_ENABLED_NAME];
		$output = '';
		
		if ($enabled) {
			if (get_option('close_comments_for_old_posts')) { // the checkbox!!!!
				$numdays = (int)get_option('close_comments_days_old'); 
				if ($numdays !== 0) { // comments are set to close in >0 days
					$output = sanitize_text_field($options[CCM_DEFAULT_CLOSED_TEXT_NAME]);
					if ($output !== '') {
						ccm_show_output($output);
					}
				}
			}
		}
	}
	
	function ccm_show_output($msg) {
		echo '<div class="ccm-conditional-msg">' . $msg . '</div>';
	}
	
	// show admin messages to plugin user
	add_action('admin_notices', 'ccm_showAdminMessages');
	function ccm_showAdminMessages() {
		// http://wptheming.com/2011/08/admin-notices-in-wordpress/
		global $pagenow;
		if (current_user_can(CCM_PERMISSIONS_LEVEL)) { // user has privilege
			if ($pagenow == 'options-general.php') { // we are on Settings menu
				if (isset($_GET['page'])) {
					if ($_GET['page'] == ccm_get_slug()) { // we are on this plugin's settings page
						$options = ccm_getpluginoptions();
						if (!empty($options)) {
							$enabled = (bool)$options[CCM_DEFAULT_ENABLED_NAME];
							if (!$enabled) {
								echo '<div id="message" class="error">' . CCM_PLUGIN_NAME . ' ' . __('is currently disabled.', ccm_get_local()) . '</div>';
							}
						}
					}
				}
			} // end page check
		} // end privilege check
	} // end admin msgs function
	// enqueue admin CSS if we are on the plugin options page
	add_action('admin_head', 'insert_ccm_admin_css');
	function insert_ccm_admin_css() {
		global $pagenow;
		if (current_user_can(CCM_PERMISSIONS_LEVEL)) { // user has privilege
			if ($pagenow == 'options-general.php') { // we are on Settings menu
				if (isset($_GET['page'])) {
					if ($_GET['page'] == ccm_get_slug()) { // we are on this plugin's settings page
						ccm_admin_styles();
					}
				}
			}
		}
	}
	// add helpful links to plugin page next to plugin name
	// http://bavotasan.com/2009/a-settings-link-for-your-wordpress-plugins/
	// http://wpengineer.com/1295/meta-links-for-wordpress-plugins/
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'ccm_plugin_settings_link');
	add_filter('plugin_row_meta', 'ccm_meta_links', 10, 2);
	
	function ccm_plugin_settings_link($links) {
		return ccm_settingslink($links, ccm_get_slug(), ccm_get_local());
	}
	function ccm_meta_links($links, $file) {
		if ($file == plugin_basename(__FILE__)) {
			$links = array_merge($links,
			array(
				sprintf(__('<a href="http://wordpress.org/support/plugin/%s">Support</a>', ccm_get_local()), ccm_get_slug()),
				sprintf(__('<a href="http://wordpress.org/extend/plugins/%s/">Documentation</a>', ccm_get_local()), ccm_get_slug()),
				sprintf(__('<a href="http://wordpress.org/plugins/%s/faq/">FAQ</a>', ccm_get_local()), ccm_get_slug())
			));
		}
		return $links;	
	}
	// enqueue/register the admin CSS file
	function ccm_admin_styles() {
		wp_enqueue_style('ccm_admin_style');
	}
	function register_ccm_admin_style() {
		wp_register_style('ccm_admin_style',
			plugins_url(ccm_get_path() . '/css/admin.css'),
			array(),
			CCM_VERSION . "_" . date('njYHis', filemtime(dirname(__FILE__) . '/css/admin.css')),
			'all');
	}
	// when plugin is activated, create options array and populate with defaults
	register_activation_hook(__FILE__, 'ccm_activate');
	function ccm_activate() {
		$options = ccm_getpluginoptions();
		update_option(ccm_get_option(), $options);
		
		// delete option when plugin is uninstalled
		register_uninstall_hook(__FILE__, 'uninstall_ccm_plugin');
	}
	function uninstall_ccm_plugin() {
		delete_option(ccm_get_option());
	}
		
	// generic function that returns plugin options from DB
	// if option does not exist, returns plugin defaults
	function ccm_getpluginoptions() {
		return get_option(ccm_get_option(), 
			array(
				CCM_DEFAULT_ENABLED_NAME => CCM_DEFAULT_ENABLED, 
				CCM_DEFAULT_TEXT_NAME => CCM_DEFAULT_TEXT,
				CCM_DEFAULT_CLOSED_TEXT_NAME => CCM_DEFAULT_CLOSED_TEXT,
				CCM_DEFAULT_DYNAMIC_CLOSE_TIME_NAME => CCM_DEFAULT_DYNAMIC_CLOSE_TIME
			));
	}
	
// encapsulate these and call them throughout the plugin instead of hardcoding the constants everywhere
	function ccm_get_slug() { return CCM_SLUG; }
	function ccm_get_local() { return CCM_LOCAL; }
	function ccm_get_option() { return CCM_OPTION; }
	function ccm_get_path() { return CCM_PATH; }
	
	function ccm_settingslink($linklist, $slugname = '', $localname = '') {
		$settings_link = sprintf( __('<a href="options-general.php?page=%s">Settings</a>', $localname), $slugname);
		array_unshift($linklist, $settings_link);
		return $linklist;
	}
	function ccm_getsupportinfo($slugname = '', $localname = '') {
		$output = __('Do you need help with this plugin? Check out the following resources:', $localname);
		$output .= '<ol>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/extend/plugins/%s/">Documentation</a>', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/plugins/%s/faq/">FAQ</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/support/plugin/%s">Support Forum</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://www.jimmyscode.com/wordpress/%s">Plugin Homepage / Demo</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/extend/plugins/%s/developers/">Development</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/plugins/%s/changelog/">Changelog</a><br />', $localname), $slugname) . '</li>';
		$output .= '</ol>';
		
		$output .= sprintf( __('If you like this plugin, please <a href="http://wordpress.org/support/view/plugin-reviews/%s/">rate it on WordPress.org</a>', $localname), $slugname);
		$output .= sprintf( __(' and click the <a href="http://wordpress.org/plugins/%s/#compatibility">Works</a> button. ', $localname), $slugname);
		$output .= '<br /><br /><br />';
		$output .= __('Your donations encourage further development and support. ', $localname);
		$output .= '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7EX9NB9TLFHVW"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate with PayPal" title="Support this plugin" width="92" height="26" /></a>';
		$output .= '<br /><br />';
		return $output;		
	}
	function ccm_checkifset($optionname, $optiondefault, $optionsarr) {
		return (isset($optionsarr[$optionname]) ? $optionsarr[$optionname] : $optiondefault);
	}
	function ccm_getlinebreak() {
	  echo '<tr valign="top"><td colspan="2"></td></tr>';
	}
	function ccm_explanationrow($msg = '') {
		echo '<tr valign="top"><td></td><td><em>' . $msg . '</em></td></tr>';
	}
	function ccm_getimagefilename($fname = '') {
		return plugins_url(ccm_get_path() . '/images/' . $fname);
	}
?>