<?php
if (!defined('ABSPATH') || !defined('WP_UNINSTALL_PLUGIN')) {
  exit();
}

function ure_delete_options() {
	delete_option('widget_social_widget');
	delete_option('dsw_settings_options');
}
?>