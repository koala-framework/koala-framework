<?php
abstract class Vpc_Abstract_Ajax_Component extends Vpc_Abstract
{
    public function sendContent()
    {
        header('Content-Type: text/html; charset=utf-8');
        echo Vps_View_Component::renderComponent($this->getData(), null);
        Vps_Component_Cache::getInstance()->process();
    }
}
