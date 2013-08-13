<?php
class Kwf_Assets_Dependency_File_Css extends Kwf_Assets_Dependency_File
{
    public function getMimeType()
    {
        return 'text/css';
    }
    public function getContents($language)
    {
        $ret = parent::getContents($language);

        $pathType = substr($this->_fileName, 0, strpos($this->_fileName, '/'));

        if ($pathType == 'ext') {
            //hack um bei ext-css-dateien korrekte pfade für die bilder zu haben
            $ret = str_replace('../images/', '/assets/ext/resources/images/', $ret);
        } else if ($pathType == 'mediaelement') {
            //hack to get the correct paths for the mediaelement pictures
            $ret = str_replace('url(', 'url(/assets/mediaelement/build/', $ret);
        }
        return $ret;
    }
}
