<?php
class Kwc_Basic_Headline_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $ret = array();
        $row = $data->getComponent()->getRow();
        $ret['headline1'] = $row->headline1;
        $ret['headline2'] = $row->headline2;

        $ret['headline1'] = str_replace('[-]', '&shy;', $ret['headline1']);
        $ret['headline2'] = str_replace('[-]', '&shy;', $ret['headline2']);

        $headlines = Kwc_Abstract::getSetting($data->componentClass, 'headlines');
        if ($row->headline_type && isset($headlines[$row->headline_type])) {
            $ret['headlineType'] = $headlines[$row->headline_type];
        } else {
            $ret['headlineType'] = reset($headlines);
        }
        unset($ret['headlineType']['text']);

        return $ret;
    }
}
