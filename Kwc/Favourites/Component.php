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

    /**
     * returns a list of all visible favourite componentIds
     */
    public static function getFavouriteComponentIds()
    {
        $ret = array();
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($user) {
            $cacheIdUser = 'favCIds'.$user->id;
            $ret = Kwf_Cache_Simple::fetch($cacheIdUser, $success);
            if (!$success) {
                // get all favourites related to user
                $select = new Kwf_Model_Select();
                $select->whereEquals('user_id', $user->id);
                $favouritesModel = Kwf_Model_Abstract::getInstance('Kwc_Favourites_Model');
                $favourites = $favouritesModel->getRows($select);
                $componentIds = array();
                foreach ($favourites as $favourite) {
                    $component = Kwf_Component_Data_Root::getInstance()
                        ->getComponentById($favourite->component_id);
                    // check if component is visible and existent
                    if ($component) {
                        // if component is visible create list of users related to component
                        $componentIds[] = $component->componentId;
                    }
                }
                // cache relation of visible components to user
                Kwf_Cache_Simple::add($cacheIdUser, $componentIds);
                $ret = $componentIds;
            }
        }
        return $ret;
    }
}
