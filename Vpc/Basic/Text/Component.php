<?php
class Vpc_Basic_Text_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'         => 'Vpc_Basic_Text_Model',
            'componentName'     => 'Text',
            'componentIcon'     => new Vps_Asset('paragraph_page'),
            'width'             => 550,
            'height'            => 400,
            'enableAlignments'  => true,
            'enableColors'      => false,
            'enableFont'        => false,
            'enableFontSize'    => false,
            'enableFormat'      => true,
            'enableLists'       => true,
            'enableSourceEdit'  => true,
            'enableBlock'       => true,
            'enableUndoRedo'    => true,
            'enableLinks'       => false, //nur wenn link komponente nicht vorhanden
            'enableInsertChar'  => true,
            'enablePastePlain'  => true,
            'enableTidy'        => true,
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
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['contentParts'] = array();
        $childs = $this->getTreeCacheRow()->findChildComponents();
        foreach ($this->_getRow()->getContentParts() as $part) {
            if (is_array($part)) {
                if ($part['type'] == 'image') {
                    $part['nr'] = 'i'.$part['nr'];
                } else if ($part['type'] == 'link') {
                    $part['nr'] = 'l'.$part['nr'];
                } else if ($part['type'] == 'download') {
                    $part['nr'] = 'd'.$part['nr'];
                }
                foreach ($childs as $row) {
                    if ($row->db_id == $this->getTreeCacheRow()->db_id.'-'.$part['nr']) {
                        $ret['contentParts'][] = $row->getComponent()->getTemplateVars();
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
