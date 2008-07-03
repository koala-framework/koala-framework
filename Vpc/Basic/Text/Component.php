<?php
class Vpc_Basic_Text_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'         => 'Vpc_Basic_Text_Model',
            'componentName'     => 'Text',
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
            'enableTagsWhitelist'=> true,
            'childComponentClasses' => array(
                //auf false setzen um buttons zu deaktivieren
                'image'         => 'Vpc_Basic_Text_Image_Component',
                'link'          => 'Vpc_Basic_LinkTag_Component',
                'download'      => 'Vpc_Basic_DownloadTag_Component'
            ),
            'default'           => array(
                'content'       => '<p>'.Vpc_Abstract::LOREM_IPSUM.'</p>'
            )
        ));
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/Text/StylesEditor.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/Text/StylesEditorTab.js';
        $ret['assets']['dep'][] = 'VpsMailDecode';
        $ret['cssClass'] = 'webStandard vpcText';

        //hinten ' css' anhängen damit die datei als css-datei erkannt wird
        //nötig weil ein get-parameter mit der mtime dranhängt
        $ret['assets']['files'][] =
                    Vpc_Basic_Text_StylesModel::getStylesUrl().' css';

        return $ret;
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

    public function getSearchVars()
    {
        $ret = parent::getSearchVars();
        foreach ($this->_getComponentParts() as $part) {
            if ($part instanceof Vpc_Abstract) {
                foreach ($part->getSearchVars() as $k=>$i) {
                    if (!isset($ret[$k])) $ret[$k] = '';
                    $ret[$k] .= ' '.$i;
                }
            } else {
                $part = strip_tags($part);
                $ret['text'] .= ' '.$part;
            }
        }
        return $ret;
    }
}
