<?php
class Vpc_Composite_ImagesEnlarge_Component extends Vpc_Composite_Images_Component
{
    public static function getSettings()
    {
        $settings = parent::getSettings();
        $settings['childComponentClasses']['child'] = 'Vpc_Basic_Image_Enlarge_Title_Component';
        $settings['componentName'] = 'Images Enlarge';
        $settings['assets']['files'][] = 'vps/Vpc/Composite/ImagesEnlarge/Component.js';
        $settings['assets']['dep'][] = 'ExtCore';
        return $settings;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $images = $this->getData()->getChildComponents(array(
            'treecache' => 'Vpc_Abstract_List_TreeCache'
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
