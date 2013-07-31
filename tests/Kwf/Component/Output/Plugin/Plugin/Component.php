<?php
class Kwf_Component_Output_Plugin_Plugin_Component extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewAfterChildRender
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['pluginChild'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Output_Plugin_Plugin_Child_Component'
        );
        return $ret;
    }

    public function processOutput($output, $renderer)
    {
        // Da das Plugin nach dem Rendern ausgeführt wird, muss schon der
        // fertige Content hier reinkommen
        if ($output != 'root plugin(plugin(c1_child c1_childchild))') {
            return 'not ok from plugin. output was: ' . $output;
        } else {
            $template = Kwc_Admin::getComponentFile($this, 'Component', 'tpl');
            $renderer = new Kwf_Component_Renderer();
            $view = new Kwf_Component_View($renderer);
            $view->child = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($this->_componentId)
                ->getChildComponent('-pluginChild');
            return $renderer->render($view->render($template));
        }
    }
}
