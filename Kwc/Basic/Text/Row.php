<?php
class Kwc_Basic_Text_Row extends Kwf_Model_Proxy_Row
{
    private $_classes;
    private $_componentClass;

    protected function _init()
    {
        parent::_init();
        $this->_componentClass = $this->getModel()->getComponentClass();
        $this->_classes = Kwc_Abstract::getChildComponentClasses($this->_componentClass, 'child');
    }

    //für Component und Row
    public function getContentParts($content = null, $ignoreLinksWithClass = null)
    {
        $classes = $this->_classes;

        $usedChildComponentNrs = array();

        $componentId = $this->component_id;
        if (is_null($content)) $content = $this->content;

        $ret = array();
                            //1   2                 3                              4              5
        while (preg_match('#^(.*)(<img[^>]+src=[\n ]*"([^"]*)"[^>]*>|<a[^>]+href=[\n ]*"([^"]*)"[^>]*>)(.*)$#Usi', $content, $m)) {

            if ($m[1] != '') {
                $ret[] = $m[1];
            }

            if (isset($classes['image']) && $m[3] != ''
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
            } else if (isset($classes['image']) && $m[3] != '') {
                $ret[] = array('type'=>'invalidImage', 'src'=>$m[3], 'html'=>$m[2]);
            } else if ($m[3] != '') {
                //kein image möglich
            }

            if ((isset($classes['link']) || isset($classes['download'])) && $m[4] != ''
                && preg_match('#/?([^/]+)$#', $m[4], $m2)) {

                $isInvalid = false;
                $childComponentId = $m2[1];
                if (isset($classes['link'])
                    && substr($childComponentId, 0, strlen($componentId)+2)
                            == $componentId.'-l') {
                    $nr = substr($childComponentId, strlen($componentId)+2);
                    if (!in_array('l'.$nr, $usedChildComponentNrs)) {
                        $usedChildComponentNrs[] = 'l'.$nr;
                        $ret[] = array('type'=>'link', 'nr'=>$nr, 'html'=>$m[2]);
                    } else {
                        $ret[] = $this->_checkIgnoreLinksWithClass(
                            $ignoreLinksWithClass,
                            array('type'=>'invalidLink', 'href'=>$m[4], 'componentId'=>$m2[1], 'html'=>$m[2])
                        );
                    }
                } else if (isset($classes['download'])
                        && substr($childComponentId, 0, strlen($componentId)+2)
                            == $componentId.'-d') {
                    $nr = substr($childComponentId, strlen($componentId)+2);
                    if (!in_array('d'.$nr, $usedChildComponentNrs)) {
                        $usedChildComponentNrs[] = 'd'.$nr;
                        $ret[] = array('type'=>'download', 'nr'=>$nr, 'html'=>$m[2]);
                    } else {
                        $ret[] = $this->_checkIgnoreLinksWithClass(
                            $ignoreLinksWithClass,
                            array('type'=>'invalidDownload', 'href'=>$m[4], 'componentId'=>$m2[1], 'html'=>$m[2])
                        );
                    }
                } else if (isset($classes['link']) && preg_match('#-l[0-9]+$#', $m2[1])) {
                    $ret[] = $this->_checkIgnoreLinksWithClass(
                        $ignoreLinksWithClass,
                        array('type'=>'invalidLink', 'href'=>$m[4], 'componentId'=>$m2[1], 'html'=>$m[2])
                    );
                } else if (isset($classes['download']) && preg_match('#-d[0-9]+$#', $m2[1])) {
                    $ret[] = $this->_checkIgnoreLinksWithClass(
                        $ignoreLinksWithClass,
                        array('type'=>'invalidDownload', 'href'=>$m[4], 'componentId'=>$m2[1], 'html'=>$m[2])
                    );
                } else {
                    $ret[] = $this->_checkIgnoreLinksWithClass(
                        $ignoreLinksWithClass,
                        array('type'=>'invalidLink', 'href'=>$m[4], 'html'=>$m[2])
                    );
                }
            } else if (isset($classes['link']) && $m[4] != '') {
                $ret[] = $this->_checkIgnoreLinksWithClass($ignoreLinksWithClass, array('type'=>'invalidLink', 'href'=>$m[4], 'html'=>$m[2]));
            } else if ($m[4] != '') {
                //kein link möglich
            }

            $content = $m[5];
        }
        if(!$m) $ret[] = $content;

        return $ret;
    }

    protected function _checkIgnoreLinksWithClass($ignoreLinksWithClass, $config = array())
    {
        $ret = null;
        if ($ignoreLinksWithClass !== null && strpos($config['html'], $ignoreLinksWithClass)) {
            $ret = $config['html'];
        } else {
            $ret = $config;
        }
        return $ret;
    }

