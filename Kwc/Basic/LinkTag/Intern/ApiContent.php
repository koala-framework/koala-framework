<?php
class Kwc_Basic_LinkTag_Intern_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $targetData = $data->getLinkedData();
        $ret = array(
            'rel' => $data->rel,
            'class' => $data->getLinkClass(),
            'dataAttributes' => $data->getLinkDataAttributes(),
        );
        return array_merge($ret, $this->getTargetLinkContent($targetData));
    }

    public function getTargetLinkContent($targetData)
    {
        if (!$targetData || !Kwc_Abstract::hasSetting($targetData->componentClass, 'apiContentType')) {
            return array();
        }
        $targetContentType = Kwc_Abstract::getSetting($targetData->componentClass, 'apiContentType');
        if (is_instance_of (Kwc_Abstract::getSetting($targetData->componentClass, 'contentSender'), 'Kwf_Component_Abstract_ContentSender_Lightbox')) {
            $targetContentType = 'lightbox';
        }
        return array(
            "href" => $targetData->url,
            "targetId" => $targetData->componentId,
            "targetContentType" => $targetContentType
        );
    }
}
