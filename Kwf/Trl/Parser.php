<?php
class Vps_Trl_Parser
{
    private $_modelVps;
    private $_modelWeb;
    private $_mode;
    private $_usedIds = array();
    private $_languages = array();
    private $_debug = false;
    private $_added = array();
    private $_deletedRows = array();
    private $_cleanUp;
    private $_warnings = array();

    public function __construct($modelVps, $modelWeb, $mode = 'all', $cleanUp = 'none')
    {
        $this->_modelVps = $modelVps;
        $this->_modelWeb = $modelWeb;

        $this->_cleanUp = $cleanUp;

        $this->_added[get_class($modelVps)] = array();
        $this->_added[get_class($modelWeb)] = array();

        $this->_deletedRows[get_class($modelVps)] = array();
        $this->_deletedRows[get_class($modelWeb)] = array();

        $this->_usedIds[get_class($modelVps)] = array();
        $this->_usedIds[get_class($modelWeb)] = array();

        $this->_mode = $mode;
        $this->_languages = Vps_Trl::getInstance()->getLanguages();
        $this->_codeLanguage = Vps_Trl::getInstance()->getWebCodeLanguage();
    }

    public function setDebug($debug)
    {
        $this->_debug = (bool)$debug;
    }

    public function setLanguages($languages) //notwendig zum testen
    {
        $this->_languages = $languages;
    }

    public function setCodeLanguage($language) //notwendig zum testen
    {
        $this->_codeLanguage = $language;
    }

    public function parse($directories = null, $quiet = false)
    {
        //das Project
        if (!$directories) {
            $directoryWeb = ".";
            $directoryVps = VPS_PATH;
            $directories = array('web' => $directoryWeb, 'vps' => $directoryVps);
        }

        $errors = array();
        $files = 0;
        $phpfiles = 0;
        $jsfiles = 0;
        $tplfiles = 0;
        $this->_usedIds = array();
        $this->_usedIds[get_class($this->_modelVps)] = array();
        $this->_usedIds[get_class($this->_modelWeb)] = array();
        foreach ($directories as $dirKey => $directory){
            $iterator = new RecursiveDirectoryIterator($directory);
            foreach(new RecursiveIteratorIterator($iterator) as $file)
            {
                if(!$file->isDir()) {
                    if (stripos($file->getPathname(), ".svn")) continue;
                    if (stripos($file->getPathname(), ".git")) continue;
                    if ((
                            stripos($file->getPathname(), VPS_PATH . "/tests/Vps/Trl/") === false &&
                            stripos($file->getPathname(), VPS_PATH . "/Vps/Trl.php") === false
                        ) ||
                        stripos($file->getPathname(), "testparse")) { //tests werden ausgeschlossen
                        $extension = end(explode('.', $file->getFileName()));
                      if($extension=='php' || $extension =='js' || $extension =='tpl') {
                          switch ($extension) {
                              case 'php': $phpfiles++; break;
                              case 'js' : $jsfiles++; break;
                              case 'tpl': $tplfiles++; break;
                          }
                          $files++;
                          //nach trl aufrufen suchen
                          $ret = array();
                          if (!$quiet) {
                              if ($this->_debug) {
                                  echo 'currentfile: '.$file->getPathname()."\n";
                              } else {
                                  echo '.';
                              }
                          }
                          $ret = Vps_Trl::getInstance()->parse(file_get_contents($file), $extension);
                          if ($ret){
                              $errors = array_merge($errors, $this->insertToXml($ret, $file->getPathname(), $dirKey));
                          }
                      }
                    }
                }
            }
        }

        $this->_cleanUp($quiet);
        $ret = array();
        $ret ['files'] = $files;
        $ret ['phpfiles'] = $phpfiles;
        $ret ['jsfiles'] = $jsfiles;
        $ret ['tplfiles'] = $tplfiles;
        $ret ['errors'] = $errors;
        $ret ['added'] = $this->_added;
        if ($this->_cleanUp != 'none')
            $ret ['deleted'] = $this->_deletedRows;
        else
            $ret ['deleted'] = false;
        $ret ['warnings'] = $this->_warnings;
        return $ret;
    }

