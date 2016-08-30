<?php
class Kwf_Data_Kwc_Frontend extends Kwf_Data_Abstract
{
    private $_componentClass;

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
    }

    public function load($row, array $info = array())
    {
        $id = $row->component_id.'-'.$row->id;
        $data = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $id,
            array('ignoreVisible' => true, 'limit' => 1)
        );
        if (!$data) {
            return "Component with '$id' not found";
        }
        $class = $data->componentClass;
        if (is_instance_of($class, 'Kwc_Abstract')) {

            $process = $data
                ->getRecursiveChildComponents(array(
                        'page' => false,
                        'flags' => array('processInput' => true),
                        'ignoreVisible' => true
                    ));
            if (Kwf_Component_Abstract::getFlag($data->componentClass, 'processInput')) {
                $process[] = $data;
            }
            foreach ($process as $i) {
                if (method_exists($i->getComponent(), 'preProcessInput')) {
                    $i->getComponent()->preProcessInput(array());
                }
            }
            foreach ($process as $i) {
                if (method_exists($i->getComponent(), 'processInput')) {
                    $i->getComponent()->processInput(array());
                }
            }
            return $data->render($data->isVisible(), false);
            //$view = new Kwf_Component_Renderer();
            //return $view->renderComponent($data);
        } else if (isset($row->settings)) {
            $settingsModel = new Kwf_Model_Field(array(
                'parentModel' => $row->getModel(),
                'fieldName' => 'settings'
            ));
            $f = new $class();
            $f->setProperties($settingsModel->getRowByParentRow($row)->toArray());

            $vars = $f->getTemplateVars(array());

            $view = new Kwf_View_Ext();
            $view->item = $vars;
            return $view->render('field.tpl');
        }
    }
}
