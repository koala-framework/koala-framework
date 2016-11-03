<?php
class Kwc_Basic_LinkTag_News_Data extends Kwc_Basic_LinkTag_Intern_Data
{
    protected function _getData($select = array())
    {
        $m = Kwc_Abstract::createModel($this->componentClass);
        $newsId = $m->fetchColumnByPrimaryId('news_id', $this->dbId);

        if ($newsId) {
            return Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId('news_'.$newsId, array('subroot' => $this));
        }
        return false;
    }
}
