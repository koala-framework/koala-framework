<?php
class Vpc_Basic_Image_Enlarge_Row extends Vpc_Basic_Image_Row
{
    protected function _delete()
    {
        parent::_delete();
        $componentClass = $this->getTable()->getComponentClass();
        $class = Vpc_Abstract::getChildComponentClasses($componentClass, 'smallImage');
        $admin = Vpc_Admin::getInstance($class);
        if ($admin) {
            $admin->delete($this->component_id);
        }
    }

    protected function _createCacheFile($source, $target, $type)
    {
        if ($type == 'small') {
            $componentClass = $this->getTable()->getComponentClass();
            $childComponentClass = Vpc_Abstract::getChildComponentClasses($componentClass, 'smallImage');
            $dimensions = Vpc_Abstract::getSetting($childComponentClass, 'dimensions');
            Vps_Media_Image::scale($source, $target, $dimensions);
        } else {
            parent::_createCacheFile($source, $target, $type);
        }
    }
}
