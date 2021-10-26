<?php
/**
 * Webto.pro plugin for ACF
 *
 * @copyright Copyright (C) 2021 to this day, Webto.pro - support@webto.pro
 *
 * @wordpress-plugin
 * Plugin Name: Mixcode by WebTo.Pro
 * Version:     1.0
 * Description: Addition for ACF plugin.
 * Author:      Unbywyd
 * Plugin URI:  https://webto.pro
 * Author URI:  https://unbywyd.com
 * Text Domain: webto.pro
 */

define('MIXCODE_DIR', plugin_dir_path(__FILE__));
define('MIXCODE_URL', plugin_dir_url(__FILE__));

define('MIXCODE_PLUGIN_DEV_MODE', false);

require_once(MIXCODE_DIR . 'libs/twig/twigLoader.php');
require_once(MIXCODE_DIR . 'incs/ajax.php');

use WTP_MIXCODE_TWIG\twigLoader as twigLoader;
use WTP_MIXCODE_AJAX\Ajax as mixcodeAjax;
class MIXCODE {
  var $scripts = '';
  var $styles = ''; 
  var $shortcodes = [];
	function __construct() {
    $this-> Twig = new twigLoader;
    $this -> Ajax = new mixcodeAjax($this-> Twig);
    /*
    * Action handlers
    */
		add_action('init', [$this, 'init']);
		add_action('admin_init', [$this, 'admin_init']);
		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
    add_action( 'admin_notices', [$this, 'admin_notices']);    
    add_action( 'admin_menu', [$this, 'admin_menu']);
    add_action( 'save_post', [$this, 'save_post'], 10, 3); 
    add_action( 'delete_post', [$this, 'delete_post'], 10, 2);
    add_action('add_meta_boxes', [$this, 'add_meta_boxes'], 1);
    add_shortcode( 'mixcode', [$this, 'do_shortcode']);
    add_action('wp_footer', [$this, 'wp_footer']); 
    add_action('after_setup_theme', [$this, 'after_setup_theme']);   
  }
  function after_setup_theme() {
    $d = get_option('wtp_mixcode_translations', '');
    $translations = json_decode(urldecode($d), true);
    if(!empty($translations)) {            
      foreach($translations as $str) {
          if(function_exists('pll_register_string')) {
              pll_register_string($str['slug'], $str['value']);
          }
      }
    } 
  }
  function wp_footer() {
    /*
    * Generating scripts and styles of shortcodes to the site footer
    */
    print '<script class="mixcode_script">'.$this -> scripts.'</script>';
    print '<style class="mixcode_style">'.$this -> styles.'</style>';
    print '<script>document.querySelectorAll(".mixcode_style").forEach((el) => {
      document.querySelector("head").appendChild(el); 
    });</script>';
  }
  function do_shortcode($attrs) {
    /*
    * Main shortcode handler
    */    
    if(empty($attrs['tid'])) {
      return '"tid" parameter is required!';
    }
    $template = get_post($attrs['tid']);
    if(empty($template)) {
      return $attrs['tid'] .  ' template not exists';
    }
    $scripts = get_post_meta($attrs['tid'], 'js', true);
    $styles = get_post_meta($attrs['tid'], 'css', true);

    $widget_id = uniqid('mixcode_');
    if(empty($scripts) || !$scripts) {
      $scripts = '';
    } else {
      // Add mixcode_widget_id var to javascript
      $scripts = "var mixcode_widget_id = '" . $widget_id . "'; " . $scripts;
    }
    if(empty($styles) || !$styles) {
      $styles = '';
    } else {
      // Replace the line "mixcode_widget_id" in the css with the id of this widget
      $styles = preg_replace('/mixcode_widget_id/im', $widget_id, $styles);
    }
    $this -> scripts .= "\n " . $scripts;
    $this -> styles .= "\n " . $styles;

    $this -> shortcodes[] = $attrs['tid'];
    $data = [];
    $pid_fields = '';
    // Check if the plugin ACF is enabled
    if(function_exists('get_field')) {
      if(!empty($attrs['field'])) {  
        if(empty($attrs['pid'])) {
          return 'No "pid" specified';
        } else {
          $data[$attrs['field']] = get_field($attrs['field'], $attrs['pid']);
          $pid_fields .= 'data-pid="'. $attrs['pid'] . '"';
          $pid_fields .= ' data-acf-field="'. $attrs['field'] . '"';
        }      
      } elseif(!empty($attrs['pid'])) { 
        $data = get_fields($attrs['pid']);
        $pid_fields .= 'data-pid="'. $attrs['pid'] . '"';       
      }
    }
 
    $_data = $attrs['data'];
    if(!empty($_data)) {
      $_data = json_decode($_data, true);
    }
    if(!empty($_data)) {
      $data = $_data;
    }
    // Add the id of this widget to the object for the template
    $data['mixcode_widget_id'] = $widget_id;
    $title = get_the_title($attrs['tid']);
    return '<div class="mixcode_widget" data-title="'.$title.'" data-tid="'.$attrs['tid'].'" '.$pid_fields.'>' . $this -> Twig -> render($attrs['tid'], $data) . '</div>';
  }
  function add_meta_boxes() {
    /*
    * Custom fields to templates post type
    */
    add_meta_box( 'mixcode_fields', 'ACF Mixcode template fields', [$this, 'meta_box_template_fields'], 'mixcode_template', 'normal', 'high'  );
  }
  function meta_box_template_fields($post) {
    ?>
    <div class="app-mixcode-forms">
      <p class="notice notice-info notice-alt">You can use <b>mixcode_widget_id</b> string that contains the unique id of this widget, examples: <b> In tempate</b> {{mixcode_widget_id}}, <b>in js</b>: alert(mixcode_widget_id), <b>in css</b> .mixcode_widget_id
    </p>
      <p class="notice notice-info notice-alt">You can use include "template_post_id" method for nested use.</p>
      <div class="app-mixcode-form-group">
        <label for="mixcode_template"><b>Twig template</b></label>
<textarea data-mode="twig" class="mixcode_codemirror" name="mixcode[template]" id="mixcode_template" cols="30" rows="10"><?php echo get_post_meta($post->ID, 'template', true); ?></textarea>
      </div>
      <hr>
      <div class="app-mixcode-form-group">
        <label for="mixcode_template_css"><b>CSS</b></label>
        <textarea data-mode="css" class="mixcode_codemirror" name="mixcode[css]" id="mixcode_template_css" cols="30" rows="10"><?php echo get_post_meta($post->ID, 'css', true); ?></textarea>
      </div>
      <hr>
      <div class="app-mixcode-form-group">
        <label for="mixcode_template_js"><b>Javascript</b></label>
        <textarea data-mode="javascript" class="mixcode_codemirror" name="mixcode[js]" id="mixcode_template_js" cols="30" rows="10"><?php echo get_post_meta($post->ID, 'js', true); ?></textarea>
      </div>
      <input type="hidden" name="mixcode_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
    </div>
    <?php
  }

  function delete_post($post_id, $post) {
    /*
    * Synchronizing with physical template files
    */
    if($post->post_type == 'mixcode_template') {
      $this -> Twig -> removeTemplate($post);
    }
  }

  function save_post($post_id, $post, $update) {
    /*
    * Synchronizing with physical template files
    * Save our custom fields for CSS/JS/Template
    */
    if( $post->post_type == 'mixcode_template') {
        if (empty( $_POST['mixcode'] )
          || ! wp_verify_nonce( $_POST['mixcode_fields_nonce'], __FILE__ )
          || wp_is_post_autosave( $post_id )
          || wp_is_post_revision( $post_id )
        ) {  return false; }

      
        foreach( $_POST['mixcode'] as $key => $value  ){
          if( empty($value) ){
            delete_post_meta( $post_id, $key );
            continue;
          }
          update_post_meta( $post_id, $key, trim($value)); 
        }    
        $this -> Twig -> cacheReset();
        if($update) {
          $this -> Twig -> removeTemplate($post);
        }
        $this -> Twig -> createTemplate($post);      
    }
    return $post_id;
  }

  function acf_check() {
    /*
    * Check if the ACF plugin is exists
    */
    return class_exists( 'acf' );
  }

  function admin_notices() {
    /*
    * Check if the ACF plugin is exists
    */
    if (!$this->acf_check() && current_user_can( 'manage_options' )) {
          $class   = 'notice notice-error';
          $message = sprintf( __( 'Uh-oh. ACF not installed. Please install the <a href="%s" class="thickbox">Advanced Custom Fields plugin.</a>', 'sample-text-domain' ), '/wp-admin/plugin-install.php?tab=plugin-information&plugin=advanced-custom-fields&TB_iframe=true&width=600&height=550' );
          printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
      }
  }

	function admin_enqueue_scripts($hook_suffix) {
    /*
    * Include scripts and styles
    */
    if($hook_suffix == 'acf-mixcode_page_mixcode_generator') {
		  wp_enqueue_style('wtp-acf-mixcode-admin');
		  wp_enqueue_script('wtp-acf-mixcode-admin');
    }
    if($hook_suffix == 'acf-mixcode_page_mixcode_translations') {
		  wp_enqueue_style('wtp-acf-mixcode-admin');
      wp_enqueue_script('wtp-acf-mixcode-translations');
    }
    global $post;
    if(!empty($post) && $post->post_type == 'mixcode_template') {
      wp_enqueue_script('wtp-acf-mixcode-codemirror');
    }
	}

	function admin_init() {
    /*
    * Registration scripts and styles
    */
    wp_register_script( 'wtp-acf-mixcode-translations', MIXCODE_URL . '/client/dist/js/translates.min.js' , array(), filemtime(MIXCODE_DIR . '/client/dist/js/translates.min.js'), true);   
    wp_register_script( 'wtp-acf-mixcode-admin', MIXCODE_URL . '/client/dist/js/index.min.js' , array(), filemtime(MIXCODE_DIR . '/client/dist/js/index.min.js'), true);   
    wp_register_script( 'wtp-acf-mixcode-codemirror', MIXCODE_URL . '/client/dist/js/codemirror.min.js' , array(), filemtime(MIXCODE_DIR . '/client/dist/js/codemirror.min.js'), true);   
    wp_localize_script('wtp-acf-mixcode-translations', 'wtp_mixcode', array(
      'ajax_url' => admin_url( 'admin-ajax.php' )
    ));
    wp_localize_script('wtp-acf-mixcode-admin', 'wtp_mixcode', array(
      'ajax_url' => admin_url( 'admin-ajax.php' )
    ));

    if(MIXCODE_PLUGIN_DEV_MODE) {
      wp_register_style( 'wtp-acf-mixcode-admin', MIXCODE_URL . '/client/dist/css/app.css' , array(), filemtime(MIXCODE_DIR . '/client/dist/css/app.css'));   
    } else {
      wp_register_style( 'wtp-acf-mixcode-admin', MIXCODE_URL . '/client/dist/releases/app-v1/css/app.min.css' , array(), filemtime(MIXCODE_DIR . '/client/dist/releases/app-v1/css/app.min.css'));   
    }
	}
  function admin_menu() {
      /*
      * Add pages to dashboard menu
      */
      add_menu_page( 'ACF Mixcode', 'ACF Mixcode', 'edit_others_posts', 'mixcode', null, 'dashicons-welcome-widgets-menus', 6 );
      add_submenu_page( 'mixcode', 'ACF Mixcode generator', 'ACF Mixcode generator', 'edit_others_posts', 'mixcode_generator', [$this, 'page_generator']);
      add_submenu_page( 'mixcode', 'ACF Mixcode translations', 'ACF Mixcode translations', 'edit_others_posts', 'mixcode_translations', [$this, 'page_translations']);
  }
  function page_translations() {
    require(MIXCODE_DIR . '/incs/translations.php');
  }
  function page_generator() {
    require(MIXCODE_DIR . '/incs/generator.php');
  }
	function init() {   
      /*
      * Add mixcode_template custom type
      */
      $labels = array(
        'name'                => __( 'ACF Mix. Template', 'wtp_mixcode' ),
        'singular_name'       => __( 'ACF Mix. Template', 'wtp_mixcode' ),
        'menu_name'           => __( 'ACF Mix. Templates', 'wtp_mixcode' ),
        'parent_item_colon'   => __( 'Parental:', 'wtp_mixcode' ),
        'all_items'           => __( 'ACF Mix. Templates', 'wtp_mixcode' ),
        'view_item'           => __( 'View', 'wtp_mixcode' ),
        'add_new_item'        => __( 'Add new ACF Mix. Template', 'wtp_mixcode' ),
        'add_new'             => __( 'Add new', 'wtp_mixcode' ),
        'edit_item'           => __( 'Edit ACF Mix. Template', 'wtp_mixcode' ),
        'update_item'         => __( 'Update ACF Mix. Template', 'wtp_mixcode' ),
        'search_items'        => __( 'Find ACF Mix. Template', 'wtp_mixcode' ),
        'not_found'           => __( 'Not found', 'wtp_mixcode' ),
        'not_found_in_trash'  => __( 'Not found in the bin', 'wtp_mixcode' ),
      );
      $args = array(
          'labels'              => $labels,
          'supports'            => array('title', 'revisions'),
          'public'              => false,
          'publicly_queryable' => false,
          'rewrite' => array( 'slug' => 'mixcode_template' ),
          'query_var' => true,
          'show_in_menu' => 'mixcode',
          'show_in_rest' => false,
          'show_ui' => true,
          'hierarchical' => false,
          'menu_position'       => 1,
          'menu_icon'           => 'dashicons-welcome-widgets-menus',
      );
      register_post_type( 'mixcode_template', $args );       
   }
}

$MIXCODE =  new MIXCODE();