<?php
class Vps_Util_FileIcon
{
    public static function getFileIcon($extension)
    {
        switch ($extension) {
            case 'pdf':
                $icon = 'page_white_acrobat';
            case 'doc':
            case 'docx':
                $icon = 'page_white_word';
            case 'xls':
            case 'xlsx':
                $icon = 'page_white_excel';
            case 'ppt':
            case 'pptx':
                $icon = 'page_white_powerpoint';
            case 'zip':
            case 'rar':
            case 'gz':
            case 'bz2':
                $icon = 'page_white_compressed';
            case 'exe':
                $icon = 'page_white_gear';
            case 'jpg':
            case 'gif':
            case 'png':
            case 'psd':
                $icon = 'page_white_picture';
            default:
                $icon = 'page_white_get';
        }
        return new Vps_Asset($icon);
    }
}
