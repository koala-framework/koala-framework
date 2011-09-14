<?php
class Vpc_Basic_LinkTag_News_Data extends Vpc_Basic_LinkTag_Intern_Data
{
    protected function _getData()
    {
        $m = Vpc_Abstract::createModel($this->componentClass);
        $newsId = $m->fetchColumnByPrimaryId('news_id', $this->dbId);

        if ($newsId) {
            return Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId('news_'.$newsId, array('subroot' => $this));
        }
        return false;
    }
}
