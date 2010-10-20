<?php
class Vps_Component_Output_Plugin_Plugin_Component extends Vps_Component_Plugin_View_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['pluginChild'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Output_Plugin_Plugin_Child_Component'
        );
        return $ret;
    }

    public function processOutput($output)
    {
        // Da das Plugin nach dem Rendern ausgeführt wird, muss schon der
        // fertige Content hier reinkommen
        if ($output != 'root plugin(plugin(c1_childmaster c1_child c1_childchild))') {
            return 'not ok from plugin. output was: ' . $output;
        } else {
            $template = Vpc_Admin::getComponentFile($this, 'Component', 'tpl');
            $view = new Vps_Component_View();
            $view->child = Vps_Component_Data_Root::getInstance()
                ->getComponentById($this->_componentId)
                ->getChildComponent('-pluginChild');
            $ret = $view->render($template);
            $renderer = new Vps_Component_Renderer();
            return $renderer->render($view, $ret);
        }
    }

    public function getExecutionPoint()
    {
        return Vps_Component_Plugin_Interface_View::EXECUTE_AFTER;
    }
}
?>