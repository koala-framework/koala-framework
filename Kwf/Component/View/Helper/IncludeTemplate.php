<?php
class Kwf_Component_View_Helper_IncludeTemplate extends Zend_View_Helper_Partial
{
    /**
     * Includes Template for current Component
     *
     * @param string $name Template Name without path and .tpl (e.g. "Mail.html")
     * @param string $module
     * @param string $model
     */
    public function includeTemplate($name = null, $module = null, $model = null)
    {
        $name = Kwc_Abstract::getTemplateFile($this->view->data->componentClass, $name);
        if (!$module) { $module = $this->view; }
        return $this->partial($name, $module, $model);
    }
}
