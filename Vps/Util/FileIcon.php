<?php
class Vps_Util_FileIcon
{
    public static function getFileIcon($extension)
    {
        switch ($extension) {
            case 'pdf':
                $icon = 'page_white_acrobat';
                break;
            case 'doc':
            case 'docx':
                $icon = 'page_white_word';
                break;
            case 'xls':
            case 'xlsx':
                $icon = 'page_white_excel';
                break;
            case 'ppt':
            case 'pptx':
                $icon = 'page_white_powerpoint';
                break;
            case 'zip':
            case 'rar':
            case 'gz':
            case 'bz2':
                $icon = 'page_white_compressed';
                break;
            case 'exe':
                $icon = 'page_white_gear';
                break;
            case 'jpg':
            case 'gif':
            case 'png':
            case 'psd':
                $icon = 'page_white_picture';
                break;
            default:
                $icon = 'page_white_get';
                break;
        }
        return new Vps_Asset($icon);
    }
}
