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
        $classes_dir = realpath(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'classes');
        $class_file = $class_name . '.php';
        require_once $classes_dir . DIRECTORY_SEPARATOR . $class_file;
    }
}
new JMACompPostTypeSelector();


/**
 * function jma_comp_setup_objs instantiates an objct for each component detected,
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
            $row = get_row();
            echo '<pre>';
            print_r($row);
            echo '</pre>';


            $row_id = $row['comp_id'];
            $row_type = $row['acf_fc_layout'];
            $class = 'JMAComponent' . $row_type;
            $return[$row_id]  = new $class($row);
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
add_filter('dynamic_styles_filter', 'jma_comp_filter');



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

    return $x . 'www';
}
add_shortcode('acf_component', 'acf_component_shortcode');


if (function_exists('acf_add_local_field_group')):
function post_group_options()
{
    $args = array(
       'public'   => true,
       '_builtin' => false
    );

    $post_types = get_post_types($args);
    array_unshift($post_types, "page", "post");
    acf_add_local_field_group(array(
        'key' => 'group_5c72a2077eb8e',
        'title' => 'Defaults',
        'fields' => array(
            array(
                'key' => 'field_5c72a25023276',
                'label' => 'Active Background Color',
                'name' => 'active_bg',
                'type' => 'color_picker',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '#ffffff',
            ),
            array(
                'key' => 'field_5c72a4ff23277',
                'label' => 'Active Font Color',
                'name' => 'active_font',
                'type' => 'color_picker',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '#000066',
            ),
            array(
                'key' => 'field_5c72a53623278',
                'label' => 'Inactive Background Color',
                'name' => 'inactive_bg',
                'type' => 'color_picker',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '#000066',
            ),
            array(
                'key' => 'field_5c72a56c23279',
                'label' => 'Inactive Font Color',
                'name' => 'inactive_font',
                'type' => 'color_picker',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '#ffffff',
            ),
            array(
                'key' => 'field_5c72a7ec47a2c',
                'label' => 'Location',
                'name' => 'location',
                'type' => 'post_object',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'post_type' => $post_types,
                'taxonomy' => '',
                'allow_null' => 0,
                'multiple' => 1,
                'return_format' => 'object',
                'ui' => 1,
            ),
            array(
                'key' => 'field_5c72ae5531046',
                'label' => 'Post Type',
                'name' => 'post_type',
                'type' => 'post_type_selector',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'select_type' => 'Checkboxes',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'jma-component-settings',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));


    $posts = get_field('location', 'option');
    $types = get_field('post_type', 'option');
    $location = array();
    foreach ($types as $type) {
        $location[] = array(
            array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => $type
        )
    );
    }
    acf_add_local_field_group(array(
    'key' => 'group_57e7318d8e3a0',
    'title' => 'ACF Components',
    'fields' => array(
        array(
            'key' => 'components',
            'label' => 'Components',
            'name' => 'components',
            'type' => 'flexible_content',
            'instructions' => 'Add components, then insert [acf_component id=\'yourcompid\'] where you want the component to appear.',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'min' => '',
            'max' => '',
            'button_label' => 'Add Component',
            'layouts' => array(
                array(
                    'key' => 'Accordion',
                    'name' => 'Accordion',
                    'label' => 'Accordion',
                    'display' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'comp_id',
                            'label' => 'Component Id',
                            'name' => 'comp_id',
                            'type' => 'text',
                            'instructions' => 'The value for yourcompid in shortcode [acf_component id=\'yourcompid\'] where you want the component to appear.',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '33',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                        ),
                        array(
                            'key' => 'custom_class',
                            'label' => 'Custom Class',
                            'name' => 'custom_class',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '33',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                        ),
                        array(
                            'key' => 'open',
                            'label' => 'Open First Panel',
                            'name' => 'open',
                            'type' => 'radio',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '33',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'horizontal',
                            'choices' => array(
                                0 => 'No',
                                1 => 'Yes',
                            ),
                            'default_value' => '',
                            'other_choice' => 0,
                            'save_other_choice' => 0,
                            'allow_null' => 0,
                            'return_format' => 'value',
                        ),
                        array(
                            'key' => 'inactive_bg',
                            'label' => 'Inactive Background',
                            'name' => 'inactive_bg',
                            'type' => 'color_picker',
                            'instructions' => 'if blank will match footer bg',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                        ),
                        array(
                            'key' => 'inactive_text',
                            'label' => 'Inactive Text',
                            'name' => 'inactive_text',
                            'type' => 'color_picker',
                            'instructions' => 'if blank will match footer text',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                        ),
                        array(
                            'key' => 'active_bg',
                            'label' => 'Active Background',
                            'name' => 'active_bg',
                            'type' => 'color_picker',
                            'instructions' => 'if blank will match footer text',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                        ),
                        array(
                            'key' => 'active_text',
                            'label' => 'Active Text',
                            'name' => 'active_text',
                            'type' => 'color_picker',
                            'instructions' => 'if blank will match body text',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                        ),
                        array(
                            'key' => 'tabs_content',
                            'label' => 'Tabs/Content',
                            'name' => 'tabs_content',
                            'type' => 'repeater',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'collapsed' => 'field_58628d0e3816b',
                            'min' => 0,
                            'max' => 0,
                            'layout' => 'table',
                            'button_label' => '',
                            'sub_fields' => array(
                                array(
                                    'key' => 'tab',
                                    'label' => 'Tab',
                                    'name' => 'tab',
                                    'type' => 'textarea',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '33',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '',
                                    'new_lines' => '',
                                    'maxlength' => '',
                                    'placeholder' => '',
                                    'rows' => 3,
                                ),
                                array(
                                    'key' => 'content',
                                    'label' => 'Content',
                                    'name' => 'content',
                                    'type' => 'wysiwyg',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '66',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'tabs' => 'all',
                                    'toolbar' => 'full',
                                    'media_upload' => 1,
                                    'default_value' => '',
                                    'delay' => 0,
                                ),
                            ),
                        ),
                    ),
                    'min' => '',
                    'max' => '',
                ),
                array(
                    'key' => 'Tabbed',
                    'name' => 'Tabbed',
                    'label' => 'Tabbed',
                    'display' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'comp_id',
                            'label' => 'Component Id',
                            'name' => 'comp_id',
                            'type' => 'text',
                            'instructions' => 'The value for yourcompid in shortcode [acf_component id=\'yourcompid\'] where you want the component to appear.',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '15',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                        ),
                        array(
                            'key' => 'custom_class',
                            'label' => 'Custom Class',
                            'name' => 'custom_class',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '15',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'maxlength' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                        ),
                        array(
                            'key' => 'alignment',
                            'label' => 'Alignment',
                            'name' => 'alignment',
                            'type' => 'radio',
                            'instructions' => 'Put the tabs on the top or left side of content penels',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '35',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'horizontal',
                            'choices' => array(
                                'top' => 'Top',
                                'left' => 'Left',
                            ),
                            'default_value' => 'top',
                            'other_choice' => 0,
                            'save_other_choice' => 0,
                            'allow_null' => 0,
                            'return_format' => 'value',
                        ),
                        array(
                            'key' => 'display',
                            'label' => 'Display',
                            'name' => 'display',
                            'type' => 'radio',
                            'instructions' => 'Arrows are animated tabs with pointed end on active panel.',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '35',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'horizontal',
                            'choices' => array(
                                'tabs' => 'Tabs',
                                'pills' => 'Pills',
                                'arrows' => 'Arrows',
                            ),
                            'default_value' => 'tabs',
                            'other_choice' => 0,
                            'save_other_choice' => 0,
                            'allow_null' => 0,
                            'return_format' => 'value',
                        ),
                        array(
                            'key' => 'inactive_bg',
                            'label' => 'Inactive Background',
                            'name' => 'inactive_bg',
                            'type' => 'color_picker',
                            'instructions' => 'if blank will match footer bg',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                        ),
                        array(
                            'key' => 'inactive_text',
                            'label' => 'Inactive Text',
                            'name' => 'inactive_text',
                            'type' => 'color_picker',
                            'instructions' => 'if blank will match footer text',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                        ),
                        array(
                            'key' => 'active_bg',
                            'label' => 'Active Background',
                            'name' => 'active_bg',
                            'type' => 'color_picker',
                            'instructions' => 'if blank will match footer text',
                            'required' => 0,
                            'conditional_logic' => array(
                                array(
                                    array(
                                        'field' => 'field_58628eaa38172',
                                        'operator' => '!=',
                                        'value' => 'tabs',
                                    ),
                                ),
                            ),
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                        ),
                        array(
                            'key' => 'active_text',
                            'label' => 'Active Text',
                            'name' => 'active_text',
                            'type' => 'color_picker',
                            'instructions' => 'if blank will match body text (footer bg for arrows)',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                        ),
                        array(
                            'key' => 'tabs_content',
                            'label' => 'Tabs/Content',
                            'name' => 'tabs_content',
                            'type' => 'repeater',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'collapsed' => 'field_58628da73816f',
                            'min' => 0,
                            'max' => 0,
                            'layout' => 'table',
                            'button_label' => '',
                            'sub_fields' => array(
                                array(
                                    'key' => 'tab',
                                    'label' => 'Tab',
                                    'name' => 'tab',
                                    'type' => 'textarea',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '20',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '',
                                    'new_lines' => '',
                                    'maxlength' => '',
                                    'placeholder' => '',
                                    'rows' => 3,
                                ),
                                array(
                                    'key' => 'content',
                                    'label' => 'Content',
                                    'name' => 'content',
                                    'type' => 'wysiwyg',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '80',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'tabs' => 'all',
                                    'toolbar' => 'full',
                                    'media_upload' => 1,
                                    'default_value' => '',
                                    'delay' => 0,
                                ),
                            ),
                        ),
                    ),
                    'min' => '',
                    'max' => '',
                ),
            ),
        ),
    ),
    'location' => $location,
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}
add_action('admin_init', 'post_group_options', 999);
endif;
