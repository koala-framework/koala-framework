<?php
class Kwf_View_Helper_FileSize
{
    public function fileSize($filesize)
    {
        if (!is_int($filesize) && file_exists($filesize)) {
            $filesize = filesize($filesize);
        }
    
        $shortcuts = array("Bytes", "KB", "MB", "GB", "TB", "PB");
    
        $i = 0;
        while ($filesize > 1024 && isset($shortcuts[$i+1])) {
            $filesize = $filesize / 1024;
            $i++;
        }
    
        if ($filesize < 10) {
            $ret = number_format($filesize, 1, ",", ".");
        } else {
            $ret = number_format($filesize, 0, ",", ".");
        }
    
        $ret .= ' '.$shortcuts[$i];
        return Kwf_Util_HtmlSpecialChars::filter($ret);
    }
}
