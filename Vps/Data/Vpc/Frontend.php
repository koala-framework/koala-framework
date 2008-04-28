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
        $class = $row->component_class;
        if (is_subclass_of($class, 'Vpc_Abstract')) {
            $id = $row->component_id . '-' . $row->id;

            $tc = new Vps_Dao_TreeCache();
            $row = $tc->find($id)->current();
            if (!$row) {
                return 'Could not create component: ' . $id;
            } else {
                $view = new Vps_View_Smarty();
                $view->setRenderFile(VPS_PATH . '/views/Component.html');
                $templateVars = $row->getComponent()->getTemplateVars();
                $view->assets = $templateVars['assets'];
                $view->component = $id;
                return $view->render('');
            }
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

            $view = new Vps_View_Smarty();
            $view->item = $vars;
            $view->setRenderFile(VPS_PATH . '/Vpc/Formular/field.tpl');
            return $view->render('');
        }
    }
}
