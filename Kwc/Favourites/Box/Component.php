<?php
class Kwc_Favourites_Box_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'kwf/Kwc/Favourites/Box/Component.js';
        $ret['favouritesPageComponentClass'] = 'Kwc_Favourites_Page_Component';
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
            $ret['linkText'] = str_replace('{0}', "<span class=\"cnt\">$count</span>",
                            $this->_getPlaceholder('linkText'));
        }
        $class = Kwc_Abstract::getSetting($this->getData()->getComponentClass(),
                         'favouritesPageComponentClass');
        if (!$class) {
            throw new Kwf_Exception('Set favouritesComponent (favourites-page) in getSettings');
        }
        $ret['favourite'] = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass($class);
        return $ret;
    }
}
