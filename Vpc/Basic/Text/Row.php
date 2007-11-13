<?php
class Vpc_Basic_Text_Row extends Vps_Db_Table_Row
{

    public function getTypeChildComponentNrs($type, $content = null)
    {
        $ret = array();
        foreach ($this->getContentParts($content) as $p) {
            if (is_string($p)) {
            } else if ($p['type'] == $type) {
                $ret[] = $p['nr'];
            }
        }
        return $ret;
    }

    public function getChildComponentNrs($content = null, $type = null)
    {
        $ret = array();
        foreach ($this->getContentParts($content) as $p) {
            if (is_string($p)) {
            } else if ($p['type'] == 'image') {
                $ret[] = 'i'.$p['nr'];
            } else if ($p['type'] == 'link') {
                $ret[] = 'l'.$p['nr'];
            }
        }
        return $ret;
    }

    public function getContentParts($content = null)
    {
        $componentId = $this->page_id.$this->component_key;
        if (is_null($content)) $content = $this->content;

        $ret = array();
        while(preg_match('#^(.*)(<img.+src=[\n ]*"([^"]*)"[^>]*>|<a.+href=[\n ]*"([^"]*)"[^>]*>)(.*)$#Us', $content, $m)) {

            if ($m[1] != '') {
                $ret[] = $m[1];
            }

            if ($m[3]!='' && preg_match('#/media/([0-9]+)/([^/]+)/([^/]+)/#', $m[3], $m2)) {
                $childComponentId = $m2[3];
                if (substr($childComponentId, 0, strlen($componentId)+2)
                            == $componentId.'-i') {
                    $nr = substr($childComponentId, strlen($componentId)+2);
                    $ret[] = array('type'=>'image', 'nr'=>$nr);
                }
            }

            if ($m[4]!='' && preg_match('#/?([^/]+)$#', $m[4], $m2)) {
                $childComponentId = $m2[1];
                if (substr($childComponentId, 0, strlen($componentId)+2)
                            == $componentId.'-l') {
                    $nr = substr($childComponentId, strlen($componentId)+2);
                    $ret[] = array('type'=>'link', 'nr'=>$nr);
                }
            }

            $content = $m[5];
        }
        if(!$m) $ret[] = $content;

        return $ret;
    }

    protected function _delete()
    {
        $class = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                            'imageClass');
        $imageAdmin = Vpc_Admin::getInstance($class);

        $class = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                            'linkClass');
        $linkAdmin = Vpc_Admin::getInstance($class);

        $parts = array_unique(array_merge(
                    $this->getChildComponentNrs($this->content),
                    $this->getChildComponentNrs($this->content_edit)));
        foreach ($parts as $part) {
            if (substr($part, 0, 1) == 'l') {
                $linkAdmin->delete($this->page_id, $this->component_key . '-' . $part);
            } else if (substr($part, 0, 1) == 'i') {
                $imageAdmin->delete($this->page_id, $this->component_key . '-' . $part);
            }
        }
    }

    protected function _update()
    {
        if ($this->content_edit == '') {
            $class = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                                'imageClass');
            $imageAdmin = Vpc_Admin::getInstance($class);

            $class = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                                'linkClass');
            $linkAdmin = Vpc_Admin::getInstance($class);


            $newParts = $this->getChildComponentNrs($this->content);

            $parts = array_unique(array_merge(
                        $this->getChildComponentNrs($this->_cleanData['content']),
                        $this->getChildComponentNrs($this->_cleanData['content_edit'])));
            foreach ($parts as $part) {
                if (!in_array($part, $newParts)) {
                    if (substr($part, 0, 1) == 'l') {
                        $linkAdmin->delete($this->page_id, $this->component_key . '-' . $part);
                    } else if (substr($part, 0, 1) == 'i') {
                        $imageAdmin->delete($this->page_id, $this->component_key . '-' . $part);
                    }
                }
            }
        }
    }
}
