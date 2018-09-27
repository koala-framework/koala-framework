<?php
class Kwc_Basic_Button_ApiContent extends Kwc_Basic_Link_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $style =  $data->getComponent()->getRow()->style;
        $settings = Kwc_Basic_Button_Component::getSettings();
        $defaultStyle = ((isset($settings['styles']) && is_array($settings['styles']) && !empty($settings['styles']))) ? key($settings['styles']) : 'default';

        $ret = parent::getContent($data);
        $ret['style'] = ($style) ? $style : $defaultStyle;
        return $ret;
    }
}
