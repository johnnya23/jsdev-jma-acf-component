<?php
class JMAComponent {
    var $content;
    var $id;


    function __construct($content) {
        $this->content = $content;
        $this->id = $content['comp_id'];
    }

    public function css(){
        $return = '';
        return $return;
    }

    static function css_filter(){
        $return = array();
        return $return;
    }

}