<?php
class Vpc_Forum_Group_NewThread_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['preview'] = 'Vpc_Posts_Write_Preview_Component';
        $ret['generators']['child']['component']['form'] = 'Vpc_Forum_Group_NewThread_Form_Component';
        $ret['flags']['noIndex'] = true;
        $ret['viewCache'] = false; // Wegen if-Abfrage in Template
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['isSaved'] = $this->getData()->getChildComponent('-form')->getComponent()->isSaved();
        return $ret;
    }

    // momentan nur für preview component
    public function getPostDirectoryClass()
    {
        $group = $this->getData()->parent->componentClass;
        $thread = self::getChildComponentClass($group, 'detail');
        $posts = self::getChildComponentClass($thread, 'child', 'posts');
        return self::getChildComponentClass($posts, 'detail');
    }
}