    public function getMaxChildComponentNr($type)
    {
        $select = $this->getModel()->select();
        $select->order('nr', 'DESC');
        $select->limit(1);
        $select->whereEquals('component', $type);
        $rows = $this->getChildRows('ChildComponents', $select);
        if (!count($rows)) return 0;
        return $rows->current()->nr;
    }

    protected function _beforeDelete()
    {
        $table = new Kwc_Basic_Text_ChildComponentsModel();
        $rows = $table->fetchAll(array('component_id = ?' => $this->component_id));
        foreach ($rows as $row) {
            $t = substr($row->component, 0, 1);
            $admin = Kwc_Admin::getInstance($this->_classes[$row->component]);
            $admin->delete($this->component_id . '-' . $t.$row->nr);
            $row->delete();
        }
    }

    //childComponents löschen die aus dem html-code entfernt wurden
    protected function _beforeSave()
    {
        $classes = $this->_classes;

        $this->content = $this->tidy($this->content);
        $newParts = $this->getContentParts($this->content);
        $newPartStrings = array();
        foreach ($newParts as $part) {
            if (!is_string($part) ) {
                $newPartStrings[] = $part['type'].$part['nr'];
            }
        }

        $rows = $this->getChildRows('ChildComponents');
        $existingParts = array();
        foreach ($rows as $row) {
            $t = substr($row->component, 0, 1);
            if (!in_array($row->component.$row->nr, $newPartStrings)) {
                $admin = Kwc_Admin::getInstance($classes[$row->component]);
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
                $row = $this->createChildRow('ChildComponents');
                $row->component = $part['type'];
                $row->nr = $part['nr'];
                $row->saved = 1;
            }
        }
    }

