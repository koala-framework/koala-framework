<?php
class Kwc_Posts_Write_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['preview'] = 'Kwc_Posts_Write_Preview_Component';
        $ret['generators']['child']['component']['form'] = 'Kwc_Posts_Write_Form_Component';
        $ret['generators']['child']['component']['lastPosts'] = 'Kwc_Posts_Write_LastPosts_Component';
        $ret['placeholder']['lastPosts'] = trlKwfStatic('Last Posts');
        $ret['flags']['noIndex'] = true;
        $ret['cssClass'] = 'kwfup-webStandard';
        $ret['plugins'] = array('Kwf_Component_Plugin_Login_Component');
        $ret['viewCache'] = false; //wegen isSaved
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
        $posts = $this->getData()->parent;
        return self::getChildComponentClass($posts, 'detail');
    }

    public function getPostsModel()
    {
        return $this->getData()->parent->getComponent()->getChildModel();
    }

    public function getPostsDirectory()
    {
        return $this->getData()->parent;
    }
}
