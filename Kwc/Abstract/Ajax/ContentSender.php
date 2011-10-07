<?php
class Vpc_Abstract_Ajax_ContentSender extends Vps_Component_Abstract_ContentSender_Default
{
    public function sendContent()
    {
        header('Content-Type: text/html; charset=utf-8');
        $process = $this->_callProcessInput();
        $view = new Vps_Component_Renderer();
        echo $view->renderComponent($this->_data);
        $this->_callPostProcessInput($process);
    }
}
