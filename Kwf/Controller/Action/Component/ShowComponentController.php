<?php
class Kwf_Controller_Action_Component_ShowComponentController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $id = $this->_getParam('id');
        if (!$id) {
            throw new Kwf_ClientException("Missing Parameter: id");
        }
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($id, array('ignoreVisible'=>true));
        if (!$c) {
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($id, array('ignoreVisible'=>true));
        }
        if (!$c) {
            throw new Kwf_ClientException("Component with id '$id' not found");
        }

        $process = $c
            ->getRecursiveChildComponents(array(
                    'page' => false,
                    'flags' => array('processInput' => true)
                ));
        if (Kwf_Component_Abstract::getFlag($c->componentClass, 'processInput')) {
            $process[] = $c;
        }
        $postData = array();
        foreach ($process as $i) {
            Kwf_Benchmark::count('processInput', $i->componentId);
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
        echo $c->render();

        Kwf_Benchmark::output();
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
