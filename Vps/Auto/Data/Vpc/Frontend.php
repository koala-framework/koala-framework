<?php
class Vps_Auto_Data_Vpc_Frontend extends Vps_Auto_Data_Abstract
{
    public function load($row)
    {
        $class = $row->component_class;
        $id = $row->component_id . '-' . $row->id;

        $tc = new Vps_Dao_TreeCache();
        $row = $tc->find($id)->current();
        if (!$row) {
            return 'Could not create component: ' . $id;
        } else {
            $view = new Vps_View_Smarty();
            $view->setRenderFile(VPS_PATH . '/views/Component.html');
            $view->component = $row->getComponent()->getTemplateVars();
            $view->mode = '';
            $html = $view->render('');
            return $html;
        }
    }
}
