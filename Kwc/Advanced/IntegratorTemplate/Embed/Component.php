<?php
class Kwc_Advanced_IntegratorTemplate_Embed_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['contentSender'] = 'Kwc_Advanced_IntegratorTemplate_Embed_ContentSender';
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['hasFooterIncludeCode'] = true;
        $ret['flags']['noIndex'] = true;
        $ret['generators']['metaTags'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Basic_None_Component',
            'inherit' => true
        );
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['includeCode'] = $this->getIncludeCode('content');
        return $ret;
    }

    public function getIncludeCode($position)
    {
        $position = strtoupper($position);
        return "\n<!-- APP_INCLUDE_$position - START -->\n###APP_INCLUDE_$position###\n<!-- APP_INCLUDE_$position - END -->\n";
    }
}
