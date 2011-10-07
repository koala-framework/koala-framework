<?php
class Kwc_Misc_UrlInclude_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Url include');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }

    public function getViewCacheLifetime()
    {
        return 15*60;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $row = $this->getRow();
        $ret['content'] = '';
        if ($row->url && preg_match('#^https?\\:/#', $row->url)) {
            $ret['content'] = @file_get_contents($row->url);
        }
        return $ret;

    }
}
