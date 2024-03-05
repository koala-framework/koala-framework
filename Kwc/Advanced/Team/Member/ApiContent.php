<?php
class Kwc_Advanced_Team_Member_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $personRow = $data->getChildComponent('-data')->getComponent()->getRow();
        $vcardCmp = $data->getChildComponent('-data')->getChildComponent('_vcard');
        $personData = $personRow->toArray();
        unset($personData['data']);
        unset($personData['component_id']);
        return array(
            'vcardDownloadUrl' => $vcardCmp->getAbsoluteUrl(),
            'person' => $personData,
            'image' => $data->getChildComponent('-image')
        );
    }
}
