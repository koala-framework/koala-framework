<?php
class Vps_View_Helper_Dynamic
{
    private $_view;

    public function setView($view)
    {
        $this->_view = $view;
    }

    public function dynamic(Vps_Component_Data $component, $class)
    {
        $args = array_slice(func_get_args(), 2);
        return Vps_Component_Output_Dynamic::getHelperOutput($component, $class, $args, $this->_view->info);
    }
}
