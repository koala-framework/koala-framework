<?php
class Vpc_Basic_ImageEnlarge_EnlargeTag_Trl_Component extends Vpc_Abstract_Image_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image']['component'] =
            'Vpc_Basic_ImageEnlarge_EnlargeTag_Trl_Image_Component.'.$masterComponentClass;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $imageTitleSetting = Vpc_Abstract::getSetting(
            $this->_getSetting('masterComponentClass'), 'imageTitle'
        );
        if ($imageTitleSetting) {
            $ret['options']->title = $this->getRow()->title;
        }

        $masterEnlargeComponentClass = Vpc_Abstract::getSetting(
            $this->_getImageEnlargeComponentData()->componentClass, 'masterComponentClass'
        );

        if (Vpc_Abstract::getSetting($masterEnlargeComponentClass, 'imageCaption')) {
            $ret['options']->imageCaption = $this->_getImageEnlargeComponentData()
                ->getComponent()->getRow()->image_caption;
        }

        $childImageComponent = $this->getData()->getChildComponent('-image')->getComponent();
        $ret['imageUrl'] = $childImageComponent->getImageUrl();
        $size = $childImageComponent->getImageDimensions();
        $ret['width'] = $size['width'];
        $ret['height'] = $size['height'];

        return $ret;
    }

    private function _getImageEnlargeComponentData()
    {
        $d = $this->getData();
        while (!is_instance_of($d->componentClass, 'Vpc_Basic_ImageEnlarge_Trl_Component')) {
            $d = $d->parent;
        }
        return $d;
    }

    public function getCacheMeta()
    {
        $ret = parent::getCacheMeta();

        //own_image checkbox kann sich aendern
        $row = $this->_getImageEnlargeComponentData()->getComponent()->getRow();
        $model = $row->getModel();
        $primaryKey = $model->getPrimaryKey();
        $ret[] = new Vps_Component_Cache_Meta_Model($model);
        return $ret;
    }
}
