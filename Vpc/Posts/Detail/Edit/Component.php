<?php
class Vpc_Posts_Detail_Edit_Component extends Vpc_Posts_Write_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Posts_Detail_Edit_Form_Component';
        $ret['generators']['child']['component']['lastPosts'] = false;
        return $ret;
    }

    // momentan nur für preview component
    public function getPostDirectoryClass()
    {
        return $this->getData()->parent->parent->componentClass;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if (!$this->getData()->parent->getComponent()->mayEditPost()) {
            throw new Vpc_AccessDeniedException();
        }
        return $ret;
    }
}
