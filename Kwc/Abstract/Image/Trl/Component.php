<?php
class Kwc_Abstract_Image_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Abstract_Image_Trl_Image_Component.'.$masterComponentClass
        );
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['throwContentChangedOnOwnMasterModelUpdate'] = true;
        $ret['throwHasContentChangedOnMasterRowColumnsUpdate'] = array('kwf_upload_id');
        return $ret;
    }

    public function getBaseType()
    {
        return $this->getData()->chained->getComponent()->getBaseType();
    }

    public function getApiData()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent("-image")->getComponent()->getApiData();
        } else {
            return $this->getData()->chained->getComponent()->getApiData();
        }
    }

    public function getBaseImageUrl()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent()->getBaseImageUrl();
        } else {
            $data = $this->getData()->chained->getComponent()->getImageDataOrEmptyImageData();
            if ($data && $data['filename']) {
                if (Kwc_Abstract::getSetting($this->_getSetting('masterComponentClass'), 'editFilename') && $this->_getRow()->filename) {
                    $fn = $this->_getRow()->filename;
                    if ($fn) {
                        $fileRow = $this->getData()->chained->getComponent()->getRow()->getParentRow('Image');
                        $data['filename'] = $fn.'.'.$fileRow->extension;
                    }
                }
                return Kwf_Media::getUrl($this->getData()->componentClass,
                    $this->getData()->componentId,
                    $this->getBaseType(),
                    $data['filename']);
            }
        }
        return null;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['image'] = $this->getData();
        $imageCaptionSetting = Kwc_Abstract::getSetting($this->_getSetting('masterComponentClass'), 'imageCaption');
        if ($imageCaptionSetting) {
            $ret['image_caption'] = $this->getRow()->image_caption;
        }
        $ret['baseUrl'] = $this->getBaseImageUrl();
        if ($this->getRow()->own_image) {
            $imageData = $this->getImageData();
            if ($imageData) {
                $ret = array_merge($ret,
                    Kwf_Media_Output_Component::getResponsiveImageVars($this->getImageDimensions(), $imageData['file'])
                );
            }
        }

        if (Kwc_Abstract::getSetting($this->_getSetting('masterComponentClass'), 'altText')) {
            $ret['altText'] = $this->_getRow()->alt_text;
        }

        if (Kwc_Abstract::getSetting($this->_getSetting('masterComponentClass'), 'titleText')) {
            $ret['imgAttributes']['title'] = $this->_getRow()->title_text;
        }

        return $ret;
    }

    public function getImageData()
    {
        return $this->_getCorrectImageComponent()->getImageData();
    }

    public final function getImageDataOrEmptyImageData()
    {
        return $this->_getCorrectImageComponent()->getImageDataOrEmptyImageData();
    }

    public function getImageUrl()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent()->getImageUrl();
        } else {
            // TODO: Use implementation from non-trl version
            $imageData = $this->getImageDataOrEmptyImageData();
            if ($imageData) {
                $s = $this->getImageDimensions();
                $width = Kwf_Media_Image::getResponsiveWidthStep($s['width'],
                    Kwf_Media_Image::getResponsiveWidthSteps($s, $imageData['file']));
                return str_replace('{width}', $width, $this->getBaseImageUrl());
            }
        }
        return null;
    }

    private function _getCorrectImageComponent()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent();
        } else {
            return $this->getData()->chained->getComponent();
        }
    }

    public function getImageDimensions()
    {
        return $this->_getCorrectImageComponent()->getImageDimensions();
    }

    public function hasContent()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->hasContent();
        }
        return $this->getData()->chained->hasContent();
    }

    public function getExportData()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent()->getExportData();
        }
        $ret = parent::getExportData();
        $ret['imageUrl'] = $this->getImageUrl();
        return $ret;
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_Component::isValidImage($id, $type, $className);
    }

    //if own_image getMediaOutput of image child component is used
    public static function getMediaOutput($id, $type, $className)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        return call_user_func(
            array(get_class($c->chained->getComponent()), 'getMediaOutput'),
            $c->chained->componentId, $type, $c->chained->componentClass
        );
    }
    private function _getAbsoluteUrl($url)
    {
        if ($url && substr($url, 0, 1) == '/' && substr($url, 0, 2) != '//') { //can already be absolute, due to Event_CreateMediaUrl (eg. varnish cache)
            $domain = $this->getData()->getDomain();
            $protocol = Kwf_Util_Https::domainSupportsHttps($domain) ? 'https' : 'http';
            $url = "$protocol://$domain$url";
        }
        return $url;
    }

    public function getAbsoluteImageUrl()
    {
        return $this->_getAbsoluteUrl($this->getImageUrl());
    }
}
