<?php
namespace WTP_MIXCODE_TWIG;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;
use Twig\Markup;

class twigExtensions extends AbstractExtension
{
    function __construct($Twig) {
        $this-> Twig = $Twig;
    }
    public function getFilters() {
        return [
            new TwigFilter('debug', [$this, 'debug']),
        ];
    }
    public function debug($data) {
        if(function_exists('d')) {
            d($data);
        } else {
            var_dump($data);
        }
    }
    public function getFunctions() {
        return [
            new TwigFunction('content', [$this, 'content']),
            new TwigFunction('title', [$this, 'title']),
            new TwigFunction('render', [$this, 'render']),
            new TwigFunction('uid', [$this, 'uid']),
            new TwigFunction('bloginfo', [$this, 'bloginfo']),
            new TwigFunction('debug', [$this, 'debugs']),
            new TwigFunction('is_admin', [$this, 'is_admin']),
            new TwigFunction('t', [$this, 't']),
            new TwigFunction('render_t', [$this, 'render_t']),
            new TwigFunction('json', [$this, 'json']),
            new TwigFunction('print_func', [$this, 'print_func']),
            new TwigFunction('do_shortcode', [$this, 'do_shortcode']),
            new TwigFunction('the_post', [$this, 'post']),
            new TwigFunction('call', [$this, 'call'])
            // Вызов функции
        ];
    }
    public function call($name, ...$arguments) {
        if(function_exists($name)) {
            return call_user_func($name, ...$arguments);
        }
    }
    public function is_admin() {
        return current_user_can('manage_options');
    }
    public function do_shortcode($shortcode, $data) {
        $re = '/\]$/m';
        $data = wp_json_encode($data);
        $shortcode = preg_replace($re, " data='" . $data . "']", $shortcode);
  
        return new Markup(do_shortcode($shortcode), "utf-8");
    }  
    public function title() {
        global $post;
        return new Markup($post->post_title, "utf-8");
    }
    public function post() {
        global $post;
        return $post;
    }
    public function content() {
        global $post;
        return new Markup(apply_filters('the_content', $post->post_content), "utf-8");
    }  
    public function uid() {
        return uniqid('mixcode_');
    }
    public function json($data, $default='[]') {
        if(empty($data)) {
            return $default;
        }
        return new Markup(json_encode($data), "utf-8");
    }
    public function render($template, $data=[]) {
        if(empty($data) || empty($template)) {
            return '';
        }
        $template = $this-> Twig -> createTemplate($template);
        $result = $template->render($data);
        return new Markup($result, "utf-8");
    }
    public function render_t($template, $data=[]) {
        if(!function_exists('pll__')) {
            return 'Plugin polylang not enabled';
        }
        if(empty($data) || empty($template)) {
            return '';
        }
        $template = pll__($template);
        $template = $this-> Twig -> createTemplate($template);
        $result = $template->render($data);
        return $result;
    }
    public function bloginfo($slug) {
        return get_bloginfo($slug);
    }
    public function t($string) {
        if(!function_exists('pll__')) {
            return 'Plugin polylang not enabled';
        }
        return pll__($string);
    }
    public function debugs($data) {
        if(function_exists('d')) {
            d($data);
        } else {
            var_dump($data);
        }
    }
    public function print_func($funcName) {
        if(function_exists($funcName)) {
            ob_start();
            call_user_func($funcName);
            return new Markup(ob_get_clean(), "utf-8");
        } else {
            return '';
        }
    }
}
