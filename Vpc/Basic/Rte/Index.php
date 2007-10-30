<?php
class Vpc_Basic_Rte_Index extends Vpc_Abstract
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
        'imageClass'        => 'Vpc_Basic_Image_Index',
        'imageSettings'     => array('allowBlank' => false)
    );

    protected $_tablename = 'Vpc_Basic_Rte_IndexModel';
    const NAME = 'Standard.Rte';

    private $_images;

    function getTemplateVars()
    {
        $return['content'] = $this->getSetting('content');
        $return['template'] = 'Rte.html';
        return $return;
    }
    protected function _init()
    {
    }

    public function getChildComponents()
    {
        return $this->_getImageComponents();
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
    }

    private function _createImageComponent($id)
    {
        $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Index');
        return $this->createComponent($imageClass, $id, $this->getSetting('imageSettings'));
    }

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

    private function _getImageIdBySrc($src)
    {
        //media/:uploadId/:componentId/:checksum/:filename
        if (preg_match('#/media/([0-9]+)/'
            .preg_quote($this->getId())
            .'-([0-9]+)/#', $src, $m)) {
            return $m[2];
        }
        return false;
    }

    public function getImageBySrc($src)
    {
        $id = $this->_getImageIdBySrc($src);
        if (!$id) return null;
        return $this->_createImageComponent($id);
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
        $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Index');
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