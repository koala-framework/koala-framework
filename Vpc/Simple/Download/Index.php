<?php
class Vpc_Simple_Download_Index extends Vpc_Abstract
{
    protected $_tablename = 'Vpc_Simple_Download_IndexModel';
    const NAME = 'Standard.Download';

    protected $_settings = array('path' => '',
                 'text' => '',
                 'info' => '',
                 'filesize' => '',
                 'filesizeshow' => 1,
                 'infoshow' => 1,
                 'iconshow' => 1,
                 'downloadshow' => 1,
                 'icon' => '/files/download.gif'
                 );

    protected $_extensions  = array (  'pdf' => '/files/icons/acrobat.png',
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

  /*public static function getStaticSettings()
    {
        $config = array();
        $config['filesizeshow'] = 1;
        $config['infoshow'] = 1;
        $config['iconshow'] = 1;
        $config['downloadshow'] = 1;
        $config['icon'] = '/files/download.gif';
        return $config;
    }*/

  public function getTemplateVars()
    {
      $return['filesizeshow'] = $this->getSetting('filesizeshow');
      $return['iconshow'] = $this->getSetting('iconshow');
      $return['downloadshow'] = $this->getSetting('downloadshow');
      $return['infoshow'] = $this->getSetting('infoshow');
    if (file_exists('./public'.$this->getSetting('path')))
          $return['filesize'] = round((filesize('./public'.$this->getSetting('path')) /1024), 2);
    else
      $return['filesize'] = '-';
      $return['icon'] = $this->getIcon($this->getSetting('path'));
      $return['info'] = $this->getSetting('info');
      $return['downloadicon'] = $this->getSetting('icon');
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

