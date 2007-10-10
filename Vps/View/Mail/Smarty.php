<?php
class Vps_View_Mail_Smarty extends Vps_View_Smarty
{
    protected $_renderFile = 'mails/Master.txt';

    protected function _run()
    {
        $path = $this->getScriptPaths();
        $this->template = substr(func_get_arg(0), strlen($path[0]));
        parent::_run();
    }
    
}