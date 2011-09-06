<?php
class Vpc_Basic_Text_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'ownModel'          => 'Vpc_Basic_Text_Model',
            'componentName'     => trlVps('Text'),
            'componentIcon'     => new Vps_Asset('paragraph_page'),
            'width'             => 550,
            'height'            => 400,
            'enableAlignments'  => false,
            'enableColors'      => false,
            'enableFont'        => false,
            'enableFontSize'    => false,
            'enableFormat'      => true,
            'enableLists'       => true,
            'enableSourceEdit'  => true,
            'enableBlock'       => false,
            'enableUndoRedo'    => true,
            'enableLinks'       => false, //nur wenn link komponente nicht vorhanden
            'enableInsertChar'  => true,
            'enablePastePlain'  => true,
            'enableTidy'        => true,
            'stylesIdPattern'   => false, //zB: '^company_[0-9]+',
            'enableStyles'      => true,
            'enableStylesEditor'=> true,
            'enableTagsWhitelist'=> true,
            'defaultText'       => Vpc_Abstract::LOREM_IPSUM,

            //veraltert NICHT VERWENDEN!! (in der Vpc_Mail komponente ist ein besserer ersatz)
            'emailStyles'       => array()
        ));

        $ret['stylesModel'] = 'Vpc_Basic_Text_StylesModel';

        $ret['generators']['child'] = array(
            'class' => 'Vpc_Basic_Text_Generator',
            'component' => array(
                //auf false setzen um buttons zu deaktivieren
                'image'         => false,
                'link'          => 'Vpc_Basic_LinkTag_Component',
                'download'      => 'Vpc_Basic_DownloadTag_Component'
            ),
            'model' => 'Vpc_Basic_Text_ChildComponentsModel'
        );

        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/Text/StylesEditor.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/Text/StylesEditorTab.js';
        $ret['assetsAdmin']['dep'][] = 'ExtHtmlEdit';
        $ret['assetsAdmin']['dep'][] = 'ExtWindow';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoForm';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoGrid';
        $ret['assetsAdmin']['dep'][] = 'ExtSimpleStore';
        $ret['assetsAdmin']['dep'][] = 'VpsColorField';

        $ret['assets']['dep'][] = 'VpsMailDecode';

        $ret['cssClass'] = 'webStandard vpcText';

        $ret['assets']['files']['styles'] = 'dynamic/Vpc_Basic_Text_StylesAsset:Vpc_Basic_Text_StylesModel';
        $ret['flags']['searchContent'] = true;
        $ret['flags']['hasFulltext'] = true;
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_Form';
        return $ret;
    }

    public static function validateSettings($settings, $component)
    {
        //nicht parent aufrufen weil da würde das model erstellt werden ohne componentClass zu übergeben
    }

    public function getModel()
    {
        return self::getTextModel(get_class($this));
    }

    public static function getTextModel($componentClass)
    {
        static $models = array();
        if (!isset($models[$componentClass])) {
            $m = Vpc_Abstract::getSetting($componentClass, 'ownModel');
            $models[$componentClass] = new $m(array('componentClass' => $componentClass));
        }
        return $models[$componentClass];
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['contentParts'] = array();
        $childs = $this->getData()->getChildComponents();
        foreach ($this->_getRow()->getContentParts() as $part) {
            if (is_array($part)) {
                if ($part['type'] == 'image') {
                    $part['nr'] = 'i'.$part['nr'];
                } else if ($part['type'] == 'link') {
                    $part['nr'] = 'l'.$part['nr'];
                } else if ($part['type'] == 'download') {
                    $part['nr'] = 'd'.$part['nr'];
                } else {
                    continue;
                }
                foreach ($childs as $row) {
                    if ($row->dbId == $this->getData()->dbId.'-'.$part['nr']) {
                        $ret['contentParts'][] = array(
                            'type' => $part['type'],
                            'component'=>$row
                        );
                        break;
                    }
                }
            } else {
                $ret['contentParts'][] = $part;
            }
        }
        return $ret;
    }

    public function hasContent()
    {
        $content = $this->_getRow()->content;
        $content = strip_tags($content);
        // replace every non-word-character, to see if there is real content,
        // or just a bunch of white-spaces
        $content = preg_replace('/\W*/', '', $content);
        if (!empty($content)) return true;
        return false;
    }

    public function getSearchContent()
    {
        $ret = '';
        foreach ($this->_getRow()->getContentParts() as $part) {
            if (is_string($part)) {
                $part = strip_tags($part);
                $ret .= ' '.$part;
            }
        }
        return $ret;
    }

    public function modifyFulltextDocument(Zend_Search_Lucene_Document $doc)
    {
        $fieldName = $this->getData()->componentId;

        $html = '';
        foreach ($this->_getRow()->getContentParts() as $part) {
            if (is_string($part)) $html .= ' '.$part;
        }

        $doc->getField('content')->value .= ' '.$this->_stripTags($html);

        $tags = array(
            'h1' => 5,
            'h2' => 3,
            'h3' => 2,
            'h4' => 1.5,
            'h5' => 1.3,
            'h6' => 1.2,
            'strong' => 2,
        );
        foreach ($tags as $tag=>$boost) {
            if (preg_match_all("#<$tag.*?>(.*?)</$tag>#", $html, $m)) {
                $html = preg_replace("#<$tag.*?>.*?</$tag>#", '', $html);
                foreach ($m[1] as $text) {
                    $text = $this->_stripTags($text);
                    if ($text) {
                        $field = Zend_Search_Lucene_Field::UnStored($fieldName.$tag, $text, 'utf-8');
                        $field->boost = $boost;
                        $doc->addField($field);
                    }
                }
            }
        }

        $html = $this->_stripTags($html);
        if ($html) {
            $field = Zend_Search_Lucene_Field::UnStored($fieldName.'p', $html, 'utf-8');
            $doc->addField($field);
        }

        return $doc;
    }

    private static function _stripTags($html)
    {
        $html = preg_replace("#<[^>]*>#", ' ', $html);
        $html = str_replace("\r", ' ', $html);
        $html = str_replace("\n", ' ', $html);
        $html = preg_replace('#  +#', ' ', $html);
        $html = trim($html);
        return $html;
    }

    public function getMailVars($user)
    {
        $ret = parent::getMailVars($user);
        $ret['styles'] = $this->_getSetting('emailStyles');
        return $ret;
    }
}
