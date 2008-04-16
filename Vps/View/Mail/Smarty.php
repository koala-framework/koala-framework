<?php
class Vps_View_Mail_Smarty extends Vps_View_Smarty
{
    protected $_renderFile = 'mails/Master.txt';

    public function __construct($config = array())
    {
        parent::__construct($config);
    }
    protected function _run()
    {
        $path = $this->getScriptPaths();
        if (file_exists(func_get_arg(0))) {
            $this->template = func_get_arg(0);
        } else {
            $this->template = substr(func_get_arg(0), strlen($path[0]));
        }
        parent::_run();
    }
    protected function _script($name)
    {
        if (file_exists($name)) return $name;
        return parent::_script($name);
    }
}
