<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_Component extends Kwc_Chained_Trl_Component
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imageUrl'] = $this->getImageUrl();
        $ret['imagePage'] = $this->getData()->getChildComponent('_imagePage');
        return $ret;
    }

    protected function _getOptions()
    {
        $ret = $this->getData()->chained->getComponent()->getOptions();
        if (Kwc_Abstract::getSetting($this->_getSetting('masterComponentClass'), 'imageTitle')) {
            $ret['title'] = nl2br($this->getRow()->title);
        }

        $masterComponentClass = Kwc_Abstract::getSetting(
            $this->_getImageEnlargeComponentData()->componentClass, 'masterComponentClass'
        );
        if (Kwc_Abstract::getSetting($masterComponentClass, 'imageCaption')) {
            $ret['imageCaption'] = $this->_getImageEnlargeComponent()
                ->getRow()->image_caption;
        }
        //TODO implement fullSizeDownloadable
        return $ret;
    }

    /**
     * This function is called by Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Trl_Component
     */
    public final function getOptions()
    {
        return $this->_getOptions();
    }

    /**
     * This function is called by Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Trl_Component
     */
    public function getImageDimension()
    {
        $dimension = $this->getData()->chained->getComponent()->_getSetting('dimension');
        if ($this->getData()->chained->getComponent()->getRow()->use_crop) {
            $parentDimensions = $this->_getImageEnlargeComponent()->getImageDimensions();
            $dimension['crop'] = $parentDimensions['crop'];
        }
        $data = $this->_getImageData();
        return Kwf_Media_Image::calculateScaleDimensions($data['file'], $dimension);
    }

    private function _getImageData()
    {
        return $this->_getImageEnlargeComponent()->getImageData();
    }

    /**
     * This function is called by Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Trl_Component
     */
    public function getImageUrl()
    {
        $data = $this->_getImageData();
        $id = $this->getData()->componentId;
        $type = 'default'; // TODO test if dpr2 enabled
        return Kwf_Media::getUrl($this->getData()->componentClass, $id, $type, $data['filename']);
    }

    private function _getImageEnlargeComponent()
    {
        $d = $this->getData();
        while (!is_instance_of($d->componentClass, 'Kwc_Basic_ImageEnlarge_Trl_Component')) {
            $d = $d->parent;
        }
        return $d->getComponent();
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_Component::isValid($id);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        if (!$component) return null;

        $data = $component->getComponent()->_getImageData();
        if (!$data) {
            return null;
        }
        $dimension = $component->getComponent()->getImageDimension();
        return Kwc_Abstract_Image_Component::getMediaOutputForDimension($data, $dimension);
    }

    public function getCacheMeta()
    {
        $ret = parent::getCacheMeta();

        //own_image checkbox kann sich aendern
        $row = $this->_getImageEnlargeComponent()->getRow();
        $model = $row->getModel();
        $primaryKey = $model->getPrimaryKey();
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model($model);
        return $ret;
    }
}
