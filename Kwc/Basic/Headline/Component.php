<?php
class Kwc_Basic_Headline_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = array_merge(parent::getSettings($param), array(
            'componentName' => trlKwfStatic('Headline'),
            'componentIcon' => 'text_padding_top',
            'ownModel'      => 'Kwc_Basic_Headline_Model',
            'rootElementClass'      => 'kwfUp-webStandard',
            'extConfig'     => 'Kwf_Component_Abstract_ExtConfig_Form'
        ));
        $ret['componentCategory'] = 'content';
        $ret['componentPriority'] = 60;
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = array('headline1', 'headline2', 'headline_type');
        $ret['headlines'] = array(
            'h1' => array(
                'text' => trlKwfStatic('Headline {0}', 1),
                'tag' => 'h1',
                'class' => null
            ),
            'h2' => array(
                'text' => trlKwfStatic('Headline {0}', 2),
                'tag' => 'h2',
                'class' => null
            ),
            'h3' => array(
                'text' => trlKwfStatic('Headline {0}', 3),
                'tag' => 'h3',
                'class' => null
            ),
            'h4' => array(
                'text' => trlKwfStatic('Headline {0}', 4),
                'tag' => 'h4',
                'class' => null
            ),
            'h5' => array(
                'text' => trlKwfStatic('Headline {0}', 5),
                'tag' => 'h5',
                'class' => null
            ),
            'h6' => array(
                'text' => trlKwfStatic('Headline {0}', 6),
                'tag' => 'h6',
                'class' => null
            )
        );
        $ret['flags']['hasAnchors'] = true;
        $ret['apiContent'] = 'Kwc_Basic_Headline_ApiContent';
        $ret['apiContentType'] = 'headline';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['headline1'] = $this->_getRow()->headline1;
        $ret['headline2'] = $this->_getRow()->headline2;

        // decode before encode to support manually encoded strings
        $ret['headline1'] = str_replace('[-]', '&shy;', htmlspecialchars(htmlspecialchars_decode($ret['headline1'])));
        $ret['headline2'] = str_replace('[-]', '&shy;', htmlspecialchars(htmlspecialchars_decode($ret['headline2'])));

        $headlines = $this->_getSetting('headlines');
        if ($this->getRow()->headline_type && isset($headlines[$this->getRow()->headline_type])) {
            $ret['headlineType'] = $headlines[$this->getRow()->headline_type];
        } else {
            $ret['headlineType'] = reset($headlines);
        }
        $ret['showAnchor'] = Kwc_Abstract::getFlag($this->getData()->componentClass, 'hasAnchors');
        return $ret;
    }

    public function hasContent()
    {
        if (trim($this->_getRow()->headline1) != "") {
            return true;
        }
        return false;
    }

    public function getAnchors()
    {
        return array($this->getData()->componentId => $this->getRow()->headline1);
    }
}
