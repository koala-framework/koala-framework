<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Enlarge Image');
        $ret['alternativePreviewImage'] = true;
        $ret['fullSizeDownloadable'] = false;
        $ret['imageTitle'] = true;
        $ret['dimensions'] = array(array('width'=>800, 'height'=>600, 'scale'=>Kwf_Media_Image::SCALE_BESTFIT));

        $ret['generators']['imagePage'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => trlKwfStatic('Image'),
            'component' => 'Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Component'
        );

        $ret['assets']['files'][] = 'kwf/Kwc/Basic/ImageEnlarge/EnlargeTag/Component.js';
        $ret['assets']['dep'][] = 'ExtElement';
        $ret['assets']['dep'][] = 'ExtConnection';
        $ret['assets']['dep'][] = 'ExtDomHelper';
        $ret['assets']['dep'][] = 'ExtUtilJson';
        $ret['assets']['dep'][] = 'ExtFx';

        return $ret;
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

    public final function getOptions()
    {
        return $this->_getOptions();
    }

    private function _getImageEnlargeComponentData()
    {
        $d = $this->getData();
        while (!is_instance_of($d->componentClass, 'Kwc_Basic_Image_Component')) {
            $d = $d->parent;
        }
        return $d;
    }

    public function getImageData()
    {
        $d = $this->_getImageEnlargeComponentData();
        return $d->getComponent()->getOwnImageData();
    }

    public function getAlternativePreviewImageData()
    {
        return parent::getImageData();
    }

    public static function getMediaOutput($id, $type, $className)
    {
        if ($type == 'original') {
            $data = Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($id, array('limit'=>1, 'ignoreVisible' => true))
                ->getComponent()->getImageData();
            if (!$data || !$data['file']) {
                return null;
            }
            return array(
                'file' => $data['file'],
                'mimeType' => 'application/octet-stream',
                'downloadFilename' => $data['filename']
            );
        } else {
            return parent::getMediaOutput($id, $type, $className);
        }
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
