<?php
class Kwc_Favourites_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['assets']['files'][] = 'kwf/Kwc/Favourites/Component.js';
        $ret['assets']['dep'][] = 'KwfSwitchHoverFade';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if (!$authedUser) throw new Kwf_Exception('not logged in');
        $select = new Kwf_Model_Select();
        $select->whereEquals('component_id', $this->getData()->dbId);
        $select->whereEquals('user_id', $authedUser->id);
        $row = Kwf_Model_Abstract::getInstance('Kwc_Favourites_Model')->countRows($select);
        $ret['favouriteText'] = trlKwf('save as favourite');
        if ($row) {
            $ret['favouriteText'] = trlKwf('delete favourite');
            $ret['cssClass'] .= ' isFavourite';
        }
        $ret['config'] = array(
            'componentId' => $this->getData()->dbId,
            'controllerUrl' => Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl()
        );
        return $ret;
    }
}
