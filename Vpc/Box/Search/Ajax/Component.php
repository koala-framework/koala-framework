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
        foreach ($searchComponents as $c) {
            $generators = Vpc_Abstract::getSetting($c->componentClass, 'generators');
            $fields = Vpc_Abstract::getSetting(
                $generators['child']['component']['view'], 'searchQueryFields'
            );
            $select = $c->getComponent()->getSelect()->searchLike($ret['qry'], $fields);
            $select->limit(10);
            $ret['lists'][] = array(
                'list' => $c,
                'items' => $c->getChildComponents($select)
            );
        }
        return $ret;
    }
}
