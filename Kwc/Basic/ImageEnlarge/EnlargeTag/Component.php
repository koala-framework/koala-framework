<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Component extends Kwc_Abstract
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Enlarge Image');
        $ret['fullSizeDownloadable'] = false;
        $ret['imageTitle'] = true;
        $ret['altText'] = false;
        $ret['dimension'] = array('width'=>800, 'height'=>600, 'cover' => false);

        $ret['generators']['imagePage'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => trlKwfStatic('Image'),
            'component' => 'Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Component'
        );

        $ret['ownModel'] = 'Kwf_Component_FieldModel';

        return $ret;
    }

    /**
     * This function is used by Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Component
     * to get the dimension-values defined in getSettings and the crop-values
     * if use_crop was checked.
     */
    public function getImageDimensions()
    {
        $dimension = $this->_getSetting('dimension');
        if ($this->getRow()->use_crop) {
            $parentDimension = $this->_getImageEnlargeComponent()->getImageDimensions();
            $dimension['crop'] = $parentDimension['crop'];
        }
        $data = $this->getImageData();
        return Kwf_Media_Image::calculateScaleDimensions($data['file'], $dimension);
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (isset($settings['showInactiveSwitchLinks'])) {
            throw new Kwf_Exception("'showInactiveSwitchLinks' setting got removed; style them using css");
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imageUrl'] = $this->getImageUrl();
        $ret['imagePage'] = $this->getData()->getChildComponent('_imagePage', array('ignoreVisible'=>true));
        return $ret;
    }

    protected function _getOptions()
    {
        $ret = array();
        if ($this->_getSetting('imageTitle')) {
            $ret['title'] = nl2br($this->getRow()->title);
        }
        if ($this->_getSetting('fullSizeDownloadable')) {
            $data = $this->getImageData();
            if ($data && $data['filename']) {
                $ret['fullSizeUrl'] = Kwf_Media::getUrl($this->getData()->componentClass,
                    $this->getData()->componentId, 'original', $data['filename']);
            }
        }
        return $ret;
    }

    /**
     * This function is called by Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Trl_Component
     */
    public final function getOptions()
    {
        return $this->_getOptions();
    }

    private function _getImageEnlargeComponent()
    {
        $d = $this->getData();
        while (!is_instance_of($d->componentClass, 'Kwc_Basic_Image_Component')) {
            $d = $d->parent;
        }
        return $d->getComponent();
    }

    /**
     * This function is used by Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Component
     * to get the url to show the image from parent with dimension defined through
     * this component.
     */
    public function getImageUrl()
    {
        $baseUrl = $this->getBaseImageUrl();
        if ($baseUrl) {
            $dimensions = $this->getImageDimensions();
            $width = Kwf_Media_Image::getResponsiveWidthStep($dimensions['width'],
                    Kwf_Media_Image::getResponsiveWidthSteps($dimensions, $this->getImageData()));
            return str_replace('{width}', $width, $baseUrl);
        }
        return null;
    }

    public function getBaseImageUrl()
    {
        $data = $this->_getImageEnlargeComponent()->getImageData();
        if ($data) {
            $id = $this->getData()->componentId;
            return Kwf_Media::getUrl($this->getData()->componentClass, $id, Kwf_Media::DONT_HASH_TYPE_PREFIX.'{width}', $data['filename']);
        }
        return null;
    }

    public function getImageData()
    {
        return $this->_getImageEnlargeComponent()->getImageData();
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_Component::isValidImage($id, $type);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        if (!$component) return null;

        $data = $component->getComponent()->getImageData();
        if (!$data) {
            return null;
        }
        $dimension = $component->getComponent()->getImageDimensions();
        // calculate output width/height on base of getImageDimensions and given width
        $width = substr($type, strlen(Kwf_Media::DONT_HASH_TYPE_PREFIX));
        if ($width) {
            $width = Kwf_Media_Image::getResponsiveWidthStep($width,
                    Kwf_Media_Image::getResponsiveWidthSteps($dimension, $data));
            $dimension['height'] = $width / $dimension['width'] * $dimension['height'];
            $dimension['width'] = $width;
        }
        return Kwf_Media_Output_Component::getMediaOutputForDimension($data, $dimension);
     }


    public function getFulltextContent()
    {
        $ret = array();

        //don't call parent, we handle imageCaption ourself
        $options = (object)$this->_getOptions();
        $text = '';
        if (isset($options->title) && $options->title) {
            $text .= ' '.$options->title;
        }
        if (isset($options->imageCaption) && $options->imageCaption) {
            $text .= ' '.$options->imageCaption;
        }
        $text = trim($text);
        $ret['content'] = $text;
        $ret['normalContent'] = $text;
        return $ret;
    }
}
