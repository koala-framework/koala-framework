<?php
class Vps_Data_Vpc_Frontend extends Vps_Data_Abstract
{
    private $_componentClass;

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
    }

    public function load($row)
    {
        $id = $row->component_id.'-'.$row->id;
        $data = Vps_Component_Data_Root::getInstance()->getComponentByDbId(
            $id,
            array('ignoreVisible' => true, 'limit' => 1)
        );
        if (!$data) {
            return "Component with '$id' not found";
        }
        $class = $data->componentClass;
        if (is_instance_of($class, 'Vpc_Abstract')) {

            $process = $data
                ->getRecursiveChildComponents(array(
                        'page' => false,
                        'flags' => array('processInput' => true),
                        'ignoreVisible' => true
                    ));
            if (Vps_Component_Abstract::getFlag($data->componentClass, 'processInput')) {
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
            $view = new Vps_Component_Renderer();
            $view->setEnableCache(true);
            return $view->renderComponent($data);
        } else if (isset($row->settings)) {
            $settingsModel = new Vps_Model_Field(array(
                'parentModel' => $row->getModel(),
                'fieldName' => 'settings'
            ));
            $f = new $class();
            $f->setProperties($settingsModel->getRowByParentRow($row)->toArray());

            $vars = $f->getTemplateVars(array());

            $view = new Vps_View_Ext();
            $view->item = $vars;
            return $view->render('field.tpl');
        }
    }
}
