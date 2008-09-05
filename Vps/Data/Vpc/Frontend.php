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
        $data = Vps_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible' => true));
        if (!$data) {
            return "Component with '$id' not found";
        }
        $class = $data->componentClass;
        if (is_subclass_of($class, 'Vpc_Abstract')) {
            return Vps_View_Component::renderComponent($data, true);
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
