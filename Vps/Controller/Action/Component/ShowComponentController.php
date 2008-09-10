<?php
class Vps_Controller_Action_Component_ShowComponentController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $id = $this->_getParam('id');
        if (!$id) {
            throw new Vps_ClientException("Missing Parameter: id");
        }
        $c = Vps_Component_Data_Root::getInstance()->getComponentById($id);
        if (!$c) {
            throw new Vps_ClientException("Component with id '$id' not found");
        }
        echo Vps_View_Component::renderMasterComponent($c, 'views/component-master.tpl');
        Vps_Benchmark::output();
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
