<?php
abstract class Vpc_Abstract_Ajax_Component extends Vpc_Abstract
{
    public function sendContent()
    {
        header('Content-Type: text/html; charset=utf-8');
        $process = $this->_callProcessInput();
        echo Vps_View_Component::renderComponent($this->getData(), null);
        $this->_callPostProcessInput($process);
    }
}
