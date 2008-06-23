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
        $settings = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Paragraphs'),
            'componentIcon' => new Vps_Asset('page')
        ));
        $settings['assetsAdmin']['files'][] = 'vps/Vpc/Paragraphs/Panel.js';
        $settings['childComponentClasses']['text'] = 'Vpc_Basic_Text_Component';
        $settings['childComponentClasses']['image'] = 'Vpc_Basic_Image_Component';
        return $settings;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paragraphs'] = $this->getData()
            ->getChildComponentIds(array('treecache'=>'Vpc_Paragraphs_TreeCache'));
        return $ret;
    }
}