    public function tidy($html, Kwc_Basic_Text_Parser $parser = null)
    {
        //convert umlauts from NFD to NFC
        $html = str_replace('u'.chr(0xCC).chr(0x88), 'ü', $html);
        $html = str_replace('a'.chr(0xCC).chr(0x88), 'ä', $html);
        $html = str_replace('o'.chr(0xCC).chr(0x88), 'ö', $html);
        $html = str_replace('U'.chr(0xCC).chr(0x88), 'Ü', $html);
        $html = str_replace('A'.chr(0xCC).chr(0x88), 'Ä', $html);
        $html = str_replace('O'.chr(0xCC).chr(0x88), 'Ö', $html);

        //delete zero width space, causes problems in Lotus Notes
        $html = str_replace(chr(0xE2).chr(0x80).chr(0x8B), '', $html);

        //delete BOM that might have sneaked into the text (at any position)
        $html = str_replace(chr(0xEF).chr(0xBB).chr(0xBF), '', $html);

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
        $enableTidy = Kwc_Abstract::getSetting($this->_componentClass, 'enableTidy');
        $enableFontSize = Kwc_Abstract::getSetting($this->_componentClass, 'enableFontSize');
        if ($enableFontSize){
            $config['drop-font-tags'] = false;
        }
        if ($enableTidy) {

            //woraround für tidy bug wo er zwei class-attribute in einen
            //tag schreibt wenn eins davon leer ist
            //siehe Kwc_Basic_Text_ModelContentTest::testTidyRemovesSomeText
            //einfach leere klassen löschen
            $html = preg_replace('#<(.[a-z]+) ([^>]*)class=""([^>]*)>#', '<\1 \2 \3>', $html);

            //html kommentare löschen, löscht auch word schas mit
            $html = preg_replace('#<!--.*?-->#s', '', $html);

            $html = str_replace('_mce_type="bookmark"', 'class="_mce_type-bookmark"', $html);
            $html = str_replace('&nbsp;', '#nbsp#', $html); //einstellungen oben funktionieren nicht richtig

            if (class_exists('tidy')) {
                $tidy = new tidy;
                $tidy->parseString($html, $config, 'utf8');
                $tidy->cleanRepair();
                $html = $tidy->value;
            } else {
                require_once Kwf_Config::getValue('externLibraryPath.htmLawed').'/htmLawed.php';
                $html = htmLawed($html);
            }
            if (!$parser) {
                $parser = new Kwc_Basic_Text_Parser($this->componentId, $this->getModel());
                $parser->setMasterStyles(Kwc_Basic_Text_StylesModel::getMasterStyles());
            }
            $parser->setEnableColor(Kwc_Abstract::getSetting($this->_componentClass, 'enableColors'));
            $parser->setEnableTagsWhitelist(Kwc_Abstract::getSetting($this->_componentClass, 'enableTagsWhitelist'));
            $parser->setEnableStyles(Kwc_Abstract::getSetting($this->_componentClass, 'enableStyles'));
            $html = $parser->parse($html);
            if (class_exists('tidy')) {
                $tidy->parseString($html, $config, 'utf8');
                $tidy->cleanRepair();
                $html = $tidy->value;
            } else {
                require_once Kwf_Config::getValue('externLibraryPath.htmLawed').'/htmLawed.php';
                $html = htmLawed($html);
            }
            $html = str_replace('class="_mce_type-bookmark"', '_mce_type="bookmark"', $html);
            $html = str_replace('#nbsp#', '&nbsp;', $html);
        }



        $classes = $this->_classes;

        $newContent = '';
        foreach ($this->getContentParts($html) as $part) {
            if (is_string($part)) {
                $newContent .= $part;
            } else if ($part['type'] == 'invalidImage') {
                if (isset($part['componentId'])
                    && class_exists($part['componentClass'])
                    && (strtolower($part['componentClass']) == 'kwc_basic_image_component'
                        || is_subclass_of($part['componentClass'], 'Kwc_Basic_Image_Component'))) {

                    $srcRow = Kwc_Abstract::createModel($part['componentClass'])
                                    ->getRow($part['componentId']);
                    if ($srcRow->imageExists()) {
                        $destRow = Kwc_Abstract::createModel($classes['image'])
                                                ->createRow($srcRow->toArray());
                        $childComponentRow = $this->addChildComponentRow('image', $destRow);
                        $destRow->save();
                        $imageComponent = Kwf_Component_Data_Root::getInstance()
                            ->getComponentByDbId($this->component_id.'-i'.$childComponentRow->nr)
                            ->getComponent();
                        $dimension = $imageComponent->getImageDimensions();
                        $newContent .= "<img src=\"".$imageComponent->getImageUrl()."\" ".
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

                $destFileRow = Kwc_Abstract::createModel($classes['image'])
                            ->getReferencedModel('Image')
                            ->createRow();

                $path = explode('?', $part['src']);
                if (preg_match('#([^/]*)\\.[a-z]+$#U', $path[0], $m)) {
                    $srcFileName = Zend_Filter::filterStatic($m[1], 'Alnum', array(ENT_QUOTES));
                }
                if (!isset($srcFileName) || !$srcFileName) {
                    $srcFileName = 'download';
                }

                $destFileRow->writeFile($response->getBody(), $srcFileName, $extension, $contentType);

                $destRow = Kwc_Abstract::createModel($classes['image'])->createRow();
                $destRow->kwf_upload_id = $destFileRow->id;
                $size = getimagesize($destFileRow->getFileSource());
                $destRow->width = $size[0];
                $destRow->height = $size[1];
                $destRow->filename = $srcFileName;
                $destRow->scale = '';
                $childComponentRow = $this->addChildComponentRow('image', $destRow);
                $destRow->save();
                $imageComponent = Kwf_Component_Data_Root::getInstance()
                    ->getComponentByDbId($this->component_id.'-i'.$childComponentRow->nr)
                    ->getComponent();
                $dimension = $imageComponent->getImageDimensions();
                $newContent .= "<img src=\"".$imageComponent->getImageUrl()."\" ".
                            "width=\"$dimension[width]\" ".
                            "height=\"$dimension[height]\" />";

            } else if ($part['type'] == 'invalidLink') {

                $model = Kwc_Abstract::createModel($classes['link']);
                $destRow = $this->_getChildComponentRow('link', $model);
                if (isset($part['componentId'])) {
                    try {
                        $srcRow = $model->getRow($part['componentId']);
                    } catch (Kwf_Exception $e) {
                        $srcRow = false;
                    }
                    if (is_instance_of($classes['link'], 'Kwc_Basic_LinkTag_Component')) {
                        $linkClasses = Kwc_Abstract::getChildComponentClasses($classes['link'], 'child');
                        if ($srcRow && class_exists($linkClasses[$srcRow->component])) {
                            $linkModel = Kwc_Abstract::createModel($linkClasses[$srcRow->component]);
                            $srcLinkRow = $linkModel->getRow($part['componentId'].'-child');
                            if ($srcLinkRow) {
                                $destRow->component = $srcRow->component;
                                $destRow->save();
                                $destLinkRow = $linkModel->getRow($destRow->component_id.'-child');
                                if (!$destLinkRow) {
                                    $destLinkRow = $linkModel->createRow();
                                    $destLinkRow->component_id = $destRow->component_id.'-child';
                                }
                                foreach ($srcLinkRow->toArray() as $k=>$i) {
                                    if ($k != 'component_id') {
                                        $destLinkRow->$k = $i;
                                    }
                                }
                                $destLinkRow->save();
                                $newContent .= "<a href=\"{$destRow->component_id}\">";
                                continue;
                            }
                        }
                    } else if (is_instance_of($classes['link'], 'Kwc_Basic_LinkTag_Abstract_Component')) {
                        if ($srcRow) {
                            foreach ($srcRow->toArray() as $k=>$i) {
                                if ($k != 'component_id') {
                                    $destRow->$k = $i;
                                }
                            }
                            $destRow->save();
                            $newContent .= "<a href=\"{$destRow->component_id}\">";
                            continue;
                        }
                    } else {
                        //Kein link möglich
                        continue;
                    }
                }
                if (!$destRow) {
                    $destRow = $model->createRow();
                    $this->addChildComponentRow('link', $destRow);
                }
                if (is_instance_of($classes['link'], 'Kwc_Basic_LinkTag_Component')) {
                    $linkClasses = Kwc_Abstract::getChildComponentClasses($classes['link'], 'child');

                    $destRow->component = null;
                    if (preg_match('#^mailto:#', $part['href'], $m)) {
                        if (isset($linkClasses['mail']) && $linkClasses['mail']) {
                            $destRow->component = 'mail';
                        }
                    } else {
                        if (isset($linkClasses['intern']) && $linkClasses['intern']) {
                            $url = $part['href'];
                            $parsedUrl = parse_url($url);
                            if (!isset($parsedUrl['host'])) {
                                if (isset($_SERVER['HTTP_HOST'])) {
                                    $url = 'http://'.$_SERVER['HTTP_HOST'].$url;
                                } else {
                                    $url = 'http://'.Kwf_Registry::get('config')->server->domain.$url;
                                }
                            }
                            $internLinkPage = Kwf_Component_Data_Root::getInstance()
                                ->getPageByUrl($url, null);
                            if ($internLinkPage) {
                                $destRow->component = 'intern';
                            }
                        }
                        if (!$destRow->component && isset($linkClasses['extern']) && $linkClasses['extern']) {
                            $destRow->component = 'extern';
                        }
                    }
                    if (!$destRow->component) continue; //kein solcher-link möglich
                    $destRow->save();

                    $destClasses =  Kwc_Abstract::getChildComponentClasses($classes['link'], 'child');

                    $row = Kwc_Abstract::createModel($destClasses[$destRow->component])
                                ->getRow($destRow->component_id.'-child');
                    if (!$row) $row = Kwc_Abstract::createModel($destClasses[$destRow->component])
                                                ->createRow();
                    $row->component_id = $destRow->component_id.'-child';
                    if ($destRow->component == 'extern') {
                        $row->target = $part['href'];
                    } else if ($destRow->component == 'intern') {
                        $row->target = $internLinkPage->dbId;
                    } else {
                        preg_match('#^mailto:(.*)\\??(.*)#', $part['href'], $m);
                        $row->mail = $m[1];
                        $m = parse_str($m[2]);
                        $row->subject = isset($m['subject']) ? $m['subject'] : '';
                        $row->text = isset($m['body']) ? $m['body'] : '';
                    }
                    $row->save();
                } else if (is_instance_of($classes['link'], 'Kwc_Basic_LinkTag_Extern_Component')) {
                    $destRow->target = $part['href'];
                    $destRow->save();
                } else {
                    //Kein link möglich
                    continue;
                }

                $newContent .= "<a href=\"{$destRow->component_id}\">";

            } else if ($part['type'] == 'invalidDownload') {

                $srcRow = Kwc_Abstract::createModel($classes['download'])
                                ->getRow($part['componentId']);
                if ($srcRow->fileExists()) {
                    $destRow = Kwc_Abstract::createModel($classes['download'])
                                                ->createRow($srcRow->toArray());
                    $this->addChildComponentRow('download', $destRow);
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

    //muss aufgerufen werden wenn eine unterkomponente hinzugefügt wird
    //im Controller + in der row
    public function addChildComponentRow($type, $childComponentRow = null)
    {
        $r = $this->createChildRow('ChildComponents');
        $r->component = $type;
        $r->nr = $this->getMaxChildComponentNr($type)+1;
        $r->saved = 0;
        $r->save();

        if ($childComponentRow) {
            $childComponentRow->component_id = $this->component_id.'-'.substr($type, 0, 1).$r->nr;
        }
        return $r;
    }

    private function _getChildComponentRow($type, $model)
    {
        $r = $this->addChildComponentRow($type);

        $componentId = $this->component_id.'-'.substr($type, 0, 1).$r->nr;
        $ret = $model->getRow($componentId);
        if (!$ret) {
            $ret = $model->createRow();
            $ret->component_id = $componentId;
        }
        return $ret;
    }
}
