<?php
class Vps_Trl_Parser
{
    private $_modelVps;
    private $_modelWeb;
    private $_mode;
    private $_languages = array();
    private $_debug = false;

    public function __construct($modelVps, $modelWeb, $mode = 'all')
    {
        $this->_modelVps = $modelVps;
        $this->_modelWeb = $modelWeb;
        $this->_mode = $mode;
        $this->_languages = Zend_Registry::get('trl')->getLanguages();
        $this->_codeLanguage = Zend_Registry::get('trl')->getWebCodeLanguage();
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

    public function parse()
    {
        //das Project
        $directoryWeb = ".";
        $directoryVps = VPS_PATH;
        $directories = array('web' => $directoryWeb, 'vps' => $directoryVps);

        $errors = array();
        $files = 0;
        $phpfiles = 0;
        $jsfiles = 0;
        $tplfiles = 0;
        foreach ($directories as $directory){
            $iterator = new RecursiveDirectoryIterator($directory);
            foreach(new RecursiveIteratorIterator($iterator) as $file)
            {
                if(!$file->isDir()) {
                    if (!stripos($file->getPathname(), "/vps/tests/Vps/Trl/") && !stripos($file->getPathname(), "vps/Vps/Trl.php")) { //tests werden ausgeschlossen
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
                          if ($this->_debug) {
                              echo 'currentfile: '.$file->getPathname()."\n";
                          } else {
                              echo '.';
                          }
                          $ret = Zend_Registry::get('trl')->parse(file_get_contents($file), $extension);
                          if ($ret){
                              set_time_limit(60);
                              $errors = array_merge($errors, $this->insertToXml($ret, $file->getPathname()));
                          }
                      }
                    }
                }
            }
        }
        $ret = array();
        $ret ['files'] = $files;
        $ret ['phpfiles'] = $phpfiles;
        $ret ['jsfiles'] = $jsfiles;
        $ret ['tplfiles'] = $tplfiles;
        $ret ['errors'] = $errors;
        return $ret;
    }

    public function insertToXml ($entries, $path)
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
                      $content[$this->_getDefaultLanguage($xmlsource)] = $entry['text'];
                      if (isset($entry['plural'])) {
                          $content[$this->_getDefaultLanguage($xmlsource).'_plural'] = $entry['plural'];
                      }
                      foreach ($this->_languages as $lang) {
                          if ($lang != $this->_getDefaultLanguage($xmlsource)) {
                              $content[$lang]  = '_';
                              if (isset($entry['plural'])) {
                                  $content[$lang.'_plural'] = '_';
                              }
                          }
                      }
                      if (isset($entry['context'])) {
                          $content['context'] = $entry['context'];
                      }
                      set_time_limit(20);
                      $row = $xmlModel->createRow($content);
                      $row->save();

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
        $rowscount = $model->getRows($select)->count();
        if ($rowscount) return false;

        return true;
    }


    private function _getDefaultLanguage($type)
    {
        if ($type == Vps_Trl::SOURCE_VPS) {
            return 'en';
        } else {
            return Zend_Registry::get('trl')->getWebCodeLanguage();
        }
    }

}