<?php
class Vps_Controller_Action_Component_ShowComponentController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $id = $this->_getParam('id');
        if (!$id) {
            throw new Vps_ClientException("Missing Parameter: id");
        }
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentById($id, array('ignoreVisible'=>true));
        if (!$c) {
            $c = Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId($id, array('ignoreVisible'=>true));
        }
        if (!$c) {
            throw new Vps_ClientException("Component with id '$id' not found");
        }
        /*
        //deaktivert: funktioniert nicht
        $c->getComponent()->sendContent('views/component-master.tpl', true);
        */

        //zwischenlösung:
        //(unschön: keine assets, kein html-header usw)
        $output = new Vps_Component_Output_NoCache();
        echo $output->render($c);

        Vps_Benchmark::output();
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
