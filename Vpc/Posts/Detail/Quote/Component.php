<?php
class Vpc_Posts_Detail_Quote_Component extends Vpc_Posts_Write_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Posts_Detail_Quote_Form_Component';
        $ret['generators']['child']['component']['lastPosts'] = 'Vpc_Posts_Detail_Quote_LastPosts_Component';
        return $ret;
    }

    // momentan nur für preview component
    public function getPostDirectoryClass()
    {
        return $this->getData()->parent->parent->componentClass;
    }
}
