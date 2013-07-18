<?php
class Kwc_User_BoxWithoutLogin_AppendChildComponentPlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewBeforeCache
{
    public function processOutput($output, $renderer)
    {
        // append data of child-component to view-cache
        $loggedIn = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId, array('ignoreVisible' => true))
            ->getChildComponent('-loggedIn');
        $h = new Kwf_Component_View_Helper_Component();
        $h->setRenderer($renderer);
        return $output.'{kwfappendboxdata}'.$h->component($loggedIn);
    }
}
