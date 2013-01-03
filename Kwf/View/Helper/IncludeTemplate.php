<?php
class Kwf_View_Helper_IncludeTemplate extends Zend_View_Helper_Partial
{
    public function includeTemplate($name = null, $module = null, $model = null)
    {
        return $this->partial($name, $module, $model);
    }
}
