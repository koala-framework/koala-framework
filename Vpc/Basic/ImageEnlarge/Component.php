<?php
class Vpc_Basic_ImageEnlarge_Component extends Vpc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Image Enlarge');
        $ret['componentIcon'] = new Vps_Asset('imageEnlarge');
        $ret['generators']['child']['component']['linkTag'] = 'Vpc_Basic_ImageEnlarge_EnlargeTag_Component';
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
        $model = Vpc_Abstract::getSetting($componentClass, 'ownModel');
        $ret[] = new Vps_Component_Cache_Meta_Static_Model($model, '{component_id}-linkTag');
        $ret[] = new Vps_Component_Cache_Meta_Static_Callback($model, '{component_id}-linkTag');
        return $ret;
    }

    public function getImageData()
    {
        $c = $this->getData()->getChildComponent('-linkTag');
        if (is_instance_of($c->componentClass, 'Vpc_Basic_LinkTag_Component')) {
            $c = $c->getChildComponent('-child');
        }
        if (is_instance_of($c->componentClass, 'Vpc_Basic_ImageEnlarge_EnlargeTag_Component')) {
            if (Vpc_Abstract::getSetting($c->componentClass, 'alternativePreviewImage')
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
