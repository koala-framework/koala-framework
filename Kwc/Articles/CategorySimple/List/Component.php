<?php
class Kwc_Articles_CategorySimple_List_Component
    extends Kwc_Directories_CategorySimple_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Articles');
        $ret['categoryComponentClass'] = 'Kwc_Articles_CategorySimple_Component';
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_Articles_Directory_Component', array('limit' => 1));
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        $ret = array();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (is_instance_of($c, 'Kwc_Articles_Directory_Component')) {
                $ret[] = $c;
            }
        }
        return $ret;
    }

    public static function countArticles($componentId)
    {
        $authedUser = Kwf_Model_Abstract::getInstance('Users')->getAuthedUser();
        if (!$authedUser) {
            return null;
        }
        $role = 'external';
        if ($authedUser && $authedUser->role == 'external') {
            $role = 'intern';
        }
        $cacheId = 'articleCategoryCount'.$componentId.$role;
        $cnt = Kwf_Cache_Simple::fetch($cacheId);
        if ($cnt === false) {
            $component = Kwf_Component_Data_Root::getInstance()->getComponentById($componentId)->getComponent();
            $cnt = $component->getItemDirectory()->countChildComponents($component->getSelect());
            $ttl = strtotime(date('Y-m-d', strtotime("+1 day"))) - time();
            Kwf_Cache_Simple::add($cacheId, $cnt, $ttl);
        }
        return $cnt;
    }
}
