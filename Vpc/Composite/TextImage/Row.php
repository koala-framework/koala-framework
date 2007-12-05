<?php
class Vpc_Composite_TextImage_Row extends Vps_Db_Table_Row_Abstract
{
    protected function _delete()
    {
        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                            'childComponentClasses');
        Vpc_Admin::getInstance($classes['text'])->delete($this->page_id, $this->component_key.'-text');
        Vpc_Admin::getInstance($classes['image'])->delete($this->page_id, $this->component_key.'-image');

    }
}
