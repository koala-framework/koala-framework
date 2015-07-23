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
            'componentIcon' => 'page'
        ));
        $ret['childModel'] = 'Kwc_Paragraphs_Model';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Paragraphs/Panel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Paragraphs/DataView.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Paragraphs/AddParagraphButton.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Paragraphs/Panel.css';
        $ret['assetsAdmin']['dep'][] = 'KwfAutoGrid';
        $ret['assetsAdmin']['dep'][] = 'KwfLegacyOnReady';
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
        $ret['useMobileBreakpoints'] = Kwf_Config::getValue('kwc.mobileBreakpoints');

        $ret['categories'] = array(
            'content'      => 'content',
            'none'         => 'none',
            'layout'       => trlKwfStatic('Layout'),
            'media'        => trlKwfStatic('Pictures & Media'),
            'callToAction' => trlKwfStatic('Call to Action'),
            'childPages'   => trlKwfStatic('Child Pages'),
            'special'      => trlKwfStatic('Special'),
            'model'        => trlKwfStatic('Model'),
            'contact'      => trlKwfStatic('Contact'),
            'admin'        => trlKwfStatic('Admin'),
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paragraphs'] = array();
        foreach($this->getData()->getChildComponents(array('generator'=>'paragraphs')) as $paragraph) {
            $cssClass = 'kwcParagraphItem';
            $row = $paragraph->getRow();
            if ($this->_getSetting('useMobileBreakpoints') && $row->device_visible) $cssClass .= ' ' . $row->device_visible;
            $cssClass .= ' outer'.ucfirst(Kwf_Component_Abstract::formatCssClass($paragraph->componentClass, ''));
            $ret['paragraphs'][] = array(
                'data' => $paragraph,
                'class' => $cssClass
            );
        }
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
}
