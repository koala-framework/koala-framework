<?php
class Vpc_Menu_Admin extends Vpc_Admin
{
    public function onRowUpdate($row)
    {
        parent::onRowUpdate($row);
        self::_deleteCache($row);
    }

    public function onRowDelete($row)
    {
        parent::onRowDelete($row);
        self::_deleteCache($row);
    }
    
    private function _deleteCache($row)
    {
        if ($row->getTable() instanceof Vps_Dao_Pages) {
            Vps_Component_Cache::getInstance()->remove($this->_class);
        }
        foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
            foreach (Vpc_Abstract::getSetting($componentClass, 'generators') as $generator) {
                if ((is_instance_of($generator['class'], 'Vps_Component_Generator_Page_Interface') &&
                    isset($generator['table']) &&
                    isset($generator['showInMenu']) && $generator['showInMenu']))
                {
                    if (is_instance_of($generator['table'], $generator['table'])) {
                        Vps_Component_Cache::getInstance()->remove($componentClass);
                    }
                }
            }
        }
    }

    public function onRowInsert($row)
    {
        parent::onRowInsert($row);
        self::_deleteCache($row);
    }
}
