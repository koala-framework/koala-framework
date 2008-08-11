<?php
class Vpc_Forum_User_Directory_Component extends Vpc_User_Directory_Component  
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Forum_User_View_Component';
        $ret['generators']['detail']['component'] = 'Vpc_Forum_User_Detail_Component';
        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        return $select;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        /*
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
*/
        return $ret;
    }
    
}