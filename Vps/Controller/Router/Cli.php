<?php
class Vps_Controller_Router_Cli extends Zend_Controller_Router_Abstract
{
    public function route(Zend_Controller_Request_Abstract $request)
    {
        $request->setModuleName('cli');
        if (!$this->getFrontController()->getDispatcher()->isDispatchable($request)) {
            $request->setModuleName('vps_controller_action_cli');
        }
        if ($request->getControllerName()) {
            if (!$this->getFrontController()->getDispatcher()->isDispatchable($request)) {
                $request->setModuleName('vps_controller_action_cli_web');
            }
            if (!$this->getFrontController()->getDispatcher()->isDispatchable($request)) {
                $request->setModuleName('vps_controller_action_cli_svn');
            }
        }
        return $request;
    }

    protected function _setRequestParams($request, $params)
    {
        foreach ($params as $param => $value) {

            $request->setParam($param, $value);

            if ($param === $request->getModuleKey()) {

            }
            if ($param === $request->getControllerKey()) {
                $request->setControllerName($value);
            }
            if ($param === $request->getActionKey()) {
                $request->setActionName($value);
            }

        }
    }
    public function assemble($userParams, $name = null, $reset = false, $encode = true)
    {
        return '';
    }
}
