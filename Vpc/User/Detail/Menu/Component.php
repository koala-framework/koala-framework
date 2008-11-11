<?php
class Vpc_User_Detail_Menu_Component extends Vpc_Abstract_Composite_Component
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
        $ret['links'] = array();

        $userRow = Vps_Registry::get('userModel')->getAuthedUser();
        if ($userRow && $userRow->id == $this->getData()->parent->row->id) {
            $ret['links'] = $this->_getLinks();
        }
        return $ret;
    }

    protected function _getLinks()
    {
        $ret = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_User_Edit_Component');
        return $ret;
    }
}
