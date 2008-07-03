<?php
/**
 * @package Vpc
 * @subpackage Paragraphs
 */
class Vpc_Paragraphs_Component extends Vpc_Abstract
{
    private $_rows;

    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Paragraphs'),
            'componentIcon' => new Vps_Asset('page')
        ));
        $ret['tablename'] = 'Vpc_Paragraphs_Model';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Paragraphs/Panel.js';
        $ret['childComponentClasses']['text'] = 'Vpc_Basic_Text_Component';
        $ret['childComponentClasses']['image'] = 'Vpc_Basic_Image_Component';
        $ret['default'] = array();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paragraphs'] = $this->getData()
            ->getChildComponents(array('treecache'=>'Vpc_Paragraphs_TreeCache'));
        return $ret;
    }
}
