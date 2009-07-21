<?php
class Vpc_Basic_Text_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'modelname'         => 'Vpc_Basic_Text_Model',
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
            'emailStyles'       => array()
        ));
        $ret['generators']['child'] = array(
            'class' => 'Vpc_Basic_Text_Generator',
            'component' => array(
                //auf false setzen um buttons zu deaktivieren
                'image'         => 'Vpc_Basic_Text_Image_Component',
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

        $ret['assets']['dep'][] = 'VpsMailDecode';

        $ret['cssClass'] = 'webStandard vpcText';

        $ret['assets']['files'][] = new Vps_Assets_Dynamic('css',
                            array('Vpc_Basic_Text_StylesModel', 'getStylesUrl'));
        $ret['flags']['searchContent'] = true;
        return $ret;
    }

    public function getModel()
    {
        return self::getTextModel(get_class($this));
    }

    public static function getTextModel($componentClass)
    {
        static $models = array();
        if (!isset($models[$componentClass])) {
            $m = Vpc_Abstract::getSetting($componentClass, 'modelname');
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
        $content = trim(strip_tags($this->_getRow()->content));
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

    public function getMailVars($user)
    {
        $ret = parent::getMailVars($user);
        $ret['styles'] = $this->_getSetting('emailStyles');
        return $ret;
    }
}
