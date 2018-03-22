<?php
class Kwf_Component_ApiContent_Helper
{
    public static function getContent(Kwf_Component_Data $data)
    {
        $ret = array();
        if (Kwc_Abstract::hasSetting($data->componentClass, 'apiContent')) {
            $cls = Kwc_Abstract::getSetting($data->componentClass, 'apiContent');
            $propsGetter = new $cls();
            $ret = array_merge($ret, $propsGetter->getContent($data));
            $ret['type'] = Kwc_Abstract::getSetting($data->componentClass, 'apiContentType');
        } else {
            $ret['html'] = $data->render();
            $ret['type'] = 'legacyHtml';
        }
        return $ret;
    }
}
