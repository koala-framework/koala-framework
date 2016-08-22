<?php
class Kwc_Root_Category_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page'] = array(
            'class' => 'Kwc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'paragraphs' => 'Kwc_Paragraphs_Component',
                'link' => 'Kwc_Basic_LinkTag_Component',
                'firstChildPage' => 'Kwc_Basic_LinkTag_FirstChildPage_Component'
            ),
            'model' => 'Kwc_Root_Category_GeneratorModel',
            'historyModel' => 'Kwc_Root_Category_HistoryModel'
        );
        $cc = Kwf_Registry::get('config')->kwc->childComponents;
        if (isset($cc->Kwc_Root_Category_Component)) {
            $ret['generators']['page']['component'] = array_merge(
                $ret['generators']['page']['component'],
                $cc->Kwc_Root_Category_Component->toArray()
            );
        }
        $ret['componentName'] = trlKwfStatic('Category');
        $ret['flags']['menuCategory'] = true;
        return $ret;
    }

    private static function _getParentsWithHasHomeFlagComponentClasses($cls)
    {
        $ret = array();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (in_array($cls, Kwc_Abstract::getChildComponentClasses($c))) {
                if (Kwc_Abstract::getFlag($c, 'hasHome')) {
                    $ret[] = $c;
                }
                $ret = array_merge($ret, self::_getParentsWithHasHomeFlagComponentClasses($c));
            }
        }
        return $ret;
    }

    private static function _validateHasNotChildWithStaticHome($cls, array &$validated = array())
    {
        if (isset($validated[$cls])) return;

        $validated[$cls] = true;
        foreach (Kwf_Component_Generator_Abstract::getOwnInstances($cls) as $g) {
            if ($g instanceof Kwf_Component_Generator_Page_StaticHome) {
                throw new Kwf_Exception("'$cls' must not have StaticHome, either remove StaticHome, remove category generator or disable hasHome for Category/Generator");
            }
        }
        foreach (Kwc_Abstract::getChildComponentClasses($cls) as $c) {
            self::_validateHasNotChildWithStaticHome($c, $validated);
        }
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);

        $parentsWithHasHomeFlag = self::_getParentsWithHasHomeFlagComponentClasses($componentClass);
        foreach ($parentsWithHasHomeFlag as $i) {
            self::_validateHasNotChildWithStaticHome($i);
        }
    }
}
