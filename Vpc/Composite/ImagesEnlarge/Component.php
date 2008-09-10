<?php
class Vpc_Composite_ImagesEnlarge_Component extends Vpc_Composite_Images_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Basic_Image_Enlarge_Title_Component';
        $ret['componentName'] = trlVps('Gallery');
        $ret['assets']['files'][] = 'vps/Vpc/Composite/ImagesEnlarge/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $images = $this->getData()->getChildComponents(array(
            'generator' => 'child'
        ));
        $ret['smallMaxWidth'] = 0;
        $ret['smallMaxHeight'] = 0;
        foreach ($images as $image) {
            $img = $image->getComponent()->getSmallImage();
            $ret['smallMaxWidth'] = max($ret['smallMaxWidth'], $img['width']);
            $ret['smallMaxHeight'] = max($ret['smallMaxHeight'], $img['height']);
        }

        return $ret;
    }
}
