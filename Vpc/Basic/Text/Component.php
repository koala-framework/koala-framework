<?php
class Vpc_Basic_Text_Component extends Vpc_Basic_Html_Component
{
    private $_componentParts;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'         => 'Vpc_Basic_Text_Model',
            'componentName'     => 'Text',
            'componentIcon'     => new Vps_Asset('paragraph_page'),
            'fieldLabel'        => 'Rich Text Editor',
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

    protected function _getComponentParts()
    {
        if (!isset($this->_componentParts)) {
            foreach ($this->_getRow()->getContentParts() as $part) {
                if (is_array($part)) {
                    $class = false;
                    if ($part['type'] == 'image') {
                        $class = $this->_getClassFromSetting('image', 'Vpc_Basic_Image_Component');
                        $part['nr'] = 'i'.$part['nr'];
                    } else if ($part['type'] == 'link') {
                        $class = $this->_getClassFromSetting('link', 'Vpc_Basic_LinkTag_Component');
                        $part['nr'] = 'l'.$part['nr'];
                    } else if ($part['type'] == 'download') {
                        $class = $this->_getClassFromSetting('download', 'Vpc_Basic_DownloadTag_Component');
                        $part['nr'] = 'd'.$part['nr'];
                    }
                    if ($class) {
                        $component = $this->createComponent($class, $part['nr']);
                        $this->_componentParts[] = $component;
                    }
                } else {
                    $this->_componentParts[] = $part;
                }
            }
        }
        return $this->_componentParts;
    }

    public function getChildComponents()
    {
        $ret = array();
        foreach ($this->_getComponentParts() as $part) {
            if ($part instanceof Vpc_Abstract) {
                $ret[] = $part;
            }
        }
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['contentParts'] = array();
        foreach ($this->_getComponentParts() as $part) {
            if ($part instanceof Vpc_Abstract) {
                $ret['contentParts'][] = $part->getTemplateVars();
            } else {
                $ret['contentParts'][] = $part;
            }
        }
        return $ret;
    }
}
