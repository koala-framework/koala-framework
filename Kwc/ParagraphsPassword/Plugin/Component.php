<?php
class Kwc_ParagraphsPassword_Plugin_Component extends Kwf_Component_Plugin_Password_Component
{
    protected function _getPassword()
    {
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId, array('ignoreVisible'=>true));
        return $c->getComponent()->getPassword();
    }
}
