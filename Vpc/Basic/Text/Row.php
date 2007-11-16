<?php
class Vpc_Basic_Text_Row extends Vps_Db_Table_Row
{
    //für Component und Row
    public function getContentParts($content = null)
    {
        $usedChildComponentNrs = array();

        $componentId = $this->page_id.$this->component_key;
        if (is_null($content)) $content = $this->content;

        $ret = array();
        while(preg_match('#^(.*)(<img.+src=[\n ]*"([^"]*)"[^>]*>|<a.+href=[\n ]*"([^"]*)"[^>]*>)(.*)$#Us', $content, $m)) {

            if ($m[1] != '') {
                $ret[] = $m[1];
            }

            if ($m[3] != '' && preg_match('#/media/([0-9]+)/([^/]+)/([^/]+)/#', $m[3], $m2)) {
                $isInvalid = false;
                $childComponentId = $m2[3];
                if (substr($childComponentId, 0, strlen($componentId)+2)
                            == $componentId.'-i') {
                    $nr = substr($childComponentId, strlen($componentId)+2);
                    if (!in_array('i'.$nr, $usedChildComponentNrs)) {
                        $usedChildComponentNrs[] = 'i'.$nr;
                        $ret[] = array('type'=>'image', 'nr'=>$nr, 'html'=>$m[2]);
                    } else {
                        $isInvalid = true;
                    }
                } else {
                    $isInvalid = true;
                }
                if ($isInvalid) {
                    $ret[] = array('type'=>'invalidImage',
                                    'src'=>$m[3],
                                    'uploadId'=>$m2[1],
                                    'componentClass'=>$m2[2],
                                    'componentId'=>$m2[3],
                                    'html'=>$m[2]);
                }
            } else if ($m[3] != '') {
                $ret[] = array('type'=>'invalidImage', 'src'=>$m[3], 'html'=>$m[2]);
            }

            if ($m[4] != '' && preg_match('#/?([^/]+)$#', $m[4], $m2)) {
                $isInvalid = false;
                $childComponentId = $m2[1];
                if (substr($childComponentId, 0, strlen($componentId)+2)
                            == $componentId.'-l') {
                    $nr = substr($childComponentId, strlen($componentId)+2);
                    if (!in_array('l'.$nr, $usedChildComponentNrs)) {
                        $usedChildComponentNrs[] = 'l'.$nr;
                        $ret[] = array('type'=>'link', 'nr'=>$nr, 'html'=>$m[2]);
                    } else {
                        $ret[] = array('type'=>'invalidLink',
                                    'href'=>$m[4],
                                    'componentId'=>$m2[1],
                                    'html'=>$m[2]);
                    }
                } else if (substr($childComponentId, 0, strlen($componentId)+2)
                            == $componentId.'-d') {
                    $nr = substr($childComponentId, strlen($componentId)+2);
                    if (!in_array('d'.$nr, $usedChildComponentNrs)) {
                        $usedChildComponentNrs[] = 'd'.$nr;
                        $ret[] = array('type'=>'download', 'nr'=>$nr, 'html'=>$m[2]);
                    } else {
                        $ret[] = array('type'=>'invalidDownload',
                                    'href'=>$m[4],
                                    'componentId'=>$m2[1],
                                    'html'=>$m[2]);
                    }
                } else if (preg_match('#-l[0-9]+$#', $m2[1])) {
                    $ret[] = array('type'=>'invalidLink',
                                   'href'=>$m[4],
                                   'componentId'=>$m2[1],
                                   'html'=>$m[2]);
                } else if (preg_match('#-d[0-9]+$#', $m2[1])) {
                    $ret[] = array('type'=>'invalidDownload',
                                   'href'=>$m[4],
                                   'componentId'=>$m2[1],
                                   'html'=>$m[2]);
                }
            } else if ($m[4] != '') {
                $ret[] = array('type'=>'invalidLink', 'href'=>$m[4], 'html'=>$m[2]);
            }

            $content = $m[5];
        }
        if(!$m) $ret[] = $content;

        return $ret;
    }

