<?php
class Kwc_Basic_ImageParent_Component extends Kwc_Abstract
    implements Kwf_Media_Output_IsValidInterface
{
    const CONTENT_WIDTH = 'contentWidth';
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimension'] = array('width'=>100, 'height'=>100, 'cover' => false);
        $ret['imgCssClass'] = '';
        $ret['lazyLoadOutOfViewport'] = true; // Set to false to load image also when not in view
        $ret['pdfMaxWidth'] = 0;
        $ret['pdfMaxDpi'] = 150;
        $ret['defineWidth'] = false;
        $ret['outputImgTag'] = true;
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (isset($settings['dimensions'])) {
            throw new Kwf_Exception("Don't set dimensions, use dimension for a single one");
        }
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['imgCssClass'] = $this->_getSetting('imgCssClass');
        $ret['style'] = '';
        $ret['containerClass'] = $this->_getBemClass("container");
        $ret['image'] = $this->getData();
        $imageComponent = $this->_getImageComponent();
        if ($imageComponent) {
            $ret['altText'] = $imageComponent->getAltText();

            $imageData = $this->getImageData();
            $ret = array_merge($ret,
                Kwf_Media_Output_Component::getResponsiveImageVars($this->getImageDimensions(), $imageData['file'])
            );
            $ret['style'] .= 'max-width:'.$ret['width'].'px;';
            if ($this->_getSetting('defineWidth')) $ret['style'] .= 'width:'.$ret['width'].'px;';
            if ($ret['width'] > 100) $ret['containerClass'] .= ' kwfUp-webResponsiveImgLoading';
        }
        $ret['baseUrl'] = $this->_getBaseImageUrl();
        $ret['defineWidth'] = $this->_getSetting('defineWidth');
        $ret['lazyLoadOutOfViewport'] = $this->_getSetting('lazyLoadOutOfViewport');
        $ret['outputImgTag'] = $this->_getSetting('outputImgTag');

        if (!$this->_getSetting('lazyLoadOutOfViewport')) $ret['containerClass'] .= ' kwfUp-loadImmediately';

        if (!$renderer instanceof Kwf_Component_Renderer_Mail) { //TODO this check is a hack
            $ret['template'] = Kwf_Component_Renderer_Twig_TemplateLocator::getComponentTemplate('Kwc_Abstract_Image_Component');
        }
        return $ret;
    }

    public function getImageDimensions()
    {
        $dimension = $this->_getSetting('dimension');
        if ($dimension['width'] === self::CONTENT_WIDTH) {
            $dimension['width'] = $this->getContentWidth();
        }
        $parentDimension = $this->_getImageComponent()->getConfiguredImageDimensions();
        if (isset($parentDimension['crop'])) $dimension['crop'] = $parentDimension['crop'];
        $data = $this->getImageData();
        return Kwf_Media_Image::calculateScaleDimensions($data['file'], $dimension);
    }

    protected function _getImageComponent()
    {
        return $this->getData()->parent->getComponent();
    }

    public function getImageData()
    {
        if (!$this->_getImageComponent()) return null;
        return $this->_getImageComponent()->getImageData();
    }

    public function getImageUrl()
    {
        $baseUrl = $this->_getBaseImageUrl();
        if ($baseUrl) {
            $dimensions = $this->getImageDimensions();
            $imageData = $this->getImageData();
            $width = Kwf_Media_Image::getResponsiveWidthStep($dimensions['width'],
                    Kwf_Media_Image::getResponsiveWidthSteps($dimensions, $imageData['file']));
            return str_replace('{width}', $width, $baseUrl);
        }
        return null;
    }

    public function getBaseType()
    {
        $type = Kwf_Media::DONT_HASH_TYPE_PREFIX.'{width}';
        $type .= '-'.substr(md5(json_encode($this->_getSetting('dimension'))), 0, 6);
        return $type;
    }

    private function _getBaseImageUrl()
    {
        $data = $this->getImageData();
        if ($data) {
            $id = $this->getData()->componentId;
            return Kwf_Media::getUrl($this->getData()->componentClass, $id, $this->getBaseType(), $data['filename']);
        }
        return null;
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_Component::isValidImage($id, $type, $className);
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

        return Kwf_Media_Output_Component::getMediaOutputForDimension($data, $dimension, $type);
    }

    public final function getImageDataOrEmptyImageData()
    {
        return $this->_getImageComponent()->getImageDataOrEmptyImageData();
    }
}
