<?php
class Vpc_Formular_IndexController extends Vpc_Paragraphs_IndexController
{
    public function indexAction()
    {
        $components = array();
        foreach (Vpc_Setup_Abstract::getAvailableComponents('Formular/') as $component) {
            if ($component != 'Vpc_Formular_Index') {
                $components[$component] = $component;
            }
        }

        $cfg = array();
        $cfg['components'] = $components;
        $this->view->ext('Vpc.Formular.Index', $cfg);
    }

    protected function _getTable()
    {
        return Zend_Registry::get('dao')->getTable('Vpc_Formular_IndexModel');
    }
}