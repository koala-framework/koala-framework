<?php
class Vpc_Advanced_GoogleMap_Row extends Vps_Db_Table_Row_Abstract
{
    protected function _delete()
    {
        parent::_delete();
        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                            'childComponentClasses');
        Vpc_Admin::getInstance($classes['text'])->delete($this->component_id.'-text');

    }
}
