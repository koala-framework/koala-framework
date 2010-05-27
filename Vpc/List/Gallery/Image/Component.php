<?php
class Vpc_List_Gallery_Image_Component extends Vpc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trl('Bild');
        $ret['generators']['child']['component']['linkTag'] =
            'Vpc_List_Gallery_Image_LinkTag_Component';
        $ret['imageCaption'] = true;
        // dimensions gibts hier nicht, die werden von der parent list geholt,
        // damit im web nich soviel überschrieben werden muss
        // $ret['dimensions'] = array();
        return $ret;
    }

    protected function _getImageDimensions()
    {
        $dimensions = Vpc_Abstract::getSetting($this->getData()->parent->componentClass, 'dimensions');
        $variant = $this->getData()->parent->getComponent()->getVariant();
        return $dimensions[$variant];
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $settingsRow = $this->getData()->parent->getComponent()->getRow();

        $ret[] = array(
            'model' => $settingsRow->getModel(),
            'id' => $settingsRow->component_id
        );
        $ret[] = array(
            'model' => $settingsRow->getModel(),
            'id' => $settingsRow->component_id,
            'callback' => true
        );
        return $ret;
    }
}
