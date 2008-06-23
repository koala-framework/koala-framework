<?php
class Vpc_Basic_Text_Row extends Vpc_Row
{
    //für Component und Row
    public function getContentParts($content = null)
    {
        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                            'childComponentClasses');

        $usedChildComponentNrs = array();

        $componentId = $this->component_id;
        if (is_null($content)) $content = $this->content;

        $ret = array();
                            //1   2                 3                              4              5
        while (preg_match('#^(.*)(<img[^>]+src=[\n ]*"([^"]*)"[^>]*>|<a[^>]+href=[\n ]*"([^"]*)"[^>]*>)(.*)$#Usi', $content, $m)) {

            if ($m[1] != '') {
                $ret[] = $m[1];
            }

            if ($classes['image'] && $m[3] != ''
                && preg_match('#/media/([^/]+)/([^/]+)#', $m[3], $m2)) {
                //"/media/$class/$id/$rule/$type/$checksum/$filename.$extension$random"
                $isInvalid = false;
                $childComponentId = $m2[2];
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
                                    'componentClass'=>$m2[1],
                                    'componentId'=>$childComponentId,
                                    'html'=>$m[2]);
                }
            } else if ($classes['image'] && $m[3] != '') {
                $ret[] = array('type'=>'invalidImage', 'src'=>$m[3], 'html'=>$m[2]);
            } else if ($m[3] != '') {
                $ret[] = $m[2];
            }

            if (($classes['link'] || $classes['download']) && $m[4] != ''
                && preg_match('#/?([^/]+)$#', $m[4], $m2)) {

                $isInvalid = false;
                $childComponentId = $m2[1];
                if ($classes['link']
                    && substr($childComponentId, 0, strlen($componentId)+2)
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
                } else if ($classes['download']
                        && substr($childComponentId, 0, strlen($componentId)+2)
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
                } else if ($classes['link'] && preg_match('#-l[0-9]+$#', $m2[1])) {
                    $ret[] = array('type'=>'invalidLink',
                                   'href'=>$m[4],
                                   'componentId'=>$m2[1],
                                   'html'=>$m[2]);
                } else if ($classes['download'] && preg_match('#-d[0-9]+$#', $m2[1])) {
                    $ret[] = array('type'=>'invalidDownload',
                                   'href'=>$m[4],
                                   'componentId'=>$m2[1],
                                   'html'=>$m[2]);
                } else {
                    $ret[] = array('type'=>'invalidLink', 'href'=>$m[4], 'html'=>$m[2]);
                }
            } else if ($classes['link'] && $m[4] != '') {
                $ret[] = array('type'=>'invalidLink', 'href'=>$m[4], 'html'=>$m[2]);
            } else if ($m[4] != '') {
                $ret[] = $m[2];
            }

            $content = $m[5];
        }
        if(!$m) $ret[] = $content;

        return $ret;
    }

    public function getMaxChildComponentNr($type)
    {
        $select = $this->getTable()->getAdapter()->select();
        $select->from('vpc_basic_text_components', new Zend_Db_Expr('MAX(nr)'))
                ->where('component_id = ?', $this->component_id)
                ->where('component = ?', $type);
        return $select->query()->fetchColumn();
    }

    protected function _delete()
    {
        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                            'childComponentClasses');

        $table = new Vpc_Basic_Text_ChildComponentsModel();
        $rows = $table->fetchAll(array('component_id = ?' => $this->component_id));
        foreach ($rows as $row) {
            $t = substr($row->type, 0, 1);
            $admin = Vpc_Admin::getInstance($classes[$row->type]);
            $admin->delete($this->component_id . '-' . $t.$row->nr);
            $row->delete();
        }
    }

    //childComponents löschen die aus dem html-code entfernt wurden
    protected function _update()
    {
        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                            'childComponentClasses');

        $this->content = $this->tidy($this->content);
        $newParts = $this->getContentParts($this->content);
        $newPartStrings = array();
        foreach ($newParts as $part) {
            if (!is_string($part) ) {
                $newPartStrings[] = $part['type'].$part['nr'];
            }
        }

        $table = new Vpc_Basic_Text_ChildComponentsModel();
        $rows = $table->fetchAll(array('component_id = ?' => $this->component_id));
        $existingParts = array();
        foreach ($rows as $row) {
            $t = substr($row->component, 0, 1);
            if (!in_array($row->component.$row->nr, $newPartStrings)) {
                $admin = Vpc_Admin::getInstance($classes[$row->component]);
                $admin->delete($this->component_id . '-' . $t.$row->nr);
                $row->delete();
            } else {
                if (!$row->saved) {
                    $row->saved = 1;
                    $row->save();
                }
                $existingParts[] = $row->component.$row->nr;
            }
        }

        foreach ($newParts as $part) {
            if (!is_string($part)
                && !in_array($part['type'].$part['nr'], $existingParts)) {
                $row = $table->createRow();
                $row->component_id = $this->component_id;
                $row->component = $part['type'];
                $row->nr = $part['nr'];
                $row->saved = 1;
                $row->save();
            }
        }
    }
    protected function _insert()
    {
        $this->_update();
    }

    public function tidy($html)
    {
        $config = array(
                    'indent'         => true,
                    'output-xhtml'   => true,
                    'clean'          => false,
                    'wrap'           => '86',
                    'doctype'        => 'omit',
                    'drop-proprietary-attributes' => true,
                    'drop-font-tags' => true,
                    'word-2000'      => true,
                    'show-body-only' => true,
                    'bare'           => true,
                    'enclose-block-text'=>true,
                    'enclose-text'   => true,
                    'join-styles'    => false,
                    'join-classes'   => false,
                    'logical-emphasis' => true,
                    'lower-literals' => true,
                    'literal-attributes' => false,
                    'indent-spaces' => 2,
                    'quote-nbsp'     => true,
                    'output-bom'     => false,
                    'char-encoding'  =>'utf8',
                    'newline'        =>'LF',
                    'uppercase-tags' =>'false'
                    );
        $enableTidy = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'enableTidy');
        $enableFontSize = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'enableFontSize');
        if ($enableFontSize){
            $config['drop-font-tags'] = false;
        }
        if (class_exists('tidy') && $enableTidy) {
            $tidy = new tidy;
            $html = str_replace('&nbsp;', '#nbsp#', $html); //einstellungen oben funktionieren nicht richtig
            $tidy->parseString($html, $config, 'utf8');
            $tidy->cleanRepair();
            $html = $tidy->value;
            $parser = new Vpc_Basic_Text_Parser($this);
            $parser->setEnableColor(Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'enableColors'));
            $parser->setEnableTagsWhitelist(Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'enableTagsWhitelist'));
            $parser->setEnableStyles(Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'enableStyles'));
            $html = $parser->parse($html);
            $tidy->parseString($html, $config, 'utf8');
            $tidy->cleanRepair();
            $html = $tidy->value;
            $html = str_replace('#nbsp#', '&nbsp;', $html);
        }

        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                            'childComponentClasses');

        $imageMaxChildComponentNr = $this->getMaxChildComponentNr('image');
        $linkMaxChildComponentNr = $this->getMaxChildComponentNr('link');
        $downloadMaxChildComponentNr = $this->getMaxChildComponentNr('download');
        $newContent = '';

        foreach ($this->getContentParts($html) as $part) {
            if (is_string($part)) {
                $newContent .= $part;
            } else if ($part['type'] == 'invalidImage') {
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
                        $imageMaxChildComponentNr++;
                        $destRow->component_id = $this->component_id.'-i'.$imageMaxChildComponentNr;
                        $destRow->vps_upload_id = $destFileRow->id;
                        $destRow->save();
                        $dimension = $destRow->getImageDimension();
                        $newContent .= "<img src=\"".$destRow->getFileUrl()."\" ".
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

                $destFileRow->writeFile($response->getBody(), $srcFileName, $extension, $contentType);
                $destTableName = Vpc_Abstract::getSetting($classes['image'], 'tablename');
                $destTable = new $destTableName(array('componentClass' => $classes['image']));
                $destRow = $destTable->createRow();
                $imageMaxChildComponentNr++;
                $destRow->component_id = $this->component_id.'-i'.$imageMaxChildComponentNr;
                $destRow->vps_upload_id = $destFileRow->id;
                $size = getimagesize($destFileRow->getFileSource());
                $destRow->width = $size[0];
                $destRow->height = $size[1];
                $destRow->filename = $srcFileName;
                $destRow->scale = '';
                $destRow->save();
                $dimension = $destRow->getImageDimension();
                $newContent .= "<img src=\"".$destRow->getFileUrl()."\" ".
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
                            $linkMaxChildComponentNr++;
                            $destRow->component_id = $this->component_id.'-l'.$linkMaxChildComponentNr;
                            $destRow->save();
                            $destLinkRow = $linkTable->createRow($srcLinkRow->toArray());
                            $destLinkRow->component_id = $destRow->component_id.'-1';
                            $destLinkRow->save();
                            $newContent .= "<a href=\"{$destRow->component_id}\">";
                            continue;
                        }
                    }
                }
                $destRow = $table->createRow();
                $linkClasses = Vpc_Abstract::getSetting($classes['link'], 'childComponentClasses');
                if (preg_match('#^mailto:#', $part['href'], $m)) {
                    foreach ($linkClasses as $class) {
                        if ($class == 'Vpc_Basic_LinkTag_Mail_Component' ||
                                is_subclass_of($class, 'Vpc_Basic_LinkTag_Mail_Component')) {
                            $destRow->link_class = $class;
                        }
                    }
                } else {
                    foreach ($linkClasses as $class) {
                        if ($class == 'Vpc_Basic_LinkTag_Extern_Component' ||
                                is_subclass_of($class, 'Vpc_Basic_LinkTag_Extern_Component')) {
                            $destRow->link_class = $class;
                        }
                    }
                }
                if (!$destRow->link_class) continue; //kein externer-link möglich
                $linkMaxChildComponentNr++;
                $destRow->component_id = $this->component_id.'-l'.$linkMaxChildComponentNr;
                $destRow->save();

                $linkExternTableName = Vpc_Abstract::getSetting($destRow->link_class, 'tablename');
                $linkExternTable = new $linkExternTableName(array('componentClass'=>$destRow->link_class));
                $row = $linkExternTable->createRow();
                if ($destRow->link_class == 'Vpc_Basic_LinkTag_Extern_Component' ||
                        is_subclass_of($destRow->link_class, 'Vpc_Basic_LinkTag_Extern_Component')) {
                    $row->target = $part['href'];
                } else {
                    preg_match('#^mailto:(.*)\\??(.*)#', $part['href'], $m);
                    $row->mail = $m[1];
                    $m = parse_str($m[2]);
                    $row->subject = isset($m['subject']) ? $m['subject'] : '';
                    $row->text = isset($m['body']) ? $m['body'] : '';
                }
                $row->component_id = $destRow->component_id.'-1';
                $row->save();
                $newContent .= "<a href=\"{$destRow->component_id}\">";

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
                    $downloadMaxChildComponentNr++;
                    $destRow->component_id = $this->component_id.'-d'.$downloadMaxChildComponentNr;
                    $destRow->vps_upload_id = $destFileRow->id;
                    $destRow->save();
                    $newContent .= "<a href=\"{$destRow->component_id}\">";
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
