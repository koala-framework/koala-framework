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

    public function getImageKey()
    {
        return $this->getData()->parent->getComponent()->getVariant();
    }

    protected function _getImageDimensions()
    {
        $dimensions = Vpc_Abstract::getSetting($this->getData()->parent->componentClass, 'dimensions');
        $variant = $this->getData()->parent->getComponent()->getVariant();
        return $dimensions[$variant];
    }

    public function getCacheMeta()
    {
        $ret = parent::getCacheMeta();
        $model = $this->getData()->parent->getComponent()->getRow()->getModel();
        // wenn sich im Setting die Bildgröße ändert, müssen alle Komponenten gelöscht
        // werden (Bilder nicht, haben eigenen Image Key für die Größe)
        $ret[] = new Vps_Component_Cache_Meta_Static_Model($model, '{component_id}-%');
        return $ret;
    }
}
