<?php
class Kwc_Advanced_DyamicContent_Plugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewAfterChildRender
{
    public function processOutput($output)
    {
        if (!preg_match('#{dynamicContent ([^ ]*) ([^}]*)}#', $output, $m)) {
            throw new Kwf_Exception("didn't find {dynamicContent");
        }
        $componentClass = $m[1];
        $language = $m[2];
        $dynamicContent = call_user_func(array($componentClass, 'getDynamicContent'), $this->_componentId, $componentClass);
        $dynamicContent = Kwf_Trl::getInstance()->trlStaticExecute($dynamicContent, $language);
        return str_replace('{dynamicContent '.$componentClass.' '.$language.'}', $dynamicContent, $output);
    }
}
