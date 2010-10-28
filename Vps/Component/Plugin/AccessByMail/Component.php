<?php
class Vps_Component_Plugin_AccessByMail_Component extends Vps_Component_Plugin_View_Abstract
    implements Vps_Component_Plugin_Interface_Login
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['form'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Plugin_AccessByMail_Form_Component'
        );
        return $ret;
    }

    public function isLoggedIn()
    {
        $session = new Zend_Session_Namespace('vpc_'.$this->_componentId);
        return $session->login;
    }

    public function processOutput($output)
    {
        if ($this->isLoggedIn()) {
            return $output;
        }

        $form = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId, array('ignoreVisible' => true))->getChildComponent('-form');

        $templateVars = array();
        $templateVars['form'] = $form;

        $template = Vpc_Admin::getComponentFile($this, 'Component', 'tpl');
        $renderer = new Vps_Component_Renderer();
        $view = new Vps_Component_View($renderer);
        $view->assign($templateVars);
        return $view->render($template);
    }
}
