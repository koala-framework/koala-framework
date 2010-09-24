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
        $ret['childModel'] = 'Vpc_Paragraphs_Model';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Paragraphs/Panel.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Paragraphs/DataView.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Paragraphs/AddParagraphButton.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Paragraphs/Panel.css';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoGrid';
        $ret['generators']['paragraphs'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => array(
                'textImage' => 'Vpc_TextImage_Component',
            )
        );
        $cc = Vps_Registry::get('config')->vpc->childComponents;
        if (isset($cc->Vpc_Paragraphs_Component)) {
            $ret['generators']['paragraphs']['component'] = array_merge(
                $ret['generators']['paragraphs']['component'],
                $cc->Vpc_Paragraphs_Component->toArray()
            );
        }
        $ret['showCopyPaste'] = true;
        $ret['previewWidth'] = 600;
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

    public function hasContent()
    {
        $childComponents = $this->getData()->getChildComponents(array('generator' => 'paragraphs'));
        foreach ($childComponents as $c) {
            if ($c->hasContent()) return true;
        }
        return false;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $ret[] = array(
            'model' => $this->getChildModel(),
            'id' => $this->getData()->dbId,
            'field' => 'component_id'
        );
        return $ret;
    }
}
