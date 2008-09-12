<?php
class Vpc_Posts_Detail_Actions_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['edit'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Detail_Edit_Component',
            'name' => trlVps('edit')
        );
        $ret['generators']['report'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Detail_Report_Component',
            'name' => trlVps('report')
        );
        $ret['generators']['delete'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Detail_Delete_Component',
            'name' => trlVps('delete')
        );
        $ret['generators']['quote'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Detail_Quote_Component',
            'name' => trlVps('quote')
        );
        $ret['viewCache'] = false;
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $data = $this->getData();
        
        $ret['edit'] = null;
        $ret['delete'] = null;
        if ($this->mayEditPost()) {
            $ret['edit'] = $data->getChildComponent('_edit');
        }
        if ($this->mayDeletePost()) {
            $ret['delete'] = $data->getChildComponent('_delete');
        }
        $ret['report'] = $data->getChildComponent('_report');
        $ret['quote'] = $data->getChildComponent('_quote');
        return $ret;
    }

    public function mayEditPost()
    {
        $authedUser = Zend_Registry::get('userModel')->getAuthedUser();
        if ($authedUser->role == 'admin') return true;
        if (!$authedUser) return false;
        return $authedUser->id == $this->getData()->parent->row->user_id;
    }

    public function mayDeletePost()
    {
        return $this->mayEditPost();
    }
}
