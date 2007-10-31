<?php
class Vpc_Basic_Text_Component extends Vpc_Basic_Html_Component
{
   protected $_settings = array(
        'content' => 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.',
        'fieldLabel' => 'Rich Text Editor',
        'width' => 550,
        'height' => 400,
        'enableAlignments' => true,
        'enableColors' => false,
        'enableFont' => false,
        'enableFontSize' => false,
        'enableFormat' => true,
        'enableLinks' => true,
        'enableLists' => true,
        'enableSourceEdit' => true,
        'imageClass'        => 'Vpc_Basic_Image_Component',
        'imageSettings'     => array('allowBlank' => false,
                                     'size'       => array())
    );

    protected $_tablename = 'Vpc_Basic_Text_Model';
    const NAME = 'Standard.Text';
    protected $_components = array();

    public function getChildComponents()
    {
        $ret = array();
        $content = $this->getSetting('content').$this->getSetting('content_edit');
        $this->_parseContentParts($content); //um components zu laden
        return $this->_components;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['contentParts'] = array();
        foreach ($this->_parseContentParts($this->getSetting('content')) as $part) {
            if (is_string($part)) {
                $ret['contentParts'][] = $part;
            } else {
                $ret['contentParts'][] = $part->getTemplateVars();
            }
        }
        $ret['template'] = 'Basic/Text.html';
        return $ret;
    }

    protected function _parseContentParts($content)
    {
        $ret = array();
        while(preg_match('#^(.*)<img.+(src|componentkey)="([^"]*)"[^>]*>(.*)$#Us', $content, $m)) {
            $ret[] = $m[1];

            $id = false;
            if ($m[2] == 'src') {
                $id = $this->_getImageIdBySrc($m[3]);
            } elseif ($m[2] == 'componentkey') {
                $id = $m[3];
            }
            if ($id && isset($this->_components[$id])) {
                $ret[] = $this->_components[$id];
            } elseif ($id) {
                $component = $this->_createImageComponent($id);
                $ret[] = $component;
            }
            $content = $m[4];
        }

        if(!$m) $ret[] = $content;
        return $ret;
    }

    private function _createImageComponent($id)
    {
        $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Component');
        $component = $this->createComponent($imageClass, $id, $this->getSetting('imageSettings'));
        $this->_components[$id] = $component;
        return $component;
    }

    private function _getImageIdBySrc($src)
    {
        //media/:uploadId/:componentId/:checksum/:filename
        if (preg_match('#/media/([0-9]+)/([^/]+)/#', $src, $m)) {
            return $this->_getChildComponentNrById($m[2]);
        }
        return false;
    }

    private function _getChildComponentNrById($id)
    {
        if (substr($id, 0, strlen($this->getId())) == $this->getId()) {
            $ret = substr($id, strlen($this->getId())+1);
            return (int)$ret;
        }
        return false;
    }

    public function getImageBySrc($src)
    {
        $id = $this->_getImageIdBySrc($src);
        if (!$id) return null;
        return $this->_createImageComponent($id);
    }

    protected function _parseImageComponentKey($html)
    {
        $parts = $this->_parseContentParts($html);
        $ret = array();
        foreach ($parts as $part) {
            if ($part instanceof Vpc_Basic_Image_Component) {
                $ret[] = $this->_getChildComponentNrById($part->getId());
            }
        }
        return $ret;
    }

    public function addImage($html)
    {
        $newIds = $this->_parseImageComponentKey($html);
        $contentIds = $this->_parseImageComponentKey($this->getSetting('content'));
        $contentEditIds = $this->_parseImageComponentKey($this->getSetting('content_edit'));
        foreach ($contentEditIds as $id) {
            if (!in_array($id, $contentIds) && !in_array($id, $newIds)) {
                $component = $this->_createImageComponent($id);
                Vpc_Admin::getInstance($component)->delete($component);
            }
        }
        if (array_merge($newIds, $contentIds)) {
            $k = max(array_merge($newIds, $contentIds));
        } else {
            $k = 0;
        }
        $k++;
        $component = $this->_createImageComponent($k);
        $html .= '<img componentkey="'.$k.'" />';

        $this->saveSetting('content_edit', $html);
        return $component;
    }

    public function beforeSave($html)
    {
        $newIds = $this->_parseImageComponentKey($html);
        $contentIds = $this->_parseImageComponentKey($this->getSetting('content'));
        $contentEditIds = $this->_parseImageComponentKey($this->getSetting('content_edit'));
        $ids = array_unique(array_merge($contentIds, $contentEditIds));
        foreach ($ids as $id) {
            if (!in_array($id, $newIds)) {
                $component = $this->_createImageComponent($id);
                Vpc_Admin::getInstance($component)->delete($component);
            }
        }
    }
}