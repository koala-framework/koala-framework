<?php
/**
 * @package Kwc
 * @subpackage Paragraphs
 */
class Kwc_Paragraphs_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = array_merge(parent::getSettings($param), array(
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
            'teaser'       => trlKwfStatic('Teasers'),
            'special'      => trlKwfStatic('Special'),
            'model'        => trlKwfStatic('Model'),
            'contact'      => trlKwfStatic('Contact'),
            'admin'        => trlKwfStatic('Admin'),
        );
        $ret['apiContent'] = 'Kwc_Paragraphs_ApiContent';
        $ret['apiContentType'] = 'paragraphs';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['paragraphs'] = array();
        foreach($this->getData()->getChildComponents(array('generator'=>'paragraphs')) as $paragraph) {
            $cssClass = $this->_getBemClass('kwcParagraphItem');
            $row = $paragraph->getRow();
            if ($this->_getSetting('useMobileBreakpoints') && $row->device_visible) $cssClass .= ' ' . $this->_getBemClass($row->device_visible);
            $cssClass .= ' '.$this->_getBemClass(
                    'outer'.ucfirst($paragraph->row->component),
                    'outer'.ucfirst(substr(Kwf_Component_Abstract::formatRootElementClass($paragraph->componentClass, ''), 6))
                );
            $preHtml = '';
            $postHtml = '';
            foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_MaskComponent') as $plugin) {
                $mask = $plugin->getMaskCode($paragraph);
                $preHtml = $mask['begin'] . $preHtml;
                $postHtml = $postHtml . $mask['end'];
            }
            $ret['paragraphs'][] = array(
                'data' => $paragraph,
                'class' => $cssClass,
                'preHtml' => $preHtml,
                'postHtml' => $postHtml
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
