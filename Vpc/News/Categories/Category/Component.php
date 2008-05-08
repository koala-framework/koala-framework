<?php

class Vpc_News_Categories_Category_Component extends Vpc_News_List_Abstract_Component implements Vpc_News_Interface_Component
{

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'         => 'Vps_Dao_Pool'
        ));
    }

    public function getNews($limit = 15, $start = null)
    {
        $categoryId = $this->getCurrentPageKey();
        $row = $this->getTable()->find($categoryId)->current();

        $newsRowset = $row->findManyToManyRowset(
            $this->getNewsComponent()->getTable(),
            'Vpc_News_Categories_NewsToPoolModel'
        );


        //todo: mit Zend_Db_Table::select einen join machen (ab zend 1.5))
        $tmp = array();
        $tmpSort = array();
        $i = 0;
        if (is_null($start)) $start = 0;
        foreach ($newsRowset as $newsRow) {
            //grauslig
            if ($i++ < $start) continue;
            if ($newsRow->visible) {
                $tmp[] = $newsRow;
                $tmpSort[] = $newsRow->publish_date;
            }
            if ($limit && count($tmp) >= $limit) break;
        }
        arsort($tmpSort);

        $ret = array();
        foreach ($tmpSort as $key => $tmpSor) {
            $ret[] = $tmp[$key];
        }

        return $ret;
    }

    public function getNewsCount()
    {
        //todo: langsam und unschön
        return count($this->getNews(null, null));
    }

    public function getTemplateVars()
    {
        return parent::getTemplateVars();
    }

}