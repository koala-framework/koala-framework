<?php
class Kwc_Root_Category_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Kwc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'paragraphs' => 'Kwc_Paragraphs_Component',
                'link' => 'Kwc_Basic_LinkTag_Component',
                'firstChildPage' => 'Kwc_Basic_LinkTag_FirstChildPage_Component'
            ),
            'model' => 'Kwc_Root_Category_GeneratorModel'
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
}
