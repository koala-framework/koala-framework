<?php
class Kwc_Box_Favourites_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'web/components/Box/Favourites/Component.js';
        $ret['favouritesPageComponent'] = 'KWC_FAVOURITES_PAGE'; //TODO set to kwc-favourites-page
        $ret['viewCache'] = false;
        $ret['placeholder']['linkText'] = trlStatic('FAVORITEN ({0})');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $user = Kwf_Model_Abstract::getInstance('Users')->getAuthedUser();
        if ($user) {
            $model = Kwf_Model_Abstract::getInstance('Kwc_Favourites_Model');
            $select = new Kwf_Model_Select();
            $select->whereEquals('user_id', $user->id);
            $count = $model->countRows($select);
            $ret['linkText'] = str_replace('{0}', "<span class=\"cnt\">$count</span>", $this->_getPlaceholder('linkText'));
        }
        $ret['favourite'] = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass(Kwc_Abstract::getSetting($this->getData()->getComponentClass(), 'favouritesPageComponent'));
        return $ret;
    }
}
