<?php
class Kwc_Basic_ImageEnlarge_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Image Enlarge');
        $ret['componentIcon'] = new Kwf_Asset('imageEnlarge');
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_Basic_ImageEnlarge_EnlargeTag_Component';
        $ret['assets']['files'][] = 'kwf/Kwc/Basic/ImageEnlarge/Component.js';
        $ret['cssClass'] = 'showHoverIcon';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $model = Kwc_Abstract::getSetting($componentClass, 'ownModel');
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model($model, '{component_id}-linkTag');
        $ret[] = new Kwf_Component_Cache_Meta_Static_Callback($model, '{component_id}-linkTag');
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
