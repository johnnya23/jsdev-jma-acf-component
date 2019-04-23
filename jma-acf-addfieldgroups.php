<?php

function post_group_options()
{
    $args = array(
       'public'   => true,
       '_builtin' => false
    );

    $post_types = get_post_types($args);
    array_unshift($post_types, "page", "post");

    /* general settings page */
    acf_add_local_field_group(array(
        'key' => 'group_5c72a2077eb8e',
        'title' => 'Defaults',
        'fields' => array(
            array(
                'key' => 'field_5c72a25023276',
                'label' => 'Active Background Color',
                'name' => 'jma_comp_active_bg',
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
                'name' => 'jma_comp_active_text',
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
                'name' => 'jma_comp_inactive_bg',
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
                'name' => 'jma_comp_inactive_text',
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
                'name' => 'jma_comp_location',
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
                'name' => 'jma_comp_post_type',
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

    /* pull types from settings page to determine visibility on pages/posts */
    //$posts = get_field('location', 'option');
    $types = get_field('jma_comp_post_type', 'option');
    $posts = get_field('jma_comp_location', 'option');
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
    foreach ($posts as $post) {
        $param = is_page($post)?'page':'post';
        $location[] = array(
                array(
                'param' => $param,
                'operator' => '==',
                'value' => $post->ID
            )
        );
    }
    /* accordion and tabbed options (flexible content) for pages/posts */
    acf_add_local_field_group(
        array(
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
                                'key' => 'accordion_comp_id',
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
                                'key' => 'accordion_custom_class',
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
                                'key' => 'accordion_open',
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
                                'key' => 'accordion_inactive_bg',
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
                                'key' => 'accordion_inactive_text',
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
                                'key' => 'accordion_active_bg',
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
                                'key' => 'accordion_active_text',
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
                                'key' => 'accordion_tabs_content',
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
                                        'key' => 'accordion_tab',
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
                                        'key' => 'accordion_content',
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
                                'key' => 'tabbed_comp_id',
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
                                'key' => 'tabbed_custom_class',
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
                                'key' => 'tabbed_alignment',
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
                                'key' => 'tabbed_display',
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
                                'key' => 'tabbed_inactive_bg',
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
                                'key' => 'tabbed_inactive_text',
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
                                'key' => 'tabbed_active_bg',
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
                                'key' => 'tabbed_active_text',
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
                                'key' => 'tabbed_tabs_content',
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
                                        'key' => 'tabbed_tab',
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
                                        'key' => 'tabbed_content',
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
        )
    );
}
//add_action('acf/init', 'post_group_options', 999);