    public function insertToXml ($entries, $path, $dirKey)
    {
        $errors = array();
        foreach ($entries as $key => $entry){
            if (isset($entry['error'])) {
                $entry['path'] = $path;
                $errors[] = $entry;
            } else {
                $xmlsource = $entry['source'];
                $xmlModel = $this->_getModel($xmlsource);
                $content = array();

                if ($this->_mode == $xmlsource || ($this->_mode == 'all' && $entry)){

                    if ($this->_checkNotExists($entry, $xmlsource)) {
                        //Warnings check

                        if ($entry['source'] != $dirKey) {

                            $this->_warnings[] = array('dir' => $dirKey, 'before' => $entry['before'],
                                                'path' => $path, 'linenr' => $entry['linenr']);
                        } else {
                            $this->_added[get_class($xmlModel)][] = $entry;
                            $content[$this->_getDefaultLanguage($xmlsource)] = $entry['text'];
                            if (isset($entry['plural'])) {
                              $content[$this->_getDefaultLanguage($xmlsource).'_plural'] = $entry['plural'];
                            }
                           /* foreach ($this->_languages as $lang) {
                              if ($lang != $this->_getDefaultLanguage($xmlsource)) {
                                  $content[$lang]  = '_';
                                  if (isset($entry['plural'])) {
                                      $content[$lang.'_plural'] = '_';
                                  }
                              }
                            }*/
                            if (isset($entry['context'])) {
                              $content['context'] = $entry['context'];
                            }
                            $row = $xmlModel->createRow($content);


                            $this->_usedIds[get_class($xmlModel)][$row->save()] = 1;
                        }

                    }
                }
            }
        }
        return $errors;
    }

    private function _getModel($xmlSource)
    {
        if ($xmlSource == 'web') {
            return $this->_modelWeb;
        } else {
            return $this->_modelVps;
        }
    }

    protected function _checkNotExists($entry, $xmlsource)
    {
        $model = $this->_getModel($xmlsource);
        $select = $model->select();
        $select->whereEquals($this->_getDefaultLanguage($xmlsource), $entry['text']);
        if (isset($entry['context'])) $select->whereEquals('context', $entry['context']);
        else $select->whereNull('context');
        $rows = $model->getRows($select);
        $rowscount = $rows->count();

        if ($rowscount) {
            $row = $rows->current();
            $this->_usedIds[get_class($model)][$row->id] = 1;
            return false;
        }


        return true;
    }


    private function _getDefaultLanguage($type)
    {
        if ($type == Vps_Trl::SOURCE_VPS) {
            return 'en';
        } else {
            return Vps_Trl::getInstance()->getWebCodeLanguage();
        }
    }

    private function _cleanUp ($quiet)
    {
        $toDeleteRows = array();
        if ($this->_cleanUp == "all" || $this->_cleanUp == "vps") {
            $rows = $this->_modelVps->getRows();
            foreach ($rows as $row) {
                if (!array_key_exists($row->id, $this->_usedIds[get_class($this->_modelVps)])) {
                    $this->_deletedRows[get_class($this->_modelVps)][] = $row->en;
                    $toDeleteRows[] = $row;
                }
            }
        }
        if ($this->_cleanUp == "all" || $this->_cleanUp == "web") {
            $rows = $this->_modelWeb->getRows();
            foreach ($rows as $row) {
                if (!array_key_exists($row->id, $this->_usedIds[get_class($this->_modelWeb)])) {
                    $this->_deletedRows[get_class($this->_modelWeb)][] = $row->{$this->_codeLanguage};
                    $toDeleteRows[] = $row;
                }
            }
        }
        foreach ($toDeleteRows as $r) {
            if (!$quiet) echo "-$r->id-";
            $r->delete();
        }
    }

}
