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

    private $_images;

    function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['template'] = 'Text.html';
        return $return;
    }
    protected function _init()
    {
    }
    protected function _parseContentParts($content)
    {
        $ret = array();
        $parts = parent::_parseContentParts($content);
        foreach ($parts as $part) {
            if (is_string($part)) {
                while(preg_match('#^(.*)<img.+(src|componentkey)="([^"]*)"[^>]*>(.*)$#Us', $part, $m)) {
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
                        $this->_components[$id] = $component;
                    }
                    $part = $m[4];
                }

                if(!$m) $ret[] = $part;
            } else {
                $ret[] = $part;
            }
        }
        return $ret;
    }

/*
    public function getChildComponents()
    {
        $return = parent::getChildComponents();
        $return = array_merge($return, $this->_getImageComponents());
        return $return;
    }

    protected function _getImageComponents()
    {
        if (isset($this->_images)) return $this->_images;

        $ids = array_unique(
            array_merge($this->_parseHtmlImageComponentIds($this->getSetting('content_edit')),
                    $this->_parseHtmlImageComponentIds($this->getSetting('content'))));

        $this->_images = array();
        foreach ($ids as $id) {
            $this->_images[$id] = $this->_createImageComponent($id);
        }
        return $this->_images;
    }*/
    protected function _getEditContent()
    {
        return $this->getSetting('content').$this->getSetting('content_edit');
    }

    private function _createImageComponent($id)
    {
        $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Component');
        return $this->createComponent($imageClass, $id, $this->getSetting('imageSettings'));
    }
/*
    private function _parseHtmlImageComponentIds($html)
    {
        $ret = array();
        preg_match_all('#<img.+src="([^"]*)"[^>]*>#', $html, $m);
        foreach ($m[1] as $src) {
            $id = $this->_getImageIdBySrc($src);
            if ($id) {
                $ret[] = $id;
            }
        }
        preg_match_all('#<img.+componentkey="([^"]+)"[^>]*>#', $html, $m);
        foreach ($m[1] as $id) {
            $ret[] = $id;
        }
        return array_unique($ret);
    }
*/
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

    protected function _parseHtmlImageComponentIds($html)
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
        $newIds = $this->_parseHtmlImageComponentIds($html);
        $contentIds = $this->_parseHtmlImageComponentIds($this->getSetting('content'));
        $contentEditIds = $this->_parseHtmlImageComponentIds($this->getSetting('content_edit'));
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
        $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Component');
        $this->images[$k] = $this->createComponent($imageClass, $k,
                                            $this->getSetting('imageSettings'));
        $html .= '<img componentkey="'.$k.'" />';

        $this->saveSetting('content_edit', $html);
        return $this->images[$k];
    }

    public function beforeSave($html)
    {
        $newIds = $this->_parseHtmlImageComponentIds($html);
        $contentIds = $this->_parseHtmlImageComponentIds($this->getSetting('content'));
        $contentEditIds = $this->_parseHtmlImageComponentIds($this->getSetting('content_edit'));
        $ids = array_unique(array_merge($contentIds, $contentEditIds));
        foreach ($ids as $id) {
            if (!in_array($id, $newIds)) {
                $component = $this->_createImageComponent($id);
                Vpc_Admin::getInstance($component)->delete($component);
            }
        }
    }
}