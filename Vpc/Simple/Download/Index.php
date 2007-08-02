<?php
class Vpc_Simple_Download_Index extends Vpc_Abstract
{

    protected $_defaultSettings = array('path' => '', 'text' => '', 'info' => '', 'filesize' => '');
    protected $_extensions  = array ('pdf' => '/files/icons/acrobat.png',
                       'doc' => '/files/icons/word.png',
                       'mp3' => '/files/icons/music.png',
                       'xls' => '/files/icons/excel.png',
                       'png' => '/files/icons/picture.png',
                       'jpg' => '/files/icons/picture.png',
                       'tif' => '/files/icons/picture.png',
                       'gif' => '/files/icons/picture.png',
                       'ppt' => '/files/icons/powerpoint.png',
                       'default' => '/files/icons/page.png'
                       );

  public static function getStaticSettings()
    {
        $config = array();
        $config['filesizeshow'] = 1;
        $config['infoshow'] = 1;
        $config['iconshow'] = 1;
        $config['downloadshow'] = 1;
        $config['icon'] = '/files/download.gif';
        return $config;
    }

  public function getTemplateVars()
    {
      $return['filesizeshow'] = $this->getStaticSetting('filesizeshow');
      $return['iconshow'] = $this->getStaticSetting('iconshow');
      $return['downloadshow'] = $this->getStaticSetting('downloadshow');
      $return['infoshow'] = $this->getStaticSetting('infoshow');
    if (file_exists('./public'.$this->getSetting('path')))
          $return['filesize'] = round((filesize('./public'.$this->getSetting('path')) /1024), 2);
    else
      $return['filesize'] = '-';
      $return['icon'] = $this->getIcon($this->getSetting('path'));
      $return['info'] = $this->getSetting('info');
      $return['downloadicon'] = $this->getStaticSetting('icon');
        $return['path'] = $this->getSetting('path');
        $return['text'] = $this->getSetting('text');
        $return['template'] = 'Simple/Download.html';
        return $return;
    }


    private function getIcon ($file){
      $start = strripos($file, '.');
      $fileextension = substr($file, $start+1);

      if (!in_array($fileextension, array_keys($this->_extensions)))
      $fileextension = 'default';

      return $this->_extensions[$fileextension];

    }

}