    private function _getChildComponentNrs($content = null, $type = null)
    {
        $ret = array();
        foreach ($this->getContentParts($content) as $p) {
            if (is_string($p)) {
            } else if ($p['type'] == 'image') {
                $ret[] = 'i'.$p['nr'];
            } else if ($p['type'] == 'link') {
                $ret[] = 'l'.$p['nr'];
            } else if ($p['type'] == 'download') {
                $ret[] = 'd'.$p['nr'];
            }
        }
        return $ret;
    }

    private function _getTypeChildComponentNrs($type, $content = null)
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

    public function getMaxChildComponentNr($type)
    {
        $nrs = array_merge($this->_getTypeChildComponentNrs($type, $this->content),
                    $this->_getTypeChildComponentNrs($type, $this->content_edit));
        if (isset($this->_cleanData['content']) && $this->content != $this->_cleanData['content']) {
            $nrs = array_merge($nrs, $this->_getTypeChildComponentNrs($type, $this->_cleanData['content']));
        }
        if ($nrs) {
            $nr = max($nrs);
        } else {
            $nr = 0;
        }
        return $nr;
    }

    protected function _delete()
    {
        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                            'childComponentClasses');
        $imageAdmin = Vpc_Admin::getInstance($classes['image']);
        $linkAdmin = Vpc_Admin::getInstance($classes['link']);
        $downloadAdmin = Vpc_Admin::getInstance($classes['download']);

