<?php
/**
 * @package Vpc
 * @subpackage Paragraphs
 */
class Vpc_Paragraphs_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Paragraphs'),
            'componentIcon' => new Vps_Asset('page')
        ));
        $ret['modelname'] = 'Vpc_Paragraphs_Model';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Paragraphs/Panel.js';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoGrid';
        $ret['generators']['paragraphs'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => array('text' => 'Vpc_Basic_Text_Component',
                                 'image' => 'Vpc_Basic_Image_Component')
        );
        $ret['default'] = array();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paragraphs'] = $this->getData()
            ->getChildComponents(array('generator'=>'paragraphs'));
        return $ret;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $ret[] = array(
            'model' => $this->getModel(),
            'id' => null
        );
        return $ret;
    }
}
