<?php
class Vpc_Menu_Expanded_MenuModel extends Vpc_Menu_Abstract_MenuModel
{
    protected function _getMenuData($parentComponent)
    {
        return $this->_menuComponent->getComponent()->getMenuData($parentComponent);
    }
}