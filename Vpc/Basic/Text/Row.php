<?p
class Vpc_Basic_Text_Row extends Vps_Db_Table_R

    //für Component und R
    public function getContentParts($content = nul
   
        $usedChildComponentNrs = array(

        $componentId = $this->page_id.$this->component_ke
        if (is_null($content)) $content = $this->conten

        $ret = array(
        while(preg_match('#^(.*)(<img.+src=[\n ]*"([^"]*)"[^>]*>|<a.+href=[\n ]*"([^"]*)"[^>]*>)(.*)$#Us', $content, $m))

            if ($m[1] != '')
                $ret[] = $m[1
           

            if ($m[3] != '' && preg_match('#/media/([0-9]+)/([^/]+)/([^/]+)/#', $m[3], $m2))
                $isInvalid = fals
                $childComponentId = $m2[3
                if (substr($childComponentId, 0, strlen($componentId)+
                            == $componentId.'-i')
                    $nr = substr($childComponentId, strlen($componentId)+2
                    if (!in_array('i'.$nr, $usedChildComponentNrs))
                        $usedChildComponentNrs[] = 'i'.$n
                        $ret[] = array('type'=>'image', 'nr'=>$nr, 'html'=>$m[2]
                    } else
                        $isInvalid = tru
                   
                } else
                    $isInvalid = tru
               
                if ($isInvalid)
                    $ret[] = array('type'=>'invalidImage
                                    'src'=>$m[3
                                    'uploadId'=>$m2[1
                                    'componentClass'=>$m2[2
                                    'componentId'=>$m2[3
                                    'html'=>$m[2]
               
            } else if ($m[3] != '')
                $ret[] = array('type'=>'invalidImage', 'src'=>$m[3], 'html'=>$m[2]
           

            if ($m[4] != '' && preg_match('#/?([^/]+)$#', $m[4], $m2))
                $isInvalid = fals
                $childComponentId = $m2[1
                if (substr($childComponentId, 0, strlen($componentId)+
                            == $componentId.'-l')
                    $nr = substr($childComponentId, strlen($componentId)+2
                    if (!in_array('l'.$nr, $usedChildComponentNrs))
                        $usedChildComponentNrs[] = 'l'.$n
                        $ret[] = array('type'=>'link', 'nr'=>$nr, 'html'=>$m[2]
                    } else
                        $ret[] = array('type'=>'invalidLink
                                    'href'=>$m[4
                                    'componentId'=>$m2[1
                                    'html'=>$m[2]
                   
                } else if (substr($childComponentId, 0, strlen($componentId)+
                            == $componentId.'-d')
                    $nr = substr($childComponentId, strlen($componentId)+2
                    if (!in_array('d'.$nr, $usedChildComponentNrs))
                        $usedChildComponentNrs[] = 'd'.$n
                        $ret[] = array('type'=>'download', 'nr'=>$nr, 'html'=>$m[2]
                    } else
                        $ret[] = array('type'=>'invalidDownload
                                    'href'=>$m[4
                                    'componentId'=>$m2[1
                                    'html'=>$m[2]
                   
                } else if (preg_match('#-l[0-9]+$#', $m2[1]))
                    $ret[] = array('type'=>'invalidLink
                                   'href'=>$m[4
                                   'componentId'=>$m2[1
                                   'html'=>$m[2]
                } else if (preg_match('#-d[0-9]+$#', $m2[1]))
                    $ret[] = array('type'=>'invalidDownload
                                   'href'=>$m[4
                                   'componentId'=>$m2[1
                                   'html'=>$m[2]
               
            } else if ($m[4] != '')
                $ret[] = array('type'=>'invalidLink', 'href'=>$m[4], 'html'=>$m[2]
           

            $content = $m[5
       
        if(!$m) $ret[] = $conten

        return $re
   

    private function _getChildComponentNrs($content = null, $type = nul
   
        $ret = array(
        foreach ($this->getContentParts($content) as $p)
            if (is_string($p))
            } else if ($p['type'] == 'image')
                $ret[] = 'i'.$p['nr'
            } else if ($p['type'] == 'link')
                $ret[] = 'l'.$p['nr'
            } else if ($p['type'] == 'download')
                $ret[] = 'd'.$p['nr'
           
       
        return $re
   

    private function _getTypeChildComponentNrs($type, $content = nul
   
        $ret = array(
        foreach ($this->getContentParts($content) as $p)
            if (is_string($p))
            } else if ($p['type'] == $type)
                $ret[] = $p['nr'
           
       
        return $re
   

    public function getMaxChildComponentNr($typ
   
        $nrs = array_merge($this->_getTypeChildComponentNrs($type, $this->content
                    $this->_getTypeChildComponentNrs($type, $this->content_edit)
        if (isset($this->_cleanData['content']) && $this->content != $this->_cleanData['content'])
            $nrs = array_merge($nrs, $this->_getTypeChildComponentNrs($type, $this->_cleanData['content'])
       
        if ($nrs)
            $nr = max($nrs
        } else
            $nr = 
       
        return $n
   

    protected function _delete
   
        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(
                                            'childComponentClasses'
        $imageAdmin = Vpc_Admin::getInstance($classes['image']
        $linkAdmin = Vpc_Admin::getInstance($classes['link']
        $downloadAdmin = Vpc_Admin::getInstance($classes['download']

        $parts = array_unique(array_merg
                    $this->_getChildComponentNrs($this->content
                    $this->_getChildComponentNrs($this->content_edit))
        foreach ($parts as $part)
            if (substr($part, 0, 1) == 'l')
                $linkAdmin->delete($this->page_id, $this->component_key . '-' . $part
            } else if (substr($part, 0, 1) == 'i')
                $imageAdmin->delete($this->page_id, $this->component_key . '-' . $part
            } else if (substr($part, 0, 1) == 'd')
                $downloadAdmin->delete($this->page_id, $this->component_key . '-' . $part
           
       
   

    //childComponents löschen die aus dem html-code entfernt wurd
    protected function _update
   
        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(
                                            'childComponentClasses'

        $imageAdmin = Vpc_Admin::getInstance($classes['image']
        $linkAdmin = Vpc_Admin::getInstance($classes['link']
        $downloadAdmin = Vpc_Admin::getInstance($classes['download']

        $this->content = $this->tidy($this->content

        $newParts = array_unique(array_merg
                    $this->_getChildComponentNrs($this->content
                    $this->_getChildComponentNrs($this->content_edit))

        $oldParts = array_unique(array_merg
                    $this->_getChildComponentNrs($this->_cleanData['content']
                    $this->_getChildComponentNrs($this->_cleanData['content_edit']))

        foreach ($oldParts as $oldPart)
            if (!in_array($oldPart, $newParts))
                if (substr($oldPart, 0, 1) == 'l')
                    $linkAdmin->delete($this->page_id, $this->component_key . '-' . $oldPart
                } else if (substr($oldPart, 0, 1) == 'i')
                    $imageAdmin->delete($this->page_id, $this->component_key . '-' . $oldPart
                } else if (substr($oldPart, 0, 1) == 'd')
                    $downloadAdmin->delete($this->page_id, $this->component_key . '-' . $oldPart
               
           
       
   

    public function tidy($htm
   
        $config = arra
                    'indent'         => tru
                    'output-xhtml'   => tru
                    'clean'          => tru
                    'wrap'           => 20
                    'doctype'        => 'omit
                    'drop-proprietary-attributes' => tru
                    'drop-font-tags' => tru
                    'word-2000'      => tru
                    'show-body-only' => tru
                    'bare'           => tru
                    'enclose-block-text'=>tru
                    'enclose-text'   => tru
                    'join-styles'    => fals
                    'logical-emphasis' => tru
                    'lower-literals' => tru
                    'output-bom'     => fals
                    'char-encoding'  =>'utf8
                    'newline'        =>'L
                    )
        if (class_exists('tidy')) 
            $tidy = new tidy
            $tidy->parseString($html, $config, 'utf8')
            $tidy->cleanRepair()
            $html = $tidy->value
       

        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(
                                            'childComponentClasses'

        $imageMaxChildComponentNr = $this->getMaxChildComponentNr('image'
        $linkMaxChildComponentNr = $this->getMaxChildComponentNr('link'
        $downloadMaxChildComponentNr = $this->getMaxChildComponentNr('download'
        $newContent = '
        foreach ($this->getContentParts($html) as $part)
            if ($part['type'] == 'invalidImage')
                if (isset($part['componentId'
                    && class_exists($part['componentClass'
                    && (strtolower($part['componentClass']) == 'vpc_basic_image_componen
                        || is_subclass_of($part['componentClass'], 'Vpc_Basic_Image_Component')))

                    $srcTableName = Vpc_Abstract::getSetting($part['componentClass'], 'tablename'
                    $srcTable = new $srcTableName(array('componentClass' => $part['componentClass'])
                    $srcRow = $srcTable->findRow($part['componentId']
                    $srcFileRow = $srcRow->findParentRow('Vps_Dao_File'
                    if ($srcFileRow && $srcFileRow->getFileSource())
                        $fileTable = new Vps_Dao_File(
                        $destFileRow = $fileTable->createRow(
                        $destFileRow->copyFile($srcFileRow->getFileSource(
                                                $srcFileRow->filenam
                                                $srcFileRow->extension

                        $destTableName = Vpc_Abstract::getSetting($classes['image'], 'tablename'
                        $destTable = new $destTableName(array('componentClass' => $classes['image'])
                        $destRow = $destTable->createRow($srcRow->toArray()
                        $destRow->page_id = $this->page_i
                        $imageMaxChildComponentNr+
                        $destRow->component_key = $this->component_key.'-i'.$imageMaxChildComponentN
                        $destRow->vps_upload_id = $destFileRow->i
                        $destRow->save(
                        $dimension = $destRow->getImageDimension(
                        $newContent .= "<img src=\"".$destRow->getImageUrl()."\" 
                                    "width=\"$dimension[width]\" 
                                    "height=\"$dimension[height]\" />
                        continu
                   
               
                $client = new Zend_Http_Client(
                try
                    $client->setUri($part['src']
                } catch (Zend_Uri_Exception $e)
                    //wann relative url mit http_host davor probier
                    if (isset($_SERVER['HTTP_HOST']))
                        $client->setUri('http://'.$_SERVER['HTTP_HOST'].'/'.$part['src']
                   
               
                try
                    $response = $client->request(
                } catch (Exception $e)
                    continu
               
                if (!$response->isSuccessful()) continu

                $contentType = $response->getHeader('Content-type'
                if ($contentType == 'image/jpg' || $contentType == 'image/jpeg')
                    $extension = 'jpg
                } else if ($contentType == 'image/gif')
                    $extension = 'gif
                } else if ($contentType == 'image/png')
                    $extension = 'png
                } else
                    continu
               
                $fileTable = new Vps_Dao_File(
                $destFileRow = $fileTable->createRow(

                $path = explode('?', $part['src']
                if (preg_match('#([^/]*)\\.[a-z]+$#U', $path[0], $m))
                    $srcFileName = Zend_Filter::get($m[1], 'Alnum', array(ENT_QUOTES)
               
                if (!isset($srcFileName) || !$srcFileName)
                    $srcFileName = 'download
               

                $destFileRow->writeFile($response->getBody(), $srcFileName, $extension
                $destTableName = Vpc_Abstract::getSetting($classes['image'], 'tablename'
                $destTable = new $destTableName(array('componentClass' => $classes['image'])
                $destRow = $destTable->createRow(
                $destRow->page_id = $this->page_i
                $imageMaxChildComponentNr+
                $destRow->component_key = $this->component_key.'-i'.$imageMaxChildComponentN
                $destRow->vps_upload_id = $destFileRow->i
                $size = getimagesize($destFileRow->getFileSource()
                $destRow->width = $size[0
                $destRow->height = $size[1
                $destRow->filename = $srcFileNam
                $destRow->scale = '
                $destRow->save(
                $dimension = $destRow->getImageDimension(
                $newContent .= "<img src=\"".$destRow->getImageUrl()."\" 
                            "width=\"$dimension[width]\" 
                            "height=\"$dimension[height]\" />

            } else if ($part['type'] == 'invalidLink')

                $tableName = Vpc_Abstract::getSetting($classes['link'], 'tablename'
                $table = new $tableName(array('componentClass'=>$classes['link'])
                if (isset($part['componentId']))
                    try
                        $srcRow = $table->findRow($part['componentId']
                    } catch (Vpc_Exception $e)
                        $srcRow = fals
                   
                    if ($srcRow && class_exists($srcRow->link_class))
                        $linkTableName = Vpc_Abstract::getSetting($srcRow->link_class, 'tablename'
                        $linkTable = new $linkTableName(array('componentClass'=>$srcRow->link_class)
                        $srcLinkRow = $linkTable->findRow($part['componentId'].'-1'
                        if ($srcLinkRow)
                            $destRow = $table->createRow(
                            $destRow->link_class = $srcRow->link_clas
                            $destRow->page_id = $this->page_i
                            $linkMaxChildComponentNr+
                            $destRow->component_key = $this->component_key.'-l'.$linkMaxChildComponentN
                            $destRow->save(
                            $destLinkRow = $linkTable->createRow($srcLinkRow->toArray()
                            $destLinkRow->page_id = $this->page_i
                            $destLinkRow->component_key = $destRow->component_key.'-1
                            $destLinkRow->save(
                            $newContent .= "<a href=\"{$destRow->page_id}{$destRow->component_key}\">
                            continu
                       
                   
               
                $destRow = $table->createRow(
                $destRow->page_id = $this->page_i
                $linkMaxChildComponentNr+
                $destRow->component_key = $this->component_key.'-l'.$linkMaxChildComponentN
                $linkClasses = Vpc_Abstract::getSetting($classes['link'], 'childComponentClasses'
                foreach ($linkClasses as $class)
                    if ($class == 'Vpc_Basic_Link_Extern_Component' 
                            is_subclass_of($class, 'Vpc_Basic_Link_Extern_Component'))
                        $destRow->link_class = $clas
                   
               
                if (!$destRow->link_class) continue; //kein externer-link mögli
                $destRow->save(

                $linkExternTableName = Vpc_Abstract::getSetting($destRow->link_class, 'tablename'
                $linkExternTable = new $linkExternTableName(array('componentClass'=>$destRow->link_class)
                $row = $linkExternTable->createRow(
                $row->target = $part['href'
                $row->page_id = $this->page_i
                $row->component_key = $destRow->component_key.'-1
                $row->save(
                $newContent .= "<a href=\"{$destRow->page_id}{$destRow->component_key}\">

            } else if ($part['type'] == 'invalidDownload')

                $srcTableName = Vpc_Abstract::getSetting($classes['download'], 'tablename'
                $srcTable = new $srcTableName(array('componentClass' => $classes['download'])
                $srcRow = $srcTable->findRow($part['componentId']
                $srcFileRow = $srcRow->findParentRow('Vps_Dao_File'
                if ($srcFileRow && $srcFileRow->getFileSource())
                    $fileTable = new Vps_Dao_File(
                    $destFileRow = $fileTable->createRow(
                    $destFileRow->copyFile($srcFileRow->getFileSource(
                                            $srcFileRow->filenam
                                            $srcFileRow->extension

                    $destTableName = Vpc_Abstract::getSetting($classes['download'], 'tablename'
                    $destTable = new $destTableName(array('componentClass' => $classes['download'])
                    $destRow = $destTable->createRow($srcRow->toArray()
                    $destRow->page_id = $this->page_i
                    $downloadMaxChildComponentNr+
                    $destRow->component_key = $this->component_key.'-d'.$downloadMaxChildComponentN
                    $destRow->vps_upload_id = $destFileRow->i
                    $destRow->save(
                    $newContent .= "<a href=\"{$destRow->page_id}{$destRow->component_key}\">
                    continu
               


            } else if (is_string($part))
                $newContent .= $par
            } else
                $newContent .= $part['html'
           
       
        return $newConten
   

