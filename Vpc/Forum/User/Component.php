<?php
class Vpc_Forum_User_Component extends Vpc_Abstract
{
    private $_paging;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'childComponentClasses' => array(
                'edit' => 'Vpc_Forum_User_Edit_Component',
                'view' => 'Vpc_Forum_User_View_Component',
                'paging' => 'Vpc_Forum_User_Paging_Component'
            )
        ));
    }

    public function getForumComponent()
    {
        return $this->getParentComponent();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $limit = $this->_getPagingComponent()->getLimit();
        $pf = $this->getPageFactory()->getAdditionalFactory('view');

        $users = $pf->getTable()
                ->fetchAll(null, "IF(nickname IS NULL, 'zzzz', nickname) ASC", $limit['limit'], $limit['start']);
        foreach ($users as $user) {
            $u = Zend_Registry::get('userModel')->find($user->id)->current();
            if (!$u) continue; //todo: sollte eigentlich nicht sein - ist aber
            $ret['users'][] = array(
                'url' => $pf->getChildPageByRow($user)->getUrl(),
                'name' => ($user->nickname ? $user->nickname : $u->firstname.' '.substr($u->lastname, 0, 1).'.'),
                'created' => $u->created,
                'rating' => $user->getRating()
            );
        }
        $ret['paging'] = $this->_getPagingComponent()->getTemplateVars();
        return $ret;
    }

    protected function _getPagingComponent()
    {
        if (!isset($this->_paging)) {
            $classes = $this->_getSetting('childComponentClasses');
            $this->_paging = $this->createComponent($classes['paging'], 'paging');

            $select = Zend_Registry::get('db')->select();
            $select->from('vpc_forum_users', array('count' => 'COUNT(*)'));
            $r = $select->query()->fetchAll();

            $this->_paging->setEntries($r[0]['count']);
        }
        return $this->_paging;
    }
}