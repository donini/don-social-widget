<?php
/**
 * Plugin Name:     Don Social Widget
 * Plugin URI:      https://br.wordpress.org/plugins/don-social-widget
 * Description:     Print social icons in widgets
 * Author:          Rodrigo Donini
 * Author URI:      https://profiles.wordpress.org/rodrigodonini
 * Text Domain:     don-social-widget
 * Domain Path:     /languages
 * Version:         0.1.2
 *
 * @package         Don_Social_Widget
 */
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

require_once (dirname(__FILE__) . '/dsw_settings.php');

class Widget_Social_Helper  extends WP_Widget{
	/* Saved options */
	public $options;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'social_widget', 
			__( 'Social Widget: Follow us', 'don-social-widget' ), 
			array( 'description' => __( 'Add links of your social networks', 'text_domain' ), ) 
			);
		add_action('plugins_loaded', array( $this, 'dsw_load_textdomain' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$this->options = get_option( 'dsw_settings_options' );

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		if ( ! empty( $instance['dsw_social_networks'] ) ) {
			$nets = $this->list_social_networks();
			$nets_urls = $this->list_social_networks_urls();
			echo '<style> 
			.dsw-item-row:first-child { margin-left: 0; }
			.dsw-item-row:last-child { margin-right: 0; }
			.dsw-item-row { margin: 5px; display: ' . $this->options['orientation']. '; } 
			.dsw-widget-front-item { text-decoration: none; font-size: ' . $this->options['icon_size'] . 'px; color: ' . $this->options['icon_color'] . '; } 
			.dsw-widget-front-item i { display: inline-block; min-width: 30px; text-align: center; }
			</style>';
			echo '<ul class="dsw-list">';
			foreach ($instance['dsw_social_networks'] as $key => $value) {
				if (!empty($value)) {
					$net_name = ($this->options['show_social_name'] == 'true') ? $nets[$key] : '';
					echo '<li class="dsw-item-row">';
						echo '<a class="dsw-widget-front-item" target="' . $this->options['target'] . '" href="' . sprintf($nets_urls[$key], $value) . '"><i class="fa fa-' . $key . '"></i> <span class="text">'. $net_name . '</span></a>';
					echo '</li>';
				}
			}
			echo '</ul>';
		}
		echo $args['after_widget'];
	}

	public function list_social_networks_urls() {
		$urls = array (
			'facebook' => 'https://www.facebook.com/%s',
			'flickr' => 'https://www.flickr.com/people/%s',
			'github' => 'https://github.com/%s',
			'google-plus' => 'https://plus.google.com/u/0/%s',
			'instagram' => 'https://www.instagram.com/%s',
			'linkedin' => 'https://br.linkedin.com/in/%s',
			'medium' => 'https://medium.com/@%s',
			'pinterest' => 'https://www.pinterest.com/%s',
			'slack' => 'https://%s.slack.com/',
			'slideshare' => 'www.slideshare.net/%s',
			'soundcloud' => 'http://soundcloud.com/%s',
			'spotify' => 'https://open.spotify.com/user/%s',
			'tumblr' => 'http://%s.tumblr.com',
			'twitter' => 'http://twitter.com/%s',
			'vimeo' => 'https://vimeo.com/channels/%s',
			'youtube' => 'https://www.youtube.com/user/%s',
			);
		return $urls;
	}

	public function list_social_networks() {
		$nets = array (
			'facebook' => 'Facebook',
			'flickr' => 'Flickr',
			'github' => 'GitHub',
			'google-plus' => 'Google Plus',
			'instagram' => 'Instagram',
			'linkedin' => 'Linkedin',
			'medium' => 'Medium',
			'pinterest' => 'Pinterest',
			'slack' => 'Slack',
			'slideshare' => 'Slideshare',
			'soundcloud' => 'Soundcloud',
			'spotify' => 'Spotify',
			'tumblr' => 'Tumblr',
			'twitter' => 'Twitter',
			'vimeo' => 'Vimeo',
			'youtube' => 'Youtube',
			);
		return $nets;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$nets = $this->list_social_networks();
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Follow us', 'text_domain' );
		$dsw_social_networks = ! empty( $instance['dsw_social_networks'] ) ? $instance['dsw_social_networks'] : '';
		$fields_counter = 0;
		$show_field = 'none';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<fieldset class="dsw-fielset">
			<legend><?php echo __('List of social networks:', 'text_domain')?></legend>
				<?php foreach ($nets as $key => $value): 
				$field_value = esc_attr( $dsw_social_networks[$key] );
				$show_field = ! empty($field_value) ? 'dsw-item-show' : '';
				?>
				<p class="dsw-social-item-<?php echo $key; ?> dsw-item <?php echo $show_field;?>" >
					<label for="<?php echo $this->get_field_name( 'dsw_social_networks' )?>[<?php echo $key; ?>]"><i class="fa fa-<?php echo $key; ?>"></i> <?php echo $value; ?> <a href="#remove" class="dsw-btn-remove" title="<?php echo __('Add', 'text_domain');?>"><?php echo __('Remove', 'text_domain');?></a></label>
					<input class="widefat dsw-field-text" id="<?php echo $this->get_field_name( 'dsw_social_networks' )?>[<?php echo $key; ?>]" name="<?php echo $this->get_field_name( 'dsw_social_networks' )?>[<?php echo $key; ?>]" type="text" value="<?php echo $field_value; ?>">
				</p>
				<?php 
				$fields_counter += 1;
				endforeach; ?>
				<p class="dsw-no-data"><?php echo __('Select and add a social network.', 'text_domain');?></p>
		</fieldset>
		<p>
			<select name="dsw_settings_options-social-networks" class="dsw-select-add-network">
			<?php foreach ($nets as $key => $value): ?>
				<option value="<?php echo $key;?>"><?php echo $value;?></option>
			<?php endforeach; ?>
			</select>
			<a href="#add" class="dsw-btn dsw-btn-add"><?php echo __('Add', 'text_domain');?></a>
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        $instance['dsw_social_networks'] = array();

        if ( isset ( $new_instance['dsw_social_networks'] ) )
        {
        	// print_r($new_instance['dsw_social_networks']);die;
            foreach ( $new_instance['dsw_social_networks'] as $key => $value )
            {
                $instance['dsw_social_networks'][$key] = $value;
            }
        }

		return $instance;
	}

	function dsw_load_textdomain() {
		load_plugin_textdomain( 'don-social-widget', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
	}
}
add_action( 'widgets_init', function(){
	register_widget( 'Widget_Social_Helper' );
});
?>
