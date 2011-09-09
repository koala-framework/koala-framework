<?php
class Vpc_Basic_LinkTag_News_Data extends Vpc_Basic_LinkTag_Intern_Data
{
    protected function _getData()
    {
        $m = Vpc_Abstract::createModel($this->componentClass);
        if ($m->getProxyModel() instanceof Vps_Model_Db) {
            //performance, avoid model overhead
            $sql = "SELECT news_id FROM ".$m->getProxyModel()->getTableName()." WHERE component_id=?";
            $newsId = Vps_Registry::get('db')->query($sql, $this->dbId)->fetchColumn();
        } else {
            $row = $m->getRow($this->dbId);
            $newsId = $row ? $row->news_id : false;
        }

        if ($newsId) {
            return Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId('news_'.$newsId, array('subroot' => $this));
        }
        return false;
    }
}
