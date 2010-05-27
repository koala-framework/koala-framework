<?php
class Vpc_Basic_ImageEnlarge_EnlargeTag_Trl_Component extends Vpc_Abstract_Image_Trl_Component
{
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
}
