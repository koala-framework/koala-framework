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
            $admin->delete($this->page_id, $this->component_key);
        }
    }

    protected function _createCacheFile($source, $target, $type)
    {
        if ($type == 'small') {
            $componentClass = $this->getTable()->getComponentClass();
            $settings = Vpc_Abstract::getSetting($componentClass, 'smallImageSettings');
            Vps_Media_Image::scale($source, $target, $settings['dimension'], $settings['scale']);
        } else {
            parent::_createCacheFile($source, $target, $type);
        }
    }
}
