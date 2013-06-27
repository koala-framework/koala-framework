<?php
class Kwc_Composite_ImagesEnlarge_Component extends Kwc_List_Images_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Kwc_Basic_ImageEnlarge_Component';
        $ret['componentName'] = trlKwfStatic('Gallery').' '.trlKwfStatic('old');
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
            $img = $image->getComponent()->getImageDimensions();
            $ret['smallMaxWidth'] = max($ret['smallMaxWidth'], $img['width']);
            $ret['smallMaxHeight'] = max($ret['smallMaxHeight'], $img['height']);
        }

        return $ret;
    }
}
