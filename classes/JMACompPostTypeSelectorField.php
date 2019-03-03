<?php

class JMACompPostTypeSelectorField extends acf_field
{

    // vars
    public $defaults; // will hold default field options

    /*
    *  __construct
    *
    *  This function will setup the field type data
    *
    *  @type	function
    *  @date	5/03/2014
    *  @since	5.0.0
    *
    *  @param	n/a
    *  @return	n/a
    */

    public function __construct()
    {

        // vars
        $this->name = 'post_type_selector';
        $this->label = __('Post Type Selector');
        $this->category = __("Relational", 'acf'); // Basic, Content, Choice, etc
        $this->defaults = array(
            'select_type' => 'Checkboxes',
        );


        // do not delete!
        parent::__construct();
    }



    /*
    *  render_field()
    *
    *  Create the HTML interface for your field
    *
    *  @param	$field (array) the $field being rendered
    *
    *  @type	action
    *  @since	3.6
    *  @date	23/01/13
    *
    *  @param	$field (array) the $field being edited
    *  @return	n/a
    */

    public function render_field($field)
    {
        // defaults?
        $field = array_merge($this->defaults, $field);

        $post_types = get_post_types(array(
            'public' => true,
        ), 'objects');

        // create Field HTML
        $checked = array( );



        echo '<ul class="checkbox_list checkbox">';

        if (! empty($field[ 'value'])) {
            foreach ($field[ 'value' ] as $val) {
                $checked[ $val ] = 'checked="checked"';
            }
        }

        foreach ($post_types as $post_type) {
            ?>

					<li><input type="checkbox" <?php echo (isset($checked[ $post_type->name ])) ? $checked[ $post_type->name] : null; ?> class="<?php echo $field[ 'class' ]; ?>" name="<?php echo $field[ 'name' ]; ?>[]" value="<?php echo $post_type->name; ?>"><label><?php echo $post_type->labels->name; ?></label></li>
				<?php
        }

        echo '</ul>';
    }
}


// create field
//new acf_field_post_type_selector();

?>
