<?php
class Vps_Controller_Action_Util_RenderController extends Vps_Controller_Action
{
    public function renderAction()
    {
        //darf nur von cli aus aufgerufen werden
        if ($_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR']) {
            //throw new Vps_Exception_AccessDenied();
        }
        $id = $this->_getParam('componentId');
        if ($id) {
            $c = Vps_Component_Data_Root::getInstance()->getComponentById($id);

            $process = $c->getRecursiveChildComponents(array(
                'page' => false,
                'flags' => array('processInput' => true)
            ));
            if (Vps_Component_Abstract::getFlag($c->componentClass, 'processInput')) {
                $process[] = $c;
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
            //echo $c->render(true);
            echo $c->getComponent()->sendContent();
        }
        exit;
    }
}
