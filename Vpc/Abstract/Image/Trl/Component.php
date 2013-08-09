<?php
class Vpc_Abstract_Image_Trl_Component extends Vpc_Abstract_Composite_Trl_Component
    implements Vps_Media_Output_IsValidInterface
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Abstract_Image_Trl_Image_Component.'.$masterComponentClass
        );
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['image'] = $this->getData();
        $imageCaptionSetting = Vpc_Abstract::getSetting($this->_getSetting('masterComponentClass'), 'imageCaption');
        if ($imageCaptionSetting) {
            $ret['image_caption'] = $this->getRow()->image_caption;
        }
        return $ret;
    }

    public function getImageUrl()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent()->getImageUrl();
        } else {
            $data = $this->getData()->chained->getComponent()->getImageDataOrEmptyImageData();
            if ($data && $data['filename']) {
                $id = $this->getData()->componentId;
                return Vps_Media::getUrl($this->getData()->componentClass, $id, 'default', $data['filename']);
            }
        }
        return null;
    }

    public function getImageDimensions()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent()->getImageDimensions();
        } else {
            return $this->getData()->chained->getComponent()->getImageDimensions();
        }
    }

    public function hasContent()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->hasContent();
        }
        return $this->getData()->chained->hasContent();
    }

    public function getExportData()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent()->getExportData();
        }
        $ret = parent::getExportData();
        $ret['imageUrl'] = $this->getImageUrl();
        return $ret;
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        if (Vps_Component_Data_Root::getInstance()->getComponentById($id)) {
            return self::VALID;
        }
        if (Vps_Registry::get('config')->showInvisible) {
            //preview im frontend
            if (Vps_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true))) {
                return self::VALID_DONT_CACHE;
            }
        }

        //paragraphs vorschau im backend
        $authData = Vps_Registry::get('userModel')->getAuthedUser();
        if ($authData) {
            return self::VALID_DONT_CACHE;
        }

        return self::INVALID;
    }

    //if own_image getMediaOutput of image child component is used
    public static function getMediaOutput($id, $type, $className)
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentById($id);
        return call_user_func(
            array(get_class($c->chained->getComponent()), 'getMediaOutput'),
            $c->chained->componentId, $type, $c->chained->componentClass
        );
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $model = Vpc_Abstract::getSetting(Vpc_Abstract::getSetting($componentClass, 'masterComponentClass'), 'ownModel');
        $ret[] = new Vps_Component_Cache_Meta_Static_Callback($model);
        return $ret;
    }

    public function onCacheCallback($row)
    {
        $cacheId = Vps_Media::createCacheId(
            $this->getData()->componentClass, $this->getData()->componentId, 'default'
        );
        Vps_Media::getOutputCache()->remove($cacheId);
    }
}
