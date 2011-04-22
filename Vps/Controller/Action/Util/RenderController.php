<?php
class Vps_Controller_Action_Util_RenderController extends Vps_Controller_Action
{
    public function renderAction()
    {
        $id = $this->_getParam('componentId');
        if ($id) {
            $c = Vps_Component_Data_Root::getInstance()->getComponentById($id);
            if (!Vpc_Abstract::getSetting($c->componentClass, 'allowIsolatedRender')) {
                throw new Vps_Exception_AccessDenied('This component must not be rendered this way');
            }

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
            echo $c->render(true);
        }
        exit;
    }
}
