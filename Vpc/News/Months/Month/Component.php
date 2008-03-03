<?php

class Vpc_News_Months_Month_Component extends Vpc_News_List_Abstract_Component implements Vpc_News_Interface_Component
{

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'         => 'Vpc_News_Model',
            'hideInNews'        => true,
            'childComponentClasses' => array()
        ));
    }

    public function getNews()
    {
        $monthDate = $this->getCurrentPageKey().'-'.$this->getCurrentComponentKey();

        $where = array(
            "publish_date >= '$monthDate-01'",
            "publish_date <= '$monthDate-31'"
        );
        $newsRowset = $this->getTable()->fetchAll($where, 'publish_date DESC');

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