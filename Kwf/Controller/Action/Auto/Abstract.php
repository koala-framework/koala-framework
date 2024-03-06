<?php
abstract class Kwf_Controller_Action_Auto_Abstract extends Kwf_Controller_Action
{
    protected $_buttons = array();
    protected $_permissions;
    private $_helpText;

    public function init()
    {
        parent::init();


        if (!isset($this->_permissions)) {
            $this->_permissions = $this->_buttons;
        }

        $btns = array();
        foreach ($this->_buttons as $k=>$i) {
            if (is_int($k)) {
                $btns[$i] = true;
            } else {
                $btns[$k] = $i;
            }
        }
        $this->_buttons = $btns;

        $perms = array();
        foreach ($this->_permissions as $k=>$i) {
            if (is_int($k)) {
                $perms[$i] = true;
            } else {
                $perms[$k] = $i;
            }
        }
        $this->_permissions = $perms;
    }

    public final function setHelpText($helpText)
    {
        $this->_helpText = $helpText;
    }

    public final function getHelpText()
    {
        return $this->_helpText;
    }

    public function postDispatch()
    {
        if (Kwf_Config::getValue('debug.activityLog')) $this->_activityLog();

        parent::postDispatch();
    }

    private function _activityLog()
    {
        $userId = Kwf_Registry::get('userModel')->getAuthedUserId();
        if (!$userId) return;

        $log = array(
            'date' => date('Y-m-d H:i:s'),
            'ip' => $this->getRequest()->getClientIp(),
            'class' => get_class($this),
            'method' => $this->getRequest()->getMethod(),
            'path' => $this->getRequest()->getPathInfo(),
            'query' => json_encode($this->getRequest()->getQuery()),
            'user' => $userId
        );

        $path = 'log/activity/' . date('Y-m-d');
        $filename = date('H') . '-00-00.log';

        if (!is_dir($path)) @mkdir($path, 0777, true);
        file_put_contents(
            "{$path}/{$filename}",
            implode(
                " | ",
                array_map(function ($key) use($log) { return "{$key}={$log[$key]}"; }, array_keys($log))
            ) . PHP_EOL,
            FILE_APPEND
        );
    }
}
