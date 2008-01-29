<?php
class Vps_Auto_Data_Vpc_Frontend extends Vps_Auto_Data_Abstract
{
    public function load($row)
    {
        $class = $row->component_class;
        $id = $row->component_id . '-' . $row->id;

        $dao = Zend_Registry::get('dao');
        $pageCollection = new Vps_PageCollection_TreeBase(Zend_Registry::get('dao'));
        $component = $pageCollection->getComponentById($id);
//         if (!$component) {
//             $component = Vpc_Abstract::createInstance(Zend_Registry::get('dao'), $class, $id);
//         }

        $view = new Vps_View_Smarty();
        $view->setRenderFile(VPS_PATH . '/views/Component.html');
        $view->component = $component->getTemplateVars();
        $view->mode = '';
        $html = $view->render('');
        return $html;
    }
}
