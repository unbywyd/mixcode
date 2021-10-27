=== Mixcode by WebTo.Pro ===
Contributors: unbywyd
Donate link: https://unbywyd.com/
Tags: Twig, shortcodes, mixcode
Requires at least: 4.0
Tested up to: 5.8
Stable tag: 1.0.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add-on to plug-in ACF, creating your own shortcodes through the admin dashboard

== Description ==

This plugin allows you to create your own dynamic shortcode based on styles, scripts and a twig template. 
Features of this plugin:

- Dynamically generate your own shortcodes
- Using the twig template engine
- Template nesting support (template in template), You can use {{do_shortcode}} method for this or include() method of Twig template engine
- Grouping styles and scripts and output in the footer of the site
- Synchronization with plugin polylang (Multilingual support)

### mixcode_widget_id
You can use **mixcode_widget_id** string that contains the unique id of this widget, examples: In tempate **{{mixcode_widget_id}}**, in js: **alert(mixcode_widget_id)**, in css **.mixcode_widget_id**

### Including templates from other templates
You can use include "**template_post_id**" method in your twig templates for nested use, example:

```
// Include template with post_id 60 and pass it a data object      
{% include "60" with {name:'Spinosaurus'}%}
```

### Use the following available methods and helpers in your twig templates:
- **content** - Get global post content
- **title** - Get global post title
- **the_post** - Get global post object
- **render** - Render an inline twig template with data
- **uid** - Generate a random unique id
- **bloginfo** - Displays information about the blog. Wordpress method, see a documentation
- **debug** - Outputting data to the screen, by default uses the d() method from the Kint Debugger plugin, and otherwise outputs data using var_dump()
- **is_admin** - Checks if the current user is an admin
- **json** - Displays data in JSON format
- **print_func**- Calls a function that displays information on the screen, stores it in memory, returns the result
- **do_shortcode** - Search content for shortcodes and filter shortcodes through their hooks. See wordpress documentation
- **t** - Displays a string previously registered in polylang using pll__ polylang method. See documentation of polylang
- **render_t** - First compile the twig template and then translate it using the pll_ method
- **call** - Calls any other function with arguments


Using this plugin, you can generate a component of any complexity and display it on the screen using a shortcode!

== Frequently Asked Questions ==

= What are the dependencies =

ACF (Advanced Custom Fields) and polylang (optional) 

== Screenshots ==

1. ![Template post](https://ungic.com/mixcode/1.jpg)
2. ![Shortcode generator](https://ungic.com/mixcode/2.jpg)
3. ![Polylang support](https://ungic.com/mixcode/3.jpg)

== Changelog ==

= 1.0 =
* Publishing a plugin

== Upgrade Notice ==
= 1.0 =
* Publishing a plugin

`<?php code(); ?>`