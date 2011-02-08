<?php
class Vpc_Basic_Image_Component extends Vpc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Image');
        $ret['componentIcon'] = new Vps_Asset('picture');
        $ret['imgCssClass'] = '';
        $ret['emptyImage'] = false; // eg. 'Empty.jpg' in same folder
        $ret['useParentImage'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['imgCssClass'] = $this->_getSetting('imgCssClass');
        return $ret;
    }

    public function getImageData()
    {
        if ($this->_getSetting('useParentImage')) {
            return $this->getData()->parent->getComponent()->getImageData();
        } else {
            return parent::getImageData();
        }
    }

    protected function _getEmptyImageData()
    {
        $emptyImage = $this->_getSetting('emptyImage');
        if (!$emptyImage) return null;
        $ext = substr($emptyImage, strrpos($emptyImage, '.') + 1);
        $filename = substr($emptyImage, 0, strrpos($emptyImage, '.'));
        $file = Vpc_Admin::getComponentFile($this, $filename, $ext);
        $s = getimagesize($file);
        return array(
            'filename' => $emptyImage,
            'file' => $file,
            'mimeType' => $s['mime']
        );
    }

    public function onCacheCallback($row)
    {
        parent::onCacheCallback($row);
        foreach ($this->getData()->getChildComponents(array('page' => false)) as $component) {
            if (is_instance_of($component->componentClass, 'Vpc_Basic_Image_Component') &&
                Vpc_Abstract::getSetting($component->componentClass, 'useParentImage')
            ) {
                $component->getComponent()->onCacheCallback($row);
            }
        }
    }
}
