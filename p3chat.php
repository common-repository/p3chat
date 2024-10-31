<?php
/*
Plugin Name: P3Chat
Plugin URI: http://sergey-s-betke.blogs.novgaro.ru/category/it/web/wordpress/p3chat
Description: This plugin provides support for p3chat service (online chat, offline messages) on Your wordpress website
Version: 1.2.1
Author: Sergey S. Betke
Author URI: http://sergey-s-betke.blogs.novgaro.ru/about
License: GPL2

Copyright 2011 Sergey S. Betke (email : sergey.s.betke@novgaro.ru)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function check_components_version(
	 $error_message
	,$min_php_version
	,$min_wp_version
) {	global $wp_version;
	if (
		   version_compare(phpversion(), $min_php_version, "<")
		|| version_compare($wp_version, $min_wp_version, "<")
	) {
		$pluginError = sprintf($error_message, $min_php_version, $min_wp_version);
		exit ($pluginError);
	};
}

if (defined('ABSPATH') && defined('WPINC')) {

	check_components_version(
		__('P3chat plugin requires WordPress %2$s and PHP %1$s or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>'),
		"5.0.0",
		"3.0"
	);

	register_activation_hook    ( __FILE__, array('p3chat', 'activation'        ));
	register_deactivation_hook  ( __FILE__, array('p3chat', 'deactivation'      ));
	register_uninstall_hook     ( __FILE__, array('p3chat', 'uninstall'         ));

	add_action('plugins_loaded', array('p3chat', 'plugins_loaded'));

};

class p3chat {

	private static $_name;
	private static $_namespace = __CLASS__;
	private static $_folder;
	private static $_domain;
	private static $_path;
	private static $_url;
	private static $options;

	public static function activation() {
	}

	public static function deactivation() {
		unregister_setting(
			self::$_namespace,
			self::$_namespace . '_options',
			array(__CLASS__, 'validate_options')
		);
	}

	public static function uninstall() {
		delete_option(self::$_namespace . '_options');
	}

	public static function plugins_loaded() {
		self::$_folder = dirname(plugin_basename(__FILE__));
		self::$_domain = self::$_folder;
		self::$_path = WP_PLUGIN_DIR . '/' . self::$_folder . '/';
		self::$_url = WP_PLUGIN_URL . '/' . self::$_folder . '/';

		add_action('init', array(__CLASS__, 'init'));
	}

	public static function init() {
		self::$options = self::validate_options(get_option(self::$_namespace . '_options'));
		load_plugin_textdomain(self::$_domain, false, self::$_folder . '/languages/');
		self::$_name = __('P3Chat', self::$_domain);

		add_action('admin_init', array(__CLASS__, 'admin_init'));
		add_action('admin_menu', array(__CLASS__, 'admin_menu'));

		if (!is_admin()) {
			$cssUrl = self::$_url . 'print.css';
			$cssFile = self::$_path . 'print.css';
			if ( file_exists($cssFile) ) {
				wp_register_style(self::$_namespace.'_print', $cssUrl, false, false, 'print');
				wp_enqueue_style(self::$_namespace.'_print');
			};
			$cssUrl = self::$_url . 'screen.css';
			$cssFile = self::$_path . 'screen.css';
			if ( file_exists($cssFile) ) {
				wp_register_style(self::$_namespace.'_screen', $cssUrl);
				wp_enqueue_style(self::$_namespace.'_screen');
			};

			wp_register_script(
				self::$_namespace.'_script',
				"http://p3chat.com/widget/uid/" . self::$options['UID'],
				false,
				false,
				(self::$options['code_location'] == 'footer')
			);
			wp_enqueue_script(self::$_namespace.'_script');

			add_shortcode('p3chat-button', array(__CLASS__, 'static_button_shortcode'));
		};
	}

	public static function validate_options($options) {
		if (!is_array($options)) {
			$options = array();
		};

		if ($options['code_location'] != 'head')
			$options['code_location'] = 'footer';

		return $options;
	}

	public static function admin_init() {
		register_setting(
			self::$_namespace,
			self::$_namespace . '_options',
			array(__CLASS__, 'validate_options')
		);

		add_settings_section(
			self::$_namespace . '_main_options',
			__('Main Settings', self::$_domain),
			array(__CLASS__, 'option_section_main'),
			self::$_namespace . '_options_page'
		);
		add_settings_field(
			self::$_namespace . '_options[UID]',
			__('p3chat UID', self::$_domain),
			array(__CLASS__, 'option_control_UID'),
			self::$_namespace . '_options_page',
			self::$_namespace . '_main_options'
		);

		add_settings_section(
			self::$_namespace . '_extra_options',
			__('Extra settings', self::$_domain),
			array(__CLASS__, 'option_section_extra'),
			self::$_namespace . '_options_page'
		);
		add_settings_field(
			self::$_namespace . '_options[code_location]',
			__('Code in the head', self::$_domain),
			array(__CLASS__, 'option_control_code_location'),
			self::$_namespace . '_options_page',
			self::$_namespace . '_extra_options'
		);
	}

	public static function option_section_main () {
	  ?>
		 <p>
			<?php _e('You must obtain UID from <a href="http://p3chat.com/signup">p3chat website</a> and set it in the UID field. Other options You must set on the p3chat site.', self::$_domain); ?>
		 </p>
	  <?php
	}

	public static function option_control_UID() {
	  ?>
			<input
				name="<?php echo self::$_namespace . '_options[UID]' ?>"
				type="text"
				maxlength="16"
				style="width: 100%"
				value="<?php echo self::$options['UID']; ?>"
			/>
			<br/><?php _e('Your UID.', self::$_domain); ?>
	  <?php
	}

	public static function option_section_extra () {
	  ?>
		 <p><?php _e('Additional settings for p3chat plugin.' , self::$_domain); ?></p>
	  <?php
	}

	public static function option_control_code_location() {
	?>
	   <div>
			<label>
				<input
					type="radio"
					name="<?php echo self::$_namespace . '_options[code_location]' ?>"
					value="head"
				<?php if (self::$options['code_location'] == 'head') { ?>
					checked="checked"
				<?php } ?>
				/>
				<?php _e('At the begin of pages (wp_head), when wp_footer isn`t used in the theme.', self::$_domain) ?>
			</label>
	   </div>
	   <div>
			<label>
				<input
					type="radio"
					name="<?php echo self::$_namespace . '_options[code_location]' ?>"
					value="footer"
				<?php if (self::$options['code_location'] != 'head') { ?>
					checked="checked"
				<?php } ?>
				/>
				<?php _e('At the end of pages (wp_footer), by default.', self::$_domain) ?>
			</label>
	   </div>
	<?php
	}

	public static function admin_menu() {
		add_options_page(
			__('P3Chat options', self::$_domain)
			, self::$_name
			, 'manage_options'
			, self::$_namespace . '_options_page'
			, array(__CLASS__, 'options_page')
		);
	}

	public static function options_page() {
		?>
		<div class="wrap">
			<?php screen_icon('options-general'); ?>
			<h2><?php _e('P3Chat options', self::$_domain) ?></h2>
			<form method="post" action="options.php">
				<?php
					settings_fields(self::$_namespace);
					do_settings_sections(self::$_namespace . '_options_page');
				?>
				<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>
		</div>
		<?php
	}

	public static function static_button_shortcode() {
		return
			'<div id="chat-launcher-c6be047eb0ae">'.
				'<img src="http://p3chat.com/widget/uid/'.self::$options['UID'].'/img/inline/" alt=""/>'.
			'</div>'
		;
	}

}
?>