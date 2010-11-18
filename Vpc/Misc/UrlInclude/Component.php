<?php
class Vpc_Misc_UrlInclude_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Url include');
        $ret['ownModel'] = 'Vps_Component_FieldModel';
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
