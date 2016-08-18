<?php
class Kwc_Box_TitleEditable_Trl_Component extends Kwc_Chained_Trl_MasterAsChild_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['flags']['hasHeaderIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }
}
