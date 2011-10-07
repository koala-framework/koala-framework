<?php
class Kwf_Component_Plugin_AccessByMail_Component extends Kwf_Component_Plugin_View_Abstract
    implements Kwf_Component_Plugin_Interface_Login
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['form'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Plugin_AccessByMail_Form_Component'
        );
        return $ret;
    }

    public function isLoggedIn()
    {
        $ret = null;
        $data = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId);
        while (!$ret && $data) {
            $session = new Zend_Session_Namespace('kwc_'.$data->componentId);
            $ret = $session->login;
            $data = $data->parent;
        }
        return $ret;
    }

    public function processOutput($output)
    {
        if ($this->isLoggedIn()) {
            return $output;
        }

        $form = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId, array('ignoreVisible' => true))->getChildComponent('-form');

        $templateVars = array();
        $templateVars['form'] = $form;

        $template = Kwc_Admin::getComponentFile($this, 'Component', 'tpl');
        $view = new Kwf_Component_View();
        $view->assign($templateVars);
        return $view->render($template);
    }
}
