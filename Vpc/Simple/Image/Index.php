<?php
class Vpc_Simple_Image_Index extends Vpc_Abstract
{
    protected $_tablename = 'Vpc_Simple_Image_IndexModel';
    const NAME = 'Standard.Image';

    protected $_settings = array(
        'typesAllowed' 	    => 'jpg, gif, png',
        'directory'   	    => 'SimpleImage/',
        'filesize'	   	    => 'free',
        'default_style'		=> 'crop',
        'enableName' 		=> 1,
        'style' 	        => '',
        'allow'		        => array('crop', 'scale', 'scale_bg', 'deform'), //keywords: crop, scale, scale_bg, deform
        'default_color'		=> 'black',
        'allow_color'		=> 1,
        'color'				=> '',
    );


    public function getTemplateVars()
    {
        $newFilename = $this->_getTable()->find($this->getDbId(), $this->getComponentKey())->current()->file_name;
        $pw = 'jupidu';
        $filename = 'pic';
        if ($newFilename != '') $filename = $newFilename;
    
        $path = '/media/' . $this->getId() . '/' . MD5($pw . $this->getId()) . '/'.$filename.'.'.$this->_getExtension();
    
    
        $return['path']		= $path;
        $return['template'] = 'Simple/Image.html';
        return $return;
    }
    
    private function _getExtensions()
    {
        $extensionsString = $this->getSetting('typesAllowed');
        $extenstions = array ();
        $delims = ',';
        $word = strtok($extensionsString, $delims);
        while (is_string($word)) {
          if ($word) {
            $extensions[] = trim($word);
          }
          $word = strtok($delims);
        }
        return $extensions;
    }
    
    //liefert die Extension des files
    private function _getExtension()
    {
        $extensions = $this->_getExtensions();
        foreach ($extensions as $data) {
          $filename = $this->getId() . '.' . $data;
          if (file_exists('./public/media/' . $filename)) {
            return $data;
          }
        }
    }

}