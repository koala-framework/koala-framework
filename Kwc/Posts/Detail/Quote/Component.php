<?php
class Kwc_Posts_Detail_Quote_Component extends Kwc_Posts_Write_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_Posts_Detail_Quote_Form_Component';
        $ret['generators']['child']['component']['lastPosts'] = 'Kwc_Posts_Detail_Quote_LastPosts_Component';
        $ret['cssClass'] = 'kwfup-webStandard';
        return $ret;
    }

    // momentan nur für preview component
    public function getPostDirectoryClass()
    {
        return $this->getData()->parent->parent->componentClass;
    }
}