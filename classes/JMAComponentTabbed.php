<?php
class JMAComponentTabbed extends JMAComponent
{
    public function markup()
    {
        $content = $this->content;
        $tabbed_array = $content['tabs_content'];
        if (!(is_array($tabbed_array) && count($tabbed_array))) {//reutns if $tabbed_array not useful
            return;
        }
        $wrap_cl = '';
        $ul_pill_cl = 'tabs';
        if ($content['display'] != 'tabs') {
            $wrap_cl .= 'tb-tabs-pills';
            $ul_pill_cl = 'pills';
        }
        if ($content['display'] == 'arrows') {
            $wrap_cl .= ' tabs-left tab-arrows';
        } elseif ($content['alignment'] == 'left') {
            $wrap_cl .= ' tabs-left';
        }

        $return = '<div ';
        $return .= 'id="' . $content['comp_id'] . '" ';
        $return .= 'class="' . $content['custom_class'] . ' tb-tabs tabbable tb-tabs-framed ' . $wrap_cl . ' jma-component jma-' . strtolower($content['acf_fc_layout']) . '"';
        $return .= '>';
        $tabs = '<ul class="nav nav-' . $ul_pill_cl . '">';
        $tab_content = '<div class="tab-content dark">';
        foreach ($tabbed_array as $i => $tabbed_pair) {
            $active =  '';
            if (!$i) {
                $active =  'active';
            }
            $tabs .= '<li class="' . $active . '">';
            $tabs .= '<a href="#tab_' . $content['comp_id'] . $i . '" data-toggle="tab" title="Title #1">';
            $tabs .= $tabbed_pair['tab'];
            $tabs .= '</a>';
            $tabs .= '</li>';


            $tab_content .= '<div id="tab_' . $content['comp_id'] . $i . '" class="tab-pane entry-content fade in clearfix ' . $active . '">';

            $tab_content .= apply_filters('the_content', $tabbed_pair['content']);
            $tab_content .= '</div><!--tab-pane-->';
        }
        $tabs .= '</ul><!--nav-tabs-->';
        $tab_content .= '</div><!--tab-content-->';
        $return .=  $tabs . $tab_content;
        $return .= '</div><!--tabbable-->';
        $return .= '<div style="clear:both"></div><!--in case tabs are taller that content-->';
        return $return;
    }

    public function css()
    {
        $content = $this->content;
        $group_class = '#' . $this->id . '.jma-component.jma-tabbed';
        $gen_options = get_fields('options');

        $return = '@media(min-width:992px){
            .jma-component.jma-tabbed.tabs-left.tab-arrows > .nav-pills>li.active:after {
                content: "";
                right: -25px;
                top: 0;
                width: 0;
                height: 0;
                position: absolute;
                border-style: solid;
                border-width: 22px 0 22px 25px;
            }
        }
        .jma-component.jma-tabbed > .nav-tabs>li.active>a {
            background-color: inherit!important;
            color: inherit;
        }
        .jma-component.jma-tabbed.tabs-left.tab-arrows > .nav-pills>li>a {
            border-radius: 0;
        }
        .jma-component.jma-tabbed > .nav>li>a:hover {
            opacity: 0.65;
        }.jma-component.jma-tabbed > .nav>li.active>a:hover {
            opacity: 1;
        }';

        //inactive background
        $value = $content['inactive_bg']? $content['inactive_bg']: $gen_options['jma_comp_inactive_bg'];
        $return .= $group_class . ' .nav>li>a {
            background-color: ' . $value . ';
        }';

        //inactive text
        $value = $content['inactive_text']? $content['inactive_text']: $gen_options['jma_comp_inactive_text'];
        $return .= $group_class . ' .nav>li>a {
            color: ' . $value . ';
        }';

        //active background
        $value = $content['active_bg']? $content['active_bg']: $gen_options['jma_comp_active_bg'];
        $return .=  $group_class . ' .nav-pills>li.active>a {
            background-color: ' . $value . '!important;
        }';
        $return .=  $group_class . '.tabs-left.tab-arrows .nav-pills>li.active:after {
            border-color: transparent transparent transparent ' . $value . '
        }';

        //active text
        $value = $content['active_text']? $content['active_text']: $gen_options['jma_comp_active_text'];
        $return .=  $group_class . ' .nav>li.active>a {
            color: ' . $value . ';
        }';


        return $return;
    }
}