        $parts = array_unique(array_merge(
                    $this->_getChildComponentNrs($this->content),
                    $this->_getChildComponentNrs($this->content_edit)));
        foreach ($parts as $part) {
            if (substr($part, 0, 1) == 'l') {
                $linkAdmin->delete($this->page_id, $this->component_key . '-' . $part);
            } else if (substr($part, 0, 1) == 'i') {
                $imageAdmin->delete($this->page_id, $this->component_key . '-' . $part);
            } else if (substr($part, 0, 1) == 'd') {
                $downloadAdmin->delete($this->page_id, $this->component_key . '-' . $part);
            }
        }
    }

    //childComponents löschen die aus dem html-code entfernt wurden
    protected function _update()
    {
        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                            'childComponentClasses');

        $imageAdmin = Vpc_Admin::getInstance($classes['image']);
        $linkAdmin = Vpc_Admin::getInstance($classes['link']);
        $downloadAdmin = Vpc_Admin::getInstance($classes['download']);

        $this->content = $this->tidy($this->content);

        $newParts = array_unique(array_merge(
                    $this->_getChildComponentNrs($this->content),
                    $this->_getChildComponentNrs($this->content_edit)));

        $oldParts = array_unique(array_merge(
                    $this->_getChildComponentNrs($this->_cleanData['content']),
                    $this->_getChildComponentNrs($this->_cleanData['content_edit'])));

        foreach ($oldParts as $oldPart) {
            if (!in_array($oldPart, $newParts)) {
                if (substr($oldPart, 0, 1) == 'l') {
                    $linkAdmin->delete($this->page_id, $this->component_key . '-' . $oldPart);
                } else if (substr($oldPart, 0, 1) == 'i') {
                    $imageAdmin->delete($this->page_id, $this->component_key . '-' . $oldPart);
                } else if (substr($oldPart, 0, 1) == 'd') {
                    $downloadAdmin->delete($this->page_id, $this->component_key . '-' . $oldPart);
                }
            }
        }
    }

    public function tidy($html)
    {
        $config = array(
                    'indent'         => true,
                    'output-xhtml'   => true,
                    'clean'          => true,
                    'wrap'           => 200,
                    'doctype'        => 'omit',
                    'drop-proprietary-attributes' => true,
                    'drop-font-tags' => true,
                    'word-2000'      => true,
                    'show-body-only' => true,
                    'bare'           => true,
                    'enclose-block-text'=>true,
                    'enclose-text'   => true,
                    'join-styles'    => false,
                    'logical-emphasis' => true,
                    'lower-literals' => true,
                    'output-bom'     => false,
                    'char-encoding'  =>'utf8',
                    'newline'        =>'LF'
                    );
        $tidy = new tidy;
        $tidy->parseString($html, $config, 'utf8');
        $tidy->cleanRepair();
        $html = $tidy->value;

        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                            'childComponentClasses');

        $imageMaxChildComponentNr = $this->getMaxChildComponentNr('image');
        $linkMaxChildComponentNr = $this->getMaxChildComponentNr('link');
        $downloadMaxChildComponentNr = $this->getMaxChildComponentNr('download');
        $newContent = '';
        foreach ($this->getContentParts($html) as $part) {
            if ($part['type'] == 'invalidImage') {
                if (isset($part['componentId'])
                    && class_exists($part['componentClass'])
                    && (strtolower($part['componentClass']) == 'vpc_basic_image_component'
                        || is_subclass_of($part['componentClass'], 'Vpc_Basic_Image_Component'))) {

                    $srcTableName = Vpc_Abstract::getSetting($part['componentClass'], 'tablename');
                    $srcTable = new $srcTableName(array('componentClass' => $part['componentClass']));
                    $srcRow = $srcTable->findRow($part['componentId']);
                    $srcFileRow = $srcRow->findParentRow('Vps_Dao_File');
                    if ($srcFileRow && $srcFileRow->getFileSource()) {
                        $fileTable = new Vps_Dao_File();
                        $destFileRow = $fileTable->createRow();
                        $destFileRow->copyFile($srcFileRow->getFileSource(),
                                                $srcFileRow->filename,
                                                $srcFileRow->extension);

                        $destTableName = Vpc_Abstract::getSetting($classes['image'], 'tablename');
                        $destTable = new $destTableName(array('componentClass' => $classes['image']));
                        $destRow = $destTable->createRow($srcRow->toArray());
                        $destRow->page_id = $this->page_id;
                        $imageMaxChildComponentNr++;
                        $destRow->component_key = $this->component_key.'-i'.$imageMaxChildComponentNr;
                        $destRow->vps_upload_id = $destFileRow->id;
                        $destRow->save();
                        $dimension = $destRow->getImageDimension();
                        $newContent .= "<img src=\"".$destRow->getImageUrl()."\" ".
                                    "width=\"$dimension[width]\" ".
                                    "height=\"$dimension[height]\" />";
                        continue;
                    }
                }
                $client = new Zend_Http_Client();
                try {
                    $client->setUri($part['src']);
                } catch (Zend_Uri_Exception $e) {
                    //wann relative url mit http_host davor probieren
                    if (isset($_SERVER['HTTP_HOST'])) {
                        $client->setUri('http://'.$_SERVER['HTTP_HOST'].'/'.$part['src']);
                    }
                }
                try {
                    $response = $client->request();
                } catch (Exception $e) {
                    continue;
                }
                if (!$response->isSuccessful()) continue;

                $contentType = $response->getHeader('Content-type');
                if ($contentType == 'image/jpg' || $contentType == 'image/jpeg') {
                    $extension = 'jpg';
                } else if ($contentType == 'image/gif') {
                    $extension = 'gif';
                } else if ($contentType == 'image/png') {
                    $extension = 'png';
                } else {
                    continue;
                }
                $fileTable = new Vps_Dao_File();
                $destFileRow = $fileTable->createRow();

                $path = explode('?', $part['src']);
                if (preg_match('#([^/]*)\\.[a-z]+$#U', $path[0], $m)) {
                    $srcFileName = Zend_Filter::get($m[1], 'Alnum', array(ENT_QUOTES));
                }
                if (!isset($srcFileName) || !$srcFileName) {
                    $srcFileName = 'download';
                }

                $destFileRow->writeFile($response->getBody(), $srcFileName, $extension);
                $destTableName = Vpc_Abstract::getSetting($classes['image'], 'tablename');
                $destTable = new $destTableName(array('componentClass' => $classes['image']));
                $destRow = $destTable->createRow();
                $destRow->page_id = $this->page_id;
                $imageMaxChildComponentNr++;
                $destRow->component_key = $this->component_key.'-i'.$imageMaxChildComponentNr;
                $destRow->vps_upload_id = $destFileRow->id;
                $size = getimagesize($destFileRow->getFileSource());
                $destRow->width = $size[0];
                $destRow->height = $size[1];
                $destRow->filename = $srcFileName;
                $destRow->scale = '';
                $destRow->save();
                $dimension = $destRow->getImageDimension();
                $newContent .= "<img src=\"".$destRow->getImageUrl()."\" ".
                            "width=\"$dimension[width]\" ".
                            "height=\"$dimension[height]\" />";

            } else if ($part['type'] == 'invalidLink') {

                $tableName = Vpc_Abstract::getSetting($classes['link'], 'tablename');
                $table = new $tableName(array('componentClass'=>$classes['link']));
                if (isset($part['componentId'])) {
                    try {
                        $srcRow = $table->findRow($part['componentId']);
                    } catch (Vpc_Exception $e) {
                        $srcRow = false;
                    }
                    if ($srcRow && class_exists($srcRow->link_class)) {
                        $linkTableName = Vpc_Abstract::getSetting($srcRow->link_class, 'tablename');
                        $linkTable = new $linkTableName(array('componentClass'=>$srcRow->link_class));
                        $srcLinkRow = $linkTable->findRow($part['componentId'].'-1');
                        if ($srcLinkRow) {
                            $destRow = $table->createRow();
                            $destRow->link_class = $srcRow->link_class;
                            $destRow->page_id = $this->page_id;
                            $linkMaxChildComponentNr++;
                            $destRow->component_key = $this->component_key.'-l'.$linkMaxChildComponentNr;
                            $destRow->save();
                            $destLinkRow = $linkTable->createRow($srcLinkRow->toArray());
                            $destLinkRow->page_id = $this->page_id;
                            $destLinkRow->component_key = $destRow->component_key.'-1';
                            $destLinkRow->save();
                            $newContent .= "<a href=\"{$destRow->page_id}{$destRow->component_key}\">";
                            continue;
                        }
                    }
                }
                $destRow = $table->createRow();
                $destRow->page_id = $this->page_id;
                $linkMaxChildComponentNr++;
                $destRow->component_key = $this->component_key.'-l'.$linkMaxChildComponentNr;
                $linkClasses = Vpc_Abstract::getSetting($classes['link'], 'childComponentClasses');
                foreach ($linkClasses as $class) {
                    if ($class == 'Vpc_Basic_Link_Extern_Component' ||
                            is_subclass_of($class, 'Vpc_Basic_Link_Extern_Component')) {
                        $destRow->link_class = $class;
                    }
                }
                if (!$destRow->link_class) continue; //kein externer-link möglich
                $destRow->save();

                $linkExternTableName = Vpc_Abstract::getSetting($destRow->link_class, 'tablename');
                $linkExternTable = new $linkExternTableName(array('componentClass'=>$destRow->link_class));
                $row = $linkExternTable->createRow();
                $row->target = $part['href'];
                $row->page_id = $this->page_id;
                $row->component_key = $destRow->component_key.'-1';
                $row->save();
                $newContent .= "<a href=\"{$destRow->page_id}{$destRow->component_key}\">";

            } else if ($part['type'] == 'invalidDownload') {

                $srcTableName = Vpc_Abstract::getSetting($classes['download'], 'tablename');
                $srcTable = new $srcTableName(array('componentClass' => $classes['download']));
                $srcRow = $srcTable->findRow($part['componentId']);
                $srcFileRow = $srcRow->findParentRow('Vps_Dao_File');
                if ($srcFileRow && $srcFileRow->getFileSource()) {
                    $fileTable = new Vps_Dao_File();
                    $destFileRow = $fileTable->createRow();
                    $destFileRow->copyFile($srcFileRow->getFileSource(),
                                            $srcFileRow->filename,
                                            $srcFileRow->extension);

                    $destTableName = Vpc_Abstract::getSetting($classes['download'], 'tablename');
                    $destTable = new $destTableName(array('componentClass' => $classes['download']));
                    $destRow = $destTable->createRow($srcRow->toArray());
                    $destRow->page_id = $this->page_id;
                    $downloadMaxChildComponentNr++;
                    $destRow->component_key = $this->component_key.'-d'.$downloadMaxChildComponentNr;
                    $destRow->vps_upload_id = $destFileRow->id;
                    $destRow->save();
                    $newContent .= "<a href=\"{$destRow->page_id}{$destRow->component_key}\">";
                    continue;
                }


            } else if (is_string($part)) {
                $newContent .= $part;
            } else {
                $newContent .= $part['html'];
            }
        }
        return $newContent;
    }
}
