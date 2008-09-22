<?php
class Vpc_Box_Search_Ajax_Component extends Vpc_Abstract_Ajax_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['qry'] = '';
        if (isset($_REQUEST['query'])) {
            $ret['qry'] = $_REQUEST['query'];
        }
        $searchComponents = $this->getData()->parent->getComponent()->getSearchComponents();
        foreach ($searchComponents as $key => $val) {
            $addList = array();
            if (is_array($val)) {
                $addList['component'] = $val['component'];
                $addList['title'] = $val['title'];
            } else if (is_object($val)) {
                $addList['component'] = $val;
                $addList['title'] = $key;
                if (is_numeric($addList['title'])) $addList['title'] = '';
            }
            $ret['lists'][] = $addList;
        }
        return $ret;
    }
}
