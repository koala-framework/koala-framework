<?php
class Vpc_Forum_Group_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'Vpc_Forum_Thread_Component';
        $ret['generators']['detail']['maxFilenameLength'] = 50;
        $ret['generators']['detail']['maxNameLength'] = 30;
        $ret['generators']['detail']['table'] = 'Vpc_Forum_Group_Model';
        $ret['generators']['child']['component']['view'] = 'Vpc_Forum_Group_View_Component';
        $ret['generators']['newThread'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Forum_Group_NewThread_Component',
            'name' => trlVps('new thread')
        );
        $ret['tablename'] = 'Vpc_Forum_Group_Model';
        return $ret;
    }

    public function mayModerate()
    {
        static $ret;
        if (is_null($ret)) {
            $ret = false;
            $authedUser = Zend_Registry::get('userModel')->getAuthedUser();
            if ($authedUser) {
                if ($authedUser->role == 'admin') return true;
                $table = new Vpc_Forum_Group_ModeratorsModel();
                $row = $table->fetchRow(array(
                    'user_id = ?' => $authedUser->id,
                    'group_id = ?' => $this->getData()->row->id
                ));
                if ($row) $ret = true;
            }
        }
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['newThread'] = $this->getData()->getChildComponent('_newThread');
        $ret['group'] = $this->getData();
        $ret['forum'] = $this->getData()->getParentPage();
        return $ret;
    }
    
    public function getSelect()
    {
        $ret = parent::getSelect();
        $ret->order(new Zend_Db_Expr('(SELECT MAX(create_time) 
                FROM vpc_posts 
                WHERE vpc_posts.component_id=cache_child_component_id) DESC'));
        return $ret;        
    }
}
