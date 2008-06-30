<?php
class Vpc_Basic_Image_Enlarge_Row extends Vpc_Basic_Image_Row
{
    protected function _delete()
    {
        parent::_delete();
        $componentClass = $this->getTable()->getComponentClass();
        $classes = Vpc_Abstract::getSetting($componentClass, 'childComponentClasses');
        $admin = Vpc_Admin::getInstance($classes['smallImage']);
        if ($admin) {
            $admin->delete($this->component_id);
        }
    }

    protected function _createCacheFile($source, $target, $type)
    {
        if ($type == 'small') {
            $componentClass = $this->getTable()->getComponentClass();
            $childComponentClasses = Vpc_Abstract::getSetting($componentClass, 'childComponentClasses');
            $childComponentClass = $childComponentClasses['smallImage'];
            $dimension = Vpc_Abstract::getSetting($childComponentClass, 'dimension');
            $scale = Vpc_Abstract::getSetting($childComponentClass, 'scale');
            if (is_array($scale)) {
                $scale = isset($scale[0]) ? $scale[0] : null;
            }
            Vps_Media_Image::scale($source, $target, $dimension, $scale);
        } else {
            parent::_createCacheFile($source, $target, $type);
        }
    }
}
