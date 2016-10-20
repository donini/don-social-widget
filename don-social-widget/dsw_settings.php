<?php
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
class DSW_Widget_Options {
  
    /*--------------------------------------------*
     * Attributes
     *--------------------------------------------*/
  
    /** Refers to a single instance of this class. */
    private static $instance = null;
     
    /* Saved options */
    public $options;
  
    /*--------------------------------------------*
     * Constructor
     *--------------------------------------------*/
  
    /**
     * Creates or returns an instance of this class.
     *
     * @return  DSW_Widget_Options A single instance of this class.
     */
    public static function get_instance() {
  
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
  
        return self::$instance;
  
    } // end get_instance;
  
    /**
     * Initializes the plugin by setting localization, filters, and administration functions.
     */
    private function __construct() {
        // Add the page to the admin menu
        add_action( 'admin_menu', array( &$this, 'add_page' ) );

        // Register page options
        add_action( 'admin_init', array( &$this, 'register_page_options') );

        // Css rules for Color Picker
        wp_enqueue_style( 'wp-color-picker' );

        // Register javascript
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_js' ) );

        // Add settings link to plugins page
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'dsw_add_action_links' ), 10, 1 );

        // Get registered option
        $this->options = get_option( 'dsw_settings_options' );
    }
  
    /*--------------------------------------------*
     * Functions
     *--------------------------------------------*/
    /*
     * Function that add setting buttom on plugin menu
     */
    public function dsw_add_action_links ( $links ) {
         $settingslinks = array(
            '<a href="' . admin_url( 'options-general.php?page=dsw_settings_options' ) . '">Settings</a>',
         );
        return array_merge( $settingslinks, $links );
    }

    /**
     * Function that will add the options page under Setting Menu.
     */
    public function add_page() { 
        // $page_title, $menu_title, $capability, $menu_slug, $callback_function
        add_options_page( __('Social Widget', 'text_domain'), __('Social Widget', 'text_domain'), 'manage_options', 'dsw_settings_options', array( $this, 'display_page' ) );
    }

    /**
     * Function that will display the options page.
     */
    public function display_page() { 
        ?>
        <div class="wrap">
            <h2><?php echo __('Social Widget Options', 'text_domain');?></h2>
            <form method="post" action="options.php">     
                <?php 
                settings_fields('dsw_settings_options');
                do_settings_sections('dsw_settings_options');
                submit_button();
                ?>
            </form>
        </div> <!-- /wrap -->
        <?php    
    }

    /**
     * Function that will register admin page options.
     */
    public function register_page_options() { 

        // Add Section for option fields
        add_settings_section( 'dsw_section', __('Widget Options', 'text_domain'), array( $this, 'display_section' ), 'dsw_settings_options' ); // id, title, display cb, page

        // Add icon size
        add_settings_field( 'dsw_icon_size', __('Icon Size', 'text_domain'), array( $this, 'dsw_icon_size_settings_field' ), 'dsw_settings_options', 'dsw_section' ); 

        // Add color icons
        add_settings_field( 'dsw_icon_color', __('Icon color', 'text_domain'), array( $this, 'dsw_icon_color_settings_field' ), 'dsw_settings_options', 'dsw_section' ); 

        // Add show social name
        add_settings_field( 'dsw_show_social_name', __('Show social name', 'text_domain'), array( $this, 'dsw_show_social_name_settings_field' ), 'dsw_settings_options', 'dsw_section' ); 

        // Add show social name
        add_settings_field( 'dsw_target', __('Target link', 'text_domain'), array( $this, 'dsw_target_settings_field' ), 'dsw_settings_options', 'dsw_section' ); 

        // Add show orientation
        add_settings_field( 'dsw_orientation', __('Orientation', 'text_domain'), array( $this, 'dsw_orientation_settings_field' ), 'dsw_settings_options', 'dsw_section' ); 

        // Register Settings
        register_setting( 'dsw_settings_options', 'dsw_settings_options', array( $this, 'validate_options' ) );
    }

    /**
     * Functions that display the fields.
     */
    public function dsw_icon_size_settings_field() { 
        $maxvalues = array(
            '12' => __('Smallest', 'text_domain'),
            '16' => __('Small', 'text_domain'),
            '20' => __('Medium', 'text_domain'),
            '30' => __('Large', 'text_domain'),
            '50' => __('Huge', 'text_domain')
                ); 
            $val = ( isset( $this->options['icon_size'] ) ) ? $this->options['icon_size'] : '';
            echo '<select name="dsw_settings_options[icon_size]">';
            foreach($maxvalues as $key => $value){
                echo '<option value="'.$key.'" '.selected( $key, $val, false).'>'.$value.'</option>';
            }
            echo '</select>';
    }

    public function dsw_icon_color_settings_field() { 
        $val = ( isset( $this->options['icon_color'] ) ) ? $this->options['icon_color'] : '';
        echo '<input type="text" name="dsw_settings_options[icon_color]" value="' . $val . '" class="dsw-color-picker" >';
    }

    public function dsw_show_social_name_settings_field() {
        $val = ( isset( $this->options['show_social_name'] ) ) ? $this->options['show_social_name'] : '';
        echo '<input type="checkbox" name="dsw_settings_options[show_social_name]" value="true" '. checked('true', $val, false).' />';
    }

    public function dsw_target_settings_field() {
        $target_values = array(
            '_blank' => __('New Tab', 'text_domain'),
            '_window' => __('New Window', 'text_domain'),
            '_parent' => __('Parent Window', 'text_domain')
            ); 
        $val = ( isset( $this->options['target'] ) ) ? $this->options['target'] : '';
        echo '<select name="dsw_settings_options[target]" id="dsw_settings_options[target]">';
        foreach($target_values as $key => $value){
            echo '<option value="'.$key.'" '.selected( $key, $val, false ).'>'.$value.'</option>';
        }
        echo '</select>';
    }

    public function dsw_orientation_settings_field() {
        $orientations = array(
            'inline-block' => __('Horizontal', 'text_domain'),
            'block' => __('Vertical', 'text_domain')
                ); 
            $val = ( isset( $this->options['orientation'] ) ) ? $this->options['orientation'] : '';
            echo '<select name="dsw_settings_options[orientation]">';
            foreach($orientations as $key => $value){
                echo '<option value="'.$key.'" '.selected( $key, $val, false).'>'.$value.'</option>';
            }
            echo '</select>';
    }

    /**
     * Function that will add javascript file for Color Piker.
     */
    public function enqueue_admin_js() { 
        // Make sure to add the wp-color-picker dependecy to js file
        wp_enqueue_script( 'dsw_color_picker', plugins_url( 'assets/js/dsw-color-picker.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), '', true  );
        wp_enqueue_script( 'dsw_main', plugins_url( 'assets/js/dsw-main.js', __FILE__ ), array( 'jquery' ), '', true  );
        wp_enqueue_style( 'dsw_styles', plugins_url( 'assets/css/don-social-widget.css', __FILE__ ) );
        wp_enqueue_style( 'font-awesome', plugins_url( 'assets/css/font-awesome/css/font-awesome.css', __FILE__ ) );
    }

    /**
     * Function that will validate all fields.
     */
    public function validate_options( $fields ) {
        $valid_fields = array();
 
        // Validate Icon Size
        $icon_size = trim( $fields['icon_size'] );
        $valid_fields['icon_size'] = strip_tags( stripslashes( $icon_size ) );

        $show_social_name = trim( $fields['show_social_name'] );
        $valid_fields['show_social_name'] = strip_tags( stripslashes( $show_social_name ) );

        $target = trim( $fields['target'] );
        $valid_fields['target'] = strip_tags( stripslashes( $target ) );

        $orientation = trim( $fields['orientation'] );
        $valid_fields['orientation'] = strip_tags( stripslashes( $orientation ) );

        // Validate Icon Color
        $icon_color = trim( $fields['icon_color'] );
        $icon_color = strip_tags( stripslashes( $icon_color ) );
         
        // Check if is a valid hex color
        if( FALSE === $this->check_color( $icon_color ) ) {
         
            // Set the error message
            add_settings_error( 'dsw_settings_options', 'dsw_bg_error', 'Insert a valid color for icon', 'error' ); // $setting, $code, $message, $type
             
            // Get the previous valid value
            $valid_fields['icon_color'] = $this->options['icon_color'];
         
        } else {
         
            $valid_fields['icon_color'] = $icon_color;  
         
        }
         
        return apply_filters( 'validate_options', $valid_fields, $fields);
    }
     
    /**
     * Function that will check if value is a valid HEX color.
     */
    public function check_color( $value ) {
        if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) { // if user insert a HEX color with #     
            return true;
        }
        return false;
    }
     
    /**
     * Callback function for settings section
     */
    public function display_section() { /* Leave blank */ } 


}
DSW_Widget_Options::get_instance();