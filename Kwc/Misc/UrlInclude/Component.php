<?php
class Kwc_Misc_UrlInclude_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Url include');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        return $ret;
    }

    public function getViewCacheLifetime()
    {
        return 15*60;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $row = $this->getRow();
        $ret['content'] = '';
        if ($row->url && preg_match('#^https?\\:/#', $row->url)) {
            $ret['content'] = $this->_processContent(@file_get_contents($row->url));
        }
        return $ret;
    }

    protected function _processContent($content)
    {
        return $content;
    }
}
