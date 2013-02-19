<?php
class Kwc_Favourites_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['assets']['files'][] = 'kwf/Kwc/Favourites/Component.js';
        $ret['assets']['dep'][] = 'KwfSwitchHoverFade';
        $ret['placeholder']['saveFavourite'] = trlKwfStatic('save as favourite');
        $ret['placeholder']['deleteFavourite'] = trlKwfStatic('delete favourite');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if (!$authedUser) throw new Kwf_Exception('not logged in');
        $ret['favouriteText'] = $this->_getPlaceholder('saveFavourite');
        if (in_array($this->getData()->componentId, self::getFavouriteComponentIds())) {
            $ret['favouriteText'] = $this->_getPlaceholder('deleteFavourite');
            $ret['cssClass'] .= ' isFavourite';
        }
        $ret['config'] = array(
            'componentId' => $this->getData()->componentId,
            'controllerUrl' => Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl(),
            'deleteFavourite' => $this->_getPlaceholder('deleteFavourite'),
            'saveFavourite' => $this->_getPlaceholder('saveFavourite')
        );
        return $ret;
    }

    public static function getFavouriteComponentIds()
    {
        $ret = array();
        $user = Kwf_Model_Abstract::getInstance('Users')->getAuthedUser();
        if ($user) {
            $cacheUser = 'favCIds'.$user->id;
            $ret = Kwf_Cache_Simple::fetch($cacheUser, $success);
            if (!$success) {
                $model = Kwf_Model_Abstract::getInstance('Kwc_Favourites_Model');
                $select = new Kwf_Model_Select();
                $select->whereEquals('user_id', $user->id);
                $favourites = $model->getRows($select);
                $componentIds = array();
                foreach ($favourites as $favourite) {
                    $component = Kwf_Component_Data_Root::getInstance()
                        ->getComponentById($favourite->component_id);
                    if ($component) {
                        $cacheComponent = 'favUIds'.$favourite->component_id;
                        $userIds = Kwf_Cache_Simple::fetch($cacheComponent, $success);
                        if (!$success) {
                            $model = Kwf_Model_Abstract::getInstance('Kwc_Favourites_Model');
                            $select = new Kwf_Model_Select();
                            $select->whereEquals('component_id', $favourite->component_id);
                            $users = $model->getRows($select);
                            $userIds = array();
                            foreach ($users as $user) {
                                $userIds[] = $user->user_id;
                            }
                            Kwf_Cache_Simple::add($cacheComponent, $userIds);
                        }
                        $componentIds[] = $component->componentId;
                    }
                }
                Kwf_Cache_Simple::add($cacheUser, $componentIds);
                $ret = $componentIds;
            }
        }
        return $ret;
    }
}
