<?php
class Vpc_Menu_Breadcrumbs_Admin extends Vpc_Menu_Admin
{
    public function onRowUpdate($row)
    {
        parent::onRowUpdate($row);
        foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
            foreach (Vpc_Abstract::getSetting($componentClass, 'generators') as $generator) {
                if ((is_instance_of($generator['class'], 'Vps_Component_Generator_Page_Interface') &&
                    isset($generator['table'])))
                {
                    if (is_instance_of(get_class($row->getTable()), $generator['table'])) {
                        Vps_Component_Cache::getInstance()->remove($this->_class);
                        return;
                    }
                }
            }
        }
    }
}
