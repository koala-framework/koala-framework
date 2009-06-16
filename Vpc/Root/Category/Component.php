<?php
class Vpc_Root_Category_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Vpc_Root_Category_PageGenerator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'paragraphs' => 'Vpc_Paragraphs_Component',
                'link' => 'Vpc_Basic_LinkTag_Component',
                'firstChildPage' => 'Vpc_Basic_LinkTag_FirstChildPage_Component'
            ),
            'table' => 'Vps_Dao_Pages'
        );
        $ret['componentName'] = trlVps('Category');
        $ret['flags']['showInPageTreeAdmin'] = true;
        return $ret;
    }
}
