<?php
class Kwc_Basic_LinkTag_FirstChildPage_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlKwfStatic('Link.First Child Page'),
            'componentIcon' => 'page_go',
            'dataClass' => 'Kwc_Basic_LinkTag_FirstChildPage_Data'
        ));
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        $ret['flags']['skipFulltext'] = true;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ex = new Kwf_Exception(get_class($this) . ' must only be used as a page type.');
        $ex->logOrThrow();
        return parent::getTemplateVars($renderer);
    }
}
