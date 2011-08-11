<?php
abstract class Vpc_Abstract_Ajax_Component extends Vpc_Abstract
{
    public function sendContent()
    {
        header('Content-Type: text/html; charset=utf-8');
        $process = $this->_callProcessInput();
        $view = new Vps_Component_Renderer();
        echo $view->renderComponent($this->getData());
        $this->_callPostProcessInput($process);
    }
}
