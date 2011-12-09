<?php
class Kwc_Abstract_Ajax_ContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    protected function _render()
    {
        $view = new Kwf_Component_Renderer();
        return $view->renderComponent($this->_data);
    }
}
