<?php
class Vpc_ParagraphsPassword_Plugin_Component extends Vps_Component_Plugin_Password_Component
{
    protected function _getPassword()
    {
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId, array('ignoreVisible'=>true));
        return $c->getComponent()->getPassword();
    }
}
