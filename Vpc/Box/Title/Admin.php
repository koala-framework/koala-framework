<?php
class Vpc_Box_Title_Admin extends Vpc_Admin
{
    protected function _onRowAction($row)
    {
        parent::_onRowAction($row);
        if ($row instanceof Vps_Component_Model_Row) {
            Vps_Component_Cache::getInstance()->cleanComponentClass($this->_class);
            return;
        }
        foreach ($this->_getGeneratorsForRow($row) as $generator) {
            if (!is_instance_of($generator['class'], 'Vps_Component_Generator_PseudoPage_Interface')) continue;
            Vps_Component_Cache::getInstance()->cleanComponentClass($this->_class);
            return;
        }
    }
}
