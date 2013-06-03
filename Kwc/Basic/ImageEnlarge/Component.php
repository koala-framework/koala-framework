<?php
class Kwc_Basic_ImageEnlarge_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Image Enlarge');
        $ret['componentIcon'] = new Kwf_Asset('imageEnlarge');
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_Basic_ImageEnlarge_EnlargeTag_Component';
        $ret['generators']['child']['addUrlPart'] = false;
        $ret['assets']['dep'][] = 'KwfOnReady';
        $ret['assets']['files'][] = 'kwf/Kwc/Basic/ImageEnlarge/Component.js';
        $ret['cssClass'] = Kwf_Config::getValue('kwc.imageEnlarge.cssClass'); //default is showHoverIcon
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        return $ret;
    }

    public function getImageData()
    {
        $c = $this->getData()->getChildComponent('-linkTag');
        if (is_instance_of($c->componentClass, 'Kwc_Basic_LinkTag_Component')) {
            $c = $c->getChildComponent('-child');
        }
        if (is_instance_of($c->componentClass, 'Kwc_Basic_ImageEnlarge_EnlargeTag_Component')) {
            if (Kwc_Abstract::getSetting($c->componentClass, 'alternativePreviewImage')
                && $c->getComponent()->getRow()->preview_image
            ) {
                $r = $c->getComponent()->getAlternativePreviewImageData();
                if ($r) {
                    return $r;
                }
            }
        }
        return parent::getImageData();
    }

    public function getOwnImageData()
    {
        return parent::getImageData();
    }
}
