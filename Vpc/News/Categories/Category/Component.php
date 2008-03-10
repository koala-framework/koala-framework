<?php

class Vpc_News_Categories_Category_Component extends Vpc_News_List_Abstract_Component implements Vpc_News_Interface_Component
{

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'         => 'Vps_Dao_Pool',
            'hideInNews'        => true,
            'childComponentClasses' => array()
        ));
    }

    public function getNews()
    {
        $categoryId = $this->getCurrentPageKey();
        $row = $this->getTable()->find($categoryId)->current();

        $newsRowset = $row->findManyToManyRowset(
            $this->getNewsComponent()->getTable(),
            'Vpc_News_Categories_NewsToPoolModel'
        );

        $tmp = array();
        $tmpSort = array();
        foreach ($newsRowset as $newsRow) {
            if ($newsRow->visible) {
                $tmp[] = $newsRow;
                $tmpSort[] = $newsRow->publish_date;
            }
        }
        arsort($tmpSort);

        $ret = array();
        foreach ($tmpSort as $key => $tmpSor) {
            $ret[] = $tmp[$key];
        }

        return $ret;
    }

    public function getTemplateVars()
    {
        return parent::getTemplateVars();
    }

}