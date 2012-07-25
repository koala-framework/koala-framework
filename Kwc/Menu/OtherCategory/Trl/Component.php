<?php
class Kwc_Menu_OtherCategory_Trl_Component extends Kwc_Menu_OtherCategory_Component
{
    protected function _getMenuComponentClass()
    {
        return Kwc_Abstract::getSetting(
            $this->getData()->chained->componentClass, 'menuComponentClass'
        );
    }
}

