<?php

class Vpc_News_Categories_Category_Component extends Vpc_News_List_Abstract_Component implements Vpc_News_Interface
{

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'         => 'Vpc_News_Categories_Model',
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
            'Vpc_News_Categories_NewsToCategoriesModel'
        );

        $ret = array();
        foreach ($newsRowset as $newsRow) {
            if ($newsRow->visible) {
                $ret[] = $newsRow;
            }
        }

        return $ret;
    }

    public function getTemplateVars()
    {
        return parent::getTemplateVars();
    }

}