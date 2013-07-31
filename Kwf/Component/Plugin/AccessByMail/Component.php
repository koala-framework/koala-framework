<?php
class Kwf_Component_Plugin_AccessByMail_Component extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_Login, Kwf_Component_Plugin_Interface_ViewReplace, Kwf_Component_Plugin_Interface_SkipProcessInput
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
            $session = new Kwf_Session_Namespace('kwc_'.$data->componentId);
            $ret = $session->login;
            $data = $data->parent;
        }
        return $ret;
    }

    public function replaceOutput($renderer)
    {
        if ($this->isLoggedIn()) {
            return false;
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

    public function skipProcessInput()
    {
        return !$this->isLoggedIn();
    }
}
