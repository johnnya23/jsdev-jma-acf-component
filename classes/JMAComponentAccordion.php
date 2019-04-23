<?php
class JMAComponentAccordion extends JMAComponent
{
    public function markup()
    {
        $content = $this->content;
        $accordion_array = $content['tabs_content'];
        if (!(is_array($accordion_array) && count($accordion_array))) {//returns if $accordion_array not useful
            return;
        }
        $return = '<div ';
        $return .= 'id="' . $content['comp_id'] . '" ';
        $return .= 'class="tb-accordion panel-group jma-component jma-' . strtolower($content['acf_fc_layout']) . ' ' . $content['custom_class'] . '"';
        $return .= '>';
        foreach ($accordion_array as $i => $accordion_pair) {
            $trigger = $in = '';
            if ($content['open'] && !$i) {
                $trigger = ' active-trigger';
                $in = ' in';
            }

            $return .= '<div class="tb-toggle panel panel-default">';// panel-default
            $return .= '<div class="panel-heading">';//panel-heading
            $return .= '<a class="accordion-cat panel-title' . $trigger . '" data-toggle="collapse" data-parent="#accordion" href="#collapse' . $content['comp_id'] . $i . '">';
            $return .= '<i class="fas fa-angle-right icon-show switch-me"></i><i class="fas fa-angle-down icon-hide switch-me"></i>' . $accordion_pair['tab'];
            $return .= '</a>';
            $return .= '</div><!--panel-heading-->';
            $return .= '<div id="collapse' . $content['comp_id'] . $i . '" class="panel-collapse collapse' . $in . '"><div class="panel-body">';
            $return .= apply_filters('the_content', $accordion_pair['content']);
            $return .= '</div></div></div><!--panel-default-->';
        }
        $return .= '</div>';
        $return .= '<!--panel-group-->';
        return $return;
    }

    public function css()
    {
        $content = $this->content;
        $group_class = '#' . $this->id . '.jma-component.jma-accordion';
        $gen_options = get_fields('options');

        $return = '.jma-component.jma-accordion.panel-group .panel-default>.panel-heading a:hover {
            opacity: 0.65
        }
        .jma-component.jma-accordion.panel-group .panel-default>.panel-heading a.active-trigger:hover {
            opacity: 1
        }';

        //inactive background
        $value = $content['inactive_bg']? $content['inactive_bg']: $gen_options['jma_comp_inactive_bg'];
        $return .= $group_class . '.panel-group .panel-default>.panel-heading a {
            background-color: ' . $value . ';
            border-color: #cccccc;
        }';

        //inactive text
        $value = $content['inactive_text']? $content['inactive_text']: $gen_options['jma_comp_inactive_text'];
        $return .= $group_class . '.panel-group .panel-default>.panel-heading a {
            color: ' . $value . ';
        }';

        //active background
        $value = $content['active_bg']? $content['active_bg']: $gen_options['jma_comp_active_bg'];
        $return .=  $group_class . '.panel-group .panel-default>.panel-heading a.active-trigger {
            background-color: ' . $value . ';
        }';

        //active text
        $value = $content['active_text']? $content['active_text']: $gen_options['jma_comp_active_text'];
        $return .=  $group_class . '.panel-group .panel-default>.panel-heading a.active-trigger {
            color: ' . $value . ';
        }';

        return $return;
    }
}
