<?php
/**
 * @package Kwc
 * @subpackage Paragraphs
 */
class Kwc_Paragraphs_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlKwfStatic('Paragraphs'),
            'componentIcon' => new Kwf_Asset('page')
        ));
        $ret['childModel'] = 'Kwc_Paragraphs_Model';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Paragraphs/Panel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Paragraphs/DataView.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Paragraphs/AddParagraphButton.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Paragraphs/Panel.css';
        $ret['assetsAdmin']['dep'][] = 'KwfAutoGrid';
        $ret['generators']['paragraphs'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => array(
                'textImage' => 'Kwc_TextImage_Component',
            )
        );
        $cc = Kwf_Registry::get('config')->kwc->childComponents;
        if (isset($cc->Kwc_Paragraphs_Component)) {
            $ret['generators']['paragraphs']['component'] = array_merge(
                $ret['generators']['paragraphs']['component'],
                $cc->Kwc_Paragraphs_Component->toArray()
            );
        }
        $ret['showCopyPaste'] = true;
        $ret['extConfig'] = 'Kwc_Paragraphs_ExtConfig';
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

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Kwf_Component_Cache_Meta_Static_ChildModel();
        return $ret;
    }
}
