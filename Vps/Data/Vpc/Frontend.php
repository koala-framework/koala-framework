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
        $data = Vps_Component_Data_Root::getInstance()->getByDbId($row->component_id)
             ->getChildComponent('-'.$row->id);
        $class = $data->componentClass;
        if (is_subclass_of($class, 'Vpc_Abstract')) {
            return Vps_View_Component::renderCachedComponent($data);
        } else if (isset($row->settings)) {
            $settingsModel = new Vps_Model_Field(array(
                'parentModel' => $row->getModel(),
                'fieldName' => 'settings'
            ));
            $f = new $class();
            $f->setProperties($settingsModel->getRowByParentRow($row)->toArray());

            $vars = $f->getTemplateVars(array());

            $dec = Vpc_Abstract::getSetting($this->_componentClass, 'decorator');

            if ($dec && is_string($dec)) {
                $dec = new $dec();
                $vars = $dec->processItem($vars);
            }

            $view = new Vps_View_Ext();
            $view->item = $vars;
            return $view->render('field.tpl');
        }
    }
}
