<?php
class Kwc_Favourites_Component extends Kwc_Abstract
{
    const LINK_TYPE_GRAPHICAL = 'graphical';
    const LINK_TYPE_TEXTUAL = 'textual';

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['assets']['files'][] = 'kwf/Kwc/Favourites/Component.js';
        $ret['assets']['dep'][] = 'KwfSwitchHoverFade';
        $ret['assets']['dep'][] = 'ExtUtilJson';
        $ret['assets']['dep'][] = 'ExtConnection';
        $ret['assets']['dep'][] = 'KwfOnReady';

        $ret['placeholder']['saveFavourite'] = trlKwfStatic('save as favourite');
        $ret['placeholder']['deleteFavourite'] = trlKwfStatic('delete favourite');
        $ret['favouritesModel'] = 'Kwc_Favourites_Model';
        $ret['linkType'] = self::LINK_TYPE_GRAPHICAL;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['favouriteText'] = $this->_getPlaceholder('saveFavourite');
        $favouritesModel = Kwc_Abstract::getSetting($this->getData()->componentClass, 'favouritesModel');
        if (in_array($this->getData()->componentId, self::getFavouriteComponentIds($favouritesModel))) {
            $ret['favouriteText'] = $this->_getPlaceholder('deleteFavourite');
            $ret['cssClass'] .= ' isFavourite';
        }
        $ret['config'] = array(
            'componentId' => $this->getData()->componentId,
            'controllerUrl' => Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl(),
            'deleteFavourite' => $this->_getPlaceholder('deleteFavourite'),
            'saveFavourite' => $this->_getPlaceholder('saveFavourite')
        );
        $ret['linkType'] = $this->_getSetting('linkType');
        if ($ret['linkType'] == self::LINK_TYPE_GRAPHICAL) {
            $ret['cssClass'] .= ' kwfSwitchHoverFade';
        }
        return $ret;
    }

    /**
     * returns a list of all visible favourite componentIds
     */
    public static function getFavouriteComponentIds($favouritesModel)
    {
        $ret = array();
        $userId = Kwf_Registry::get('userModel')->getAuthedUserId();
        if ($userId) {
            $cacheIdUser = 'favCIds'.$userId;
            $ret = Kwf_Cache_Simple::fetch($cacheIdUser, $success);
            if (!$success) {
                // get all favourites related to user
                $select = new Kwf_Model_Select();
                $select->whereEquals('user_id', $userId);
                $favouritesModel = Kwf_Model_Abstract::getInstance($favouritesModel);
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
