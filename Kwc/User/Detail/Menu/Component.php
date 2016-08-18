<?php
class Kwc_User_Detail_Menu_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['links'] = array();

        $userRow = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($userRow && $userRow->id == $this->getData()->parent->row->id) {
            $ret['links'] = $this->_getLinks();
        }
        return $ret;
    }

    protected function _getLinks()
    {
        $ret = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_User_Edit_Component');
        return $ret;
    }
}
