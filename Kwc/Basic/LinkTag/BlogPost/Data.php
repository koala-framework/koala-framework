<?php
class Kwc_Basic_LinkTag_BlogPost_Data extends Kwc_Basic_LinkTag_Intern_Data
{
    protected function _getData()
    {
        $m = Kwc_Abstract::createModel($this->componentClass);
        $blogPostId = $m->fetchColumnByPrimaryId('blog_post_id', $this->dbId);

        if ($blogPostId) {
            return Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId('blog_'.$blogPostId, array('subroot' => $this));
        }
        return false;
    }
}
