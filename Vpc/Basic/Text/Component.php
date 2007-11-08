<?php
class Vpc_Basic_Text_Component extends Vpc_Basic_Html_Component
{
    protected $_components = array();

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'         => 'Vpc_Basic_Text_Model',
            'componentName'     => 'Standard.Text',
            'fieldLabel'        => 'Rich Text Editor',
            'width'             => 550,
            'height'            => 400,
            'enableAlignments'  => true,
            'enableColors'      => false,
            'enableFont'        => false,
            'enableFontSize'    => false,
            'enableFormat'      => true,
            'enableLinks'       => true,
            'enableLists'       => true,
            'enableSourceEdit'  => true,
            'imageClass'        => 'Vpc_Basic_Image_Component',
            'linkClass'         => 'Vpc_Basic_Link_Intern_Component',
            'default'           => array(
                'content'       => Vpc_Abstract::LOREM_IPSUM
            )
        ));
    }

    public function getChildComponents()
    {
        return array();
        /*
        $content = $this->getSetting('content').$this->_getSetting('content_edit');
        $this->_parseContentParts($content); //um components zu laden
        return $this->_components;
        */
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['contentParts'] = array();
        /*
        foreach ($this->_parseContentParts($this->_getSetting('content')) as $part) {
            if ($part instanceof Vpc_Abstract) {
                $ret['contentParts'][] = $part->getTemplateVars();
            } else {
                $ret['contentParts'][] = $part;
            }
        }
        */
        return $ret;
    }

    protected function _parseContentParts($content)
    {
        $ret = array();
        //while(preg_match('#^(.*)<img.+(src|componentkey)="([^"]*)"[^>]*>(.*)$#Us', $content, $m)) {
        while(preg_match('#^(.*)(<img.+(src|componentkey)="([^"]*)"[^>]*>|<a.+href="([^"]*)"[^>]*>)(.*)$#Us', $content, $m)) {

            if ($m[1] != '') {
                $ret[] = $m[1];
            }

            $nr = false;
            if ($m[3] == 'src') {
                $nr = $this->_getImageNrBySrc($m[4]);
            } elseif ($m[3] == 'componentkey') {
                $nr = $m[4];
            }
            if ($nr) {
                $ret[] = $this->_createImageComponent($nr);
            }

            if ($m[5] != '') {
                $component = $this->getLinkByHref($m[5]);
                if ($component) {
                    $ret[] = $component;
                } else {
                    //todo: neue link-komponente erstellen, aber mit welcher nr?
                    //kann womöglich auch in getLinkByHref funktion gemacht werden
                    $ret[] = $m[2]; //vorerst einfach html anhängen
                }
            }

            $content = $m[6];
        }
        if(!$m) $ret[] = $content;
        return $ret;
    }
    private function _getLinkComponentDataByHref($href)
    {
        if (substr($href, 0, 7) == 'http://') $href = substr($href, 7);
        if (preg_match('#([A-Za-z_0-9]+):(.+)$#', $href, $m)) {
            $ret['class'] = $m[1];
            if (!is_subclass_of($ret['class'], 'Vpc_Basic_Link_Component')) {
                return null;
            }
            $ret['nr'] = $this->_getChildComponentNrById($m[2]);
            if (!$ret['nr']) return null;
            return $ret;
        }
        return null;
    }

    private function _createLinkComponent($nr, $linkClass)
    {
        if (isset($this->_components[$nr])) return $this->_components[$nr];
        $component = $this->createComponent($linkClass, $nr, $this->getSetting('linkSettings'));
        $this->_components[$nr] = $component;
        return $component;
    }

    private function _createImageComponent($nr)
    {
        if (isset($this->_components[$nr])) return $this->_components[$nr];
        $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Component');
        $component = $this->createComponent($imageClass, $nr, $this->getSetting('imageSettings'));
        $this->_components[$nr] = $component;
        return $component;
    }

    private function _getImageNrBySrc($src)
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
        $nr = $this->_getImageNrBySrc($src);
        if (!$nr) return null;
        return $this->_createImageComponent($nr);
    }

    public function getLinkByHref($href)
    {
        $d = $this->_getLinkComponentDataByHref($href);
        if (!$d) return null;
        return $this->_createLinkComponent($d['nr'], $d['class']);
    }

    protected function _parseChildComponentKey($html)
    {
        $parts = $this->_parseContentParts($html);
        $ret = array();
        foreach ($parts as $part) {
            if ($part instanceof Vpc_Abstract) {
                $ret[] = $this->_getChildComponentNrById($part->getId());
            }
        }
        return $ret;
    }

    public function addImage($html)
    {
        $newNrs = $this->_parseChildComponentKey($html);
        $contentNrs = $this->_parseChildComponentKey($this->getSetting('content'));
        $contentEditNrs = $this->_parseChildComponentKey($this->getSetting('content_edit'));
        foreach ($contentEditNrs as $nr) {
            if (!in_array($nr, $contentNrs) && !in_array($nr, $newNrs)) {
                $component = $this->_components[$nr];
                Vpc_Admin::getInstance($component)->delete($component);
                unset($this->_components[$nr]);
            }
        }
        if (count($this->_components)) {
            $k = max(array_keys($this->_components));
        } else {
            $k = 0;
        }
        $k++;
        $component = $this->_createImageComponent($k);
        $html .= '<img componentkey="'.$k.'" />';

        $this->saveSetting('content_edit', $html);
        return $component;
    }

    public function addLink($html)
    {
        $newNrs = $this->_parseChildComponentKey($html);
        $contentNrs = $this->_parseChildComponentKey($this->getSetting('content'));
        $contentEditNrs = $this->_parseChildComponentKey($this->getSetting('content_edit'));
        foreach ($contentEditNrs as $nr) {
            if (!in_array($nr, $contentNrs) && !in_array($nr, $newNrs)) {
                $component = $this->_components[$nr];
                Vpc_Admin::getInstance($component)->delete($component);
                unset($this->_components[$nr]);
            }
        }
        if (count($this->_components)) {
            $k = max(array_keys($this->_components));
        } else {
            $k = 0;
        }
        $k++;
        $linkClass = $this->_getClassFromSetting('linkClass', 'Vpc_Basic_Link_Component');
        $component = $this->_createLinkComponent($k, $linkClass);
        $html .= '<a href="'.$linkClass.':'.$component->getId().'" />';

        $this->saveSetting('content_edit', $html);
        return $component;
    }

    public function beforeSave($html)
    {
        $newNrs = $this->_parseChildComponentKey($html);
        $contentNrs = $this->_parseChildComponentKey($this->getSetting('content'));
        $contentEditNrs = $this->_parseChildComponentKey($this->getSetting('content_edit'));
        $nrs = array_unique(array_merge($contentNrs, $contentEditNrs));
        foreach ($nrs as $nr) {
            if (!in_array($nr, $newNrs)) {
                $component = $this->_components[$nr];
                Vpc_Admin::getInstance($component)->delete($component);
                unset($this->_components[$nr]);
            }
        }
    }
}