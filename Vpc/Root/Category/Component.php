<?php
class Vpc_Root_Category_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Vpc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'paragraphs' => 'Vpc_Paragraphs_Component',
                'link' => 'Vpc_Basic_LinkTag_Component',
                'firstChildPage' => 'Vpc_Basic_LinkTag_FirstChildPage_Component'
            ),
            'model' => 'Vpc_Root_Category_GeneratorModel'
        );
        $cc = Vps_Registry::get('config')->vpc->childComponents;
        if (isset($cc->Vpc_Root_Category_Component)) {
            $ret['generators']['page']['component'] = array_merge(
                $ret['generators']['page']['component'],
                $cc->Vpc_Root_Category_Component->toArray()
            );
        }
        $ret['componentName'] = trlVps('Category');
        $ret['flags']['showInPageTreeAdmin'] = true;
        $ret['flags']['menuCategory'] = true;
        return $ret;
    }
}
