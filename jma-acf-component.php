<?php
/*
Plugin Name: JMA Advanced Custom Fields Components
Description: Updated for Theme ver 2.2 This plugin creates an accordions and tabs from Advanced Custom Fields flexible content field
Version: 1.1
Author: John Antonacci
Author URI: http://cleansupersites.com
License: GPL2
*/

/**
 * function jmaacf_detect_shortcode Detect shortcodes in a post object,
 *  from a post id or from global $post.
 * @param string or array $needle - the shortcode(s) and block(s) to search for
 * use array for multiple values
 * @param int or object $post_item - the post to search (defaults to current)
 * @return boolean $return
 */
if (! defined('JMACOMP_DIR')) {
    define('JMACOMP_DIR', plugin_dir_path(__FILE__));
}

//require JMACOMP_DIR . 'acf-post-type-selector/acf-post-type-selector.php';

function jmaacf_detect_shortcode($needle = '', $post_item = 0)
{
    if ($post_item) {
        if (is_object($post_item)) {
            $post = $post_item;
        } else {
            $post = get_post($post_item);
        }
    } else {
        global $post;
    }
    $return = false;
    $pattern = get_shortcode_regex();

    preg_match_all('/'. $pattern .'/s', $post->post_content, $matches);

    //if shortcode(s) to be searched for were passed and not found $return false
    if (!is_array($needle)) {
        $needle = explode(',', $needle);
    }
    if (count($matches[2])) {
        $return = array_intersect($needle, $matches[2]);
    }//next check for blocks
    elseif (function_exists('has_blocks') && has_blocks($post->post_content)) {
        foreach (parse_blocks($post->post_content) as $block) {
            $blocknames[] = $block['blockName'];
        }
        $return = array_intersect($needle, $blocknames);
    }
    return apply_filters('jmaacf_detect_shortcode_result', $return, $post, $needle);
}

/* accordion shortcode */


spl_autoload_register('jma_component_autoloader');
function jma_component_autoloader($class_name)
{
    if (false !== strpos($class_name, 'JMAComp')) {
        $classes_dir = JMACOMP_DIR . DIRECTORY_SEPARATOR . 'classes';
        $class_file = $class_name . '.php';
        require_once $classes_dir . DIRECTORY_SEPARATOR . $class_file;
    }
}
new JMACompPostTypeSelector();


/**
 * function jma_comp_setup_objs instantiates an objct with the name of the row id
 * for each component detected
 *  @uses have_rows from acf plugin
 *
 * @return boolean $return - array of objects with the comp_id value as their index
 */
function jma_comp_setup_objs()
{
    $return = array();
    if (have_rows('components')) {
        while (have_rows('components')) {
            the_row();
            $row = get_row(true);
            $row_type = $row['acf_fc_layout'];
            $row_id = $row['comp_id'];
            $class = 'JMAComponent' . $row_type;
            $return[$row_id] = new $class($row);
        }
    }
    return $return;
}

function jma_comp_css()
{
    if (!(jmaacf_detect_shortcode('acf_component') && have_rows('components'))) {
        return;
    }
    $comp_objs = jma_comp_setup_objs();
    $print = '';

    foreach ($comp_objs as $comp_obj) {
        $print .= $comp_obj->css();
    }
    $print .= '
    .jma-tabbed .nav li a {
    white-space: nowrap;
    }
    .jma-accordion.panel-group .panel+.panel {
    margin-top: 1px;
    }
    .jma-tabbed .nav>li.active>a {
    cursor: default;
    }
    @media(min-width:992px){
    .tabs-left.tb-tabs-framed > .tab-content {
    border-top-width: 1px;
    margin-left: 189px;
    }
    .tabs-left > .nav-tabs {
    width: 170px;
    float: left;
    }
    .tabs-left > .nav-pills {
    width: 185px;
    float: left;
    }
    .tabs-left > .nav-tabs>li {
    border-left-width: 1px;
    float: none;
    }
    .tabs-left > .nav-pills>li {
    margin-top:1px;
    border-width: 0;
    float: none;
    }
    .tabs-left >.nav-tabs>li.active {
    border-right-color: #ffffff;
    }


    .tabs-left {
        position: relative;
    }
    .tabs-left.tb-tabs-framed.tab-arrows > .tab-content {
        border-top-width: 1px;
        margin-left: 189px;
    }
    .tabs-left.tab-arrows > .nav-pills>li {
        margin-left:0;
        width: 155px;
        position: relative;
        -webkit-transition: all 0.3s; /* Safari */
        transition: all 0.3s;
    }
    .tabs-left.tab-arrows > .nav-pills>li.active, .tabs-left.tab-arrows > .nav-pills {
        width: 170px;
    }
    }
    @media(min-width:767px) and (max-width:920px){
        .tabs-left > .nav-pills>li>a {
            padding-left: 8px;
            padding-right: 8px
            }
            .nav-pills>li+li {
            margin-left: 1px;
        }
    }
    .jma-tabbed .tab-content  {
        overflow: hidden; /* allow clears to work correctly within this element */
    }';

    if ($print) {
        wp_add_inline_style('themeblvd-theme', apply_filters('jma_acf_css_output', $print));
    }
}
add_action('wp_enqueue_scripts', 'jma_comp_css', 99);


function get_comp_classes()
{
    $return = array();
    foreach (scandir(plugin_dir_path(__FILE__)) as $file) {
        // get the file name of the current file without the extension
        // which is essentially the class name
        $class = basename($file, '.php');

        if (false !== strpos($class, 'JMAComponent')) {
            $return[] = $class;
        }
    }
    return $return;
}


function jma_comp_filter($dynamic_styles)
{
    $comp_classes = get_comp_classes();
    if (is_array($comp_classes)) {
        foreach ($comp_classes as $comp_class) {
            $dynamic_styles = array_merge($dynamic_styles, $comp_class::css_filter());
        }
    }

    return $dynamic_styles;
}
//add_filter('dynamic_styles_filter', 'jma_comp_filter');



function acf_component_shortcode($atts = array())
{
    if (!function_exists('have_rows') || !have_rows('components')) {//returns if acf not active
        return;
    }
    extract(shortcode_atts(array(
        'id' => '',
        ), $atts));
    $comps = jma_comp_setup_objs();
    $this_comp = $comps[$id];
    ob_start();
    echo $this_comp->markup();
    $x = ob_get_contents();
    ob_end_clean();

    return $x;
}
add_shortcode('acf_component', 'acf_component_shortcode');

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' 	=> 'Component Settings',
        'menu_title'	=> 'Component Settings',
        'menu_slug' 	=> 'jma-component-settings',
        'capability'	=> 'edit_posts',
        'redirect'		=> false
    ));
}

if (function_exists('acf_add_local_field_group')) {
    include('jma-acf-addfieldgroups.php');
}
