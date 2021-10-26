<?php
namespace WTP_MIXCODE_TWIG;
require_once('vendor/autoload.php');
require_once(MIXCODE_DIR . 'incs/utils.php');
require_once('twigExtensions.php');

//use WTP_MIXCODE_TWIG\twigExtensions as twigExtensions;

class twigLoader {
	function __construct() {	
    /*
    * Initialized twig, assigned templates and cache directories
    */
		if(!file_exists(wp_get_upload_dir()['basedir'] . '/mixcode')) {
      wp_mkdir_p(wp_get_upload_dir()['basedir'] . '/mixcode/cache');
      wp_mkdir_p(wp_get_upload_dir()['basedir'] . '/mixcode/templates');
    }
    $this -> twigLoader = new \Twig\Loader\FilesystemLoader(wp_get_upload_dir()['basedir'] . '/mixcode/templates');
    $this -> plugin = new \Twig\Environment($this -> twigLoader, [
        'cache' => wp_get_upload_dir()['basedir'] . '/mixcode/cache',
        'debug' => false
    ]);
    $this -> plugin -> addExtension(new twigExtensions($this -> plugin));
    $this -> plugin -> addExtension(new \Twig\Extension\StringLoaderExtension());
	}
  function render($slug, $data) {
    global $post;  
    return $this -> plugin -> render($slug, $data);
  }
  function cacheReset() {
    \WTP_MIXCODE_UTILS\rrmdir(wp_get_upload_dir()['basedir'] . '/mixcode/cache');
  }
  function removeTemplate($post) {   
    $pathFile = wp_get_upload_dir()['basedir'] . '/mixcode/templates/'. $post->ID;
    if(file_exists($pathFile)) {
      unlink($pathFile);
    }
  }
  function createTemplate($post) {
    $pathFile = wp_get_upload_dir()['basedir'] . '/mixcode/templates/'. $post->ID;
    $template = get_post_meta($post->ID, 'template', 1);
    file_put_contents($pathFile, $template);
  }
}
