<?php
namespace WTP_MIXCODE_AJAX;

class Ajax {
    function __construct($Twig) {
        $this -> Twig = $Twig;
        add_action('wp_ajax_mixcode_get_first_data', array($this, 'get_first_data'));   
        add_action('wp_ajax_mixcode_get_posts', array($this, 'get_posts'));   
        add_action('wp_ajax_mixcode_get_acf_fields', array($this, 'get_acf_fields'));   
        add_action('wp_ajax_mixcode_remove_cache', array($this, 'remove_cache'));  

        add_action('wp_ajax_mixcode_get_translations', array($this, 'get_translations'));   
        add_action('wp_ajax_mixcode_save_translations', array($this, 'save_translations'));   
    }
    function save_translations() {
        if(isset($_POST['data']) && !empty($_POST['data'])) {       
            $data = $_POST['data'];        
            try {
                $translations = json_decode(urldecode($data), true);
                if(!empty($translations)) {            
                    foreach($translations as $str) {
                        if(function_exists('pll_register_string')) {
                            pll_register_string(sanitize_key($str['slug']), sanitize_text_field($str['value']));
                        }
                    }
                }        
                print update_option('wtp_mixcode_translations', $data);
            } catch (Exception $e) {
               print 0;
               wp_die();
            }            
        }
        wp_die();
    }
    function get_translations() {
        print(get_option('wtp_mixcode_translations', ''));
        wp_die();
    }
    function remove_cache() {
        $this->Twig->cacheReset();
        print 1;
        wp_die();
    }
    function get_acf_fields() {
        if(isset($_POST['post_id'])) {
            $postId = sanitize_key($_POST['post_id']);
            wp_send_json(get_fields($postId));
        } else {
            wp_die();
        }
    }
    function get_posts() {
        if(isset($_POST['post_type'])) {
            $postType = sanitize_key($_POST['post_type']);
            $posts = get_posts([
                'numberposts' => -1,
                'posts_per_page' => -1,
                'post_type' => $postType,
                'orderby' => 'title'
            ]);            
            wp_send_json(['posts' => $posts]);
        } else {
            wp_die();
        }
    }
    function get_first_data() {
        $output = [];
        $output['post_types'] = get_post_types();        
        $templates = get_posts([
            'numberposts' => -1,
            'posts_per_page' => -1,
            'post_type' => 'mixcode_template',
            'orderby' => 'title'
        ]);
        $output['templates'] = $templates;
        wp_send_json($output);       
    }

}