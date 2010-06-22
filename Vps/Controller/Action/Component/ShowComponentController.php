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
        
        $process = $c
            ->getRecursiveChildComponents(array(
                    'page' => false,
                    'flags' => array('processInput' => true)
                ));
        if (Vps_Component_Abstract::getFlag($c->componentClass, 'processInput')) {
            $process[] = $this->getData();
        }
        $postData = array();
        foreach ($process as $i) {
            Vps_Benchmark::count('processInput', $i->componentId);
            if (method_exists($i->getComponent(), 'preProcessInput')) {
                $i->getComponent()->preProcessInput($postData);
            }
        }
        foreach ($process as $i) {
            if (method_exists($i->getComponent(), 'processInput')) {
                $i->getComponent()->processInput($postData);
            }
        }        

        /*
        //deaktivert: funktioniert nicht
        $c->getComponent()->sendContent('views/component-master.tpl', true);
        */

        //zwischenlösung:
        //(unschön: keine assets, kein html-header usw)
        $output = new Vps_Component_View();
        echo $output->render($c);

        Vps_Benchmark::output();
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
