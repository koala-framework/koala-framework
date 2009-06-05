<?php
class Vps_Component_Output_Mail extends Vps_Component_Output_NoCache
{
    private $_user;

    public function setUser($user)
    {
        $this->_user = $user;
    }

    protected function _renderContent($componentId, $componentClass, $masterTemplate, $useCache = false)
    {
        $output = new Vps_Component_Output_ComponentMail();
        $output->setIgnoreVisible($this->ignoreVisible());
        return $output->render($this->_getComponent($componentId), $this->_user);
    }
}
