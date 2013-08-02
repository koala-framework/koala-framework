<?php
class Kwc_Favourites_Page_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Favourites');
        $ret['viewCache'] = false;
        $ret['assets']['files'][] = 'kwf/Kwc/Favourites/Page/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['flags']['skipFulltextRecursive'] = true;
        $ret['flags']['hasComponentLinkModifiers'] = true;
        $ret['favouritesModel'] = 'Kwc_Favourites_Model';
        return $ret;
    }

    public function getComponentLinkModifiers()
    {
        return array(
            array(
                'type' => 'callback',
                'callback' => array(get_class($this), 'modifyComponentLink'),
                'favouritesModel' => Kwc_Abstract::
                            getSetting($this->getData()->componentClass, 'favouritesModel'),
            )
        );
    }

    public static function modifyComponentLink($ret, $componentId, $settings)
    {
        return $ret .
            '<div class="kwcFavouritesPageComponentFavouritesCount">' .
            count(Kwc_Favourites_Component::getFavouriteComponentIds($settings['favouritesModel'])) .
            '</div>';
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $userId = Kwf_Registry::get('userModel')->getAuthedUserId();
        if($userId) {
            $lessonsFavouritesModel = Kwf_Model_Abstract::getInstance(Kwc_Abstract::
                            getSetting($this->getData()->componentClass, 'favouritesModel'));
            $selectFavourites = $lessonsFavouritesModel->select()
                                        ->whereEquals('user_id', $userId);
            $favourites = $lessonsFavouritesModel->getRows($selectFavourites);
            $components = array();
            foreach ($favourites as $favourite) {
                $component = Kwf_Component_Data_Root::getInstance()->getComponentById($favourite->component_id);
                if ($component) {
                    $components[] = $component;
                }
            }
            $ret['favourites'] = $components;
        }
        return $ret;
    }
}
