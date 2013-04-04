<?php
require_once Kwf_Config::getValue('externLibraryPath.sass') . '/Extensions/Compass/Compass.php';
class CompassSass extends Compass
{
    public static function compassUrl($path, $only_path = false, $web_path = true)
    {
        if ($path == 'PIE.htc') return;
        if (substr($path, 0, strlen('/assets/')) == '/assets/') {
            if ($only_path) {
                return new SassString($path);
            }
            return new SassString("url('$path')");
        } else {
            return parent::compassUrl($path, $only_path, $web_path);
        }
    }

    public static function compassStylesheetUrl($path, $only_path = false)
    {
        return self::compassUrl($path, $only_path);
    }

    public static function compassFontUrl($path, $only_path = false)
    {
        return self::compassUrl($path, $only_path);
    }

    public static function compassImageUrl($path, $only_path = false)
    {
        return self::compassUrl($path, $only_path);
    }

    public static function compassInlineImage($file, $mime = null)
    {
        if ($path = self::compassUrl($file, true, false)) {
            $info = getimagesize($path);
            $mime = $info['mime'];
            $data = base64_encode(file_get_contents($path));
            # todo - do not return encoded if file size > 32kb
            return new SassString("url('data:$mime;base64,$data')");
        }
        return new SassString('');
    }
}
