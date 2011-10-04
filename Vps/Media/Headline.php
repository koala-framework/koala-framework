<?php
class Vps_Media_Headline
{
    public static function getHeadlineStyles($contents)
    {
        $ret = array();
        $contents = preg_replace('#/\\*.*?\\*/#', '', $contents);
        preg_match_all('#\s*([^{}]*)\s*{([^}]*)\s*-vps-headline\s*:\s*graphic\s*;([^}]*)}#', $contents, $m);
        foreach (array_keys($m[0]) as $i) {
            $selector = trim($m[1][$i]);
            $style[$selector] = array();
            preg_match_all('#([^:;]+):([^;]+);#', $m[2][$i].' '.$m[3][$i], $ms);
            foreach (array_keys($ms[0]) as $j) {
                $s = trim($ms[1][$j]);
                if (substr($s, 0, 5) == '-vps-') {
                    $s = substr($s, 5);
                }
                $value = trim($ms[2][$j]);
                if (substr($value, 0, 1) == "'" && substr($value, -1) == "'") {
                    $value = substr($value, 1, -1);
                }
                $ret[$selector][$s] = $value;
            }
        }
        return $ret;
    }

    public static function outputHeadline($selector, $text, $assetsType)
    {
        if(strlen($text)>200) $text = substr($text, 0, 200)."...";

        $loader = new Vps_Assets_Loader();
        $dep = $loader->getDependencies();
        $language = Vps_Trl::getInstance()->getTargetLanguage();
        $cacheId = 'headline'.md5($selector.$text);
        $cache = new Vps_Assets_Cache();
        $cacheData = $cache->load($cacheId);
        if ($cacheData && filemtime($cacheData['file']) > $cacheData['mtime']) {
            $cacheData = false;
        }
        $cacheData = false;
        if (!$cacheData) {
            $s = false;
            $styles = false;
            foreach ($dep->getAssetFiles($assetsType, 'css', 'web', Vps_Component_Data_Root::getComponentClass()) as $file) {
                if (!(substr($file, 0, 8) == 'dynamic/' || substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://' || substr($file, 0, 1) == '/')) {
                    $c = $loader->getFileContents($file, $language);
                    foreach (self::getHeadlineStyles($c['contents']) as $s => $styles) {
                        if ($s == $selector) {
                            break;
                        }
                    }
                }
                if ($s == $selector) break;
            }
            if ($s != $selector) {
                throw new Vps_Exception("Unknown selector: '$selector'");
            }
            $contents = self::_generateHeadline($styles, $text);
            $file = $dep->getAssetPath($file);
            $cacheData = array(
                'contents' => $contents,
                'etag' => md5($contents),
                'mimeType' => 'image/png',
                'mtime' => filemtime($file),
                'file' => $file
            );
            $cache->save($cacheData, $cacheId);
        }
        unset($cacheData['file']);
        Vps_Media_Output::output($cacheData);
    }

    /**
     * Unterstützte CSS-Eigenschaften:
     * height, width, padding-top, padding-bottom, padding-left, padding-right
     * font-size, font-file, color, background-color, line-height
     */
    private static function _generateHeadline($styles, $text)
    {
        $text = str_replace(chr(0xE2).chr(0x80).chr(0x93), '-', $text); //langen bindestrich durch normalen ersetzen
        $text = str_replace(array("\r", "\n", "<br>", "<br/>"), array("", "", "<br />", "<br />"), $text);
        $text = explode("<br />", $text);
        if (!isset($styles['font-file'])) {
            throw new Vps_Exception("missing style font-file");
        }
        $fontFile = $styles['font-file'];
        if (file_exists($fontFile)) {
        } else if (file_exists('fonts/'.$fontFile)) {
            $fontFile = 'fonts/'.$fontFile;
        } else if (file_exists(VPS_PATH.'/'.$fontFile)) {
            $fontFile = VPS_PATH.'/'.$fontFile;
        } else {
            throw new Vps_Exception("invalid font: '$fontFile'");
        }

        $width = isset($styles['width']) ? $styles['width'] : false;
        if ($width && !preg_match('/^[0-9]+px$/', $width)) throw new Vps_Exception("Invalid width: '$width'");
        if ($width) $width = (int)substr($width, 0, -2);

        $height = isset($styles['height']) ? $styles['height'] : false;
        if ($height && !preg_match('/^[0-9]+px$/', $height)) throw new Vps_Exception("Invalid height: '$height'");
        if ($height) $height = (int)substr($height, 0, -2);

        $fontSize = isset($styles['font-size']) ? $styles['font-size'] : 12;
        $paddingTop = isset($styles['padding-top']) ? $styles['padding-top'] : 0;
        $paddingLeft = isset($styles['padding-left']) ? $styles['padding-left'] : 0;
        $paddingBottom = isset($styles['padding-bottom']) ? $styles['padding-bottom'] : 0;
        $paddingRight = isset($styles['padding-right']) ? $styles['padding-right'] : 0;
        $lineHeight = isset($styles['line-height']) ? $styles['line-height'] : $fontSize;
        

        $backgroundColor = isset($styles['background-color']) ? $styles['background-color'] : false;
        if ($backgroundColor && !preg_match('/^#[0-9a-fA-F]{6}$/', $backgroundColor)) throw new Vps_Exception("Invalid background-color: '$backgroundColor'");

        $color = isset($styles['color']) ? $styles['color'] : '#000000';
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) throw new Vps_Exception("Invalid color: '$color'");

        $factor = 1;
        $steps = false;
        $antializing = true;

        //0  lower left corner, X position  =0
        //1  lower left corner, Y position  =1
        //2  lower right corner, X position
        //3  lower right corner, Y position
        //4  upper right corner, X position = 2
        //5  upper right corner, Y position = 3
        //6  upper left corner, X position
        //7  upper left corner, Y position
        $bbox = imagettfbbox($fontSize*$factor, 0, $fontFile, $text[0]);
        if (!$width) {
            $width = 0;
            foreach ($text as $t) {
                $bbox = imagettfbbox($fontSize*$factor, 0, $fontFile, $t);
                $width = max($width, abs($bbox[4] - $bbox[0])/$factor);
            }
            $width += 2; //+2 damit platz für antializing-pixel sind
        }
        $width += $paddingRight;
        $width += $paddingLeft;
        if (!$width) throw new Vps_Exception("Width can't be 0");
        $fontX = $paddingLeft*$factor - $bbox[0];
        //für die berechnung der höhe immer 'gÜ' verwenden - das gibt und die maximale höhe die zu erwarten ist
        //TODO: funktioniert nicht für mehrzeilige texte!
        $bbox = imagettfbbox($fontSize*$factor, 0, $fontFile, 'gÜ');
        if (!$height) {
            $height = (abs($bbox[3] - $bbox[7])/$factor) * count($text);
        }
        $height += $paddingTop;
        $height += $paddingBottom;
        if (!$height) throw new Vps_Exception("Height can't be 0");
        $fontY = $paddingTop*$factor - $bbox[5] /*- $bbox[1]*/;

        $im1 = imagecreatetruecolor ($width*$factor, $height*$factor) or die ("Error");
        imagealphablending($im1, true);
        imageSaveAlpha($im1, true);
        if ($backgroundColor) {
            $bgColor = ImageColorAllocate ($im1, hexdec(substr($backgroundColor,1,2)), hexdec(substr($backgroundColor,3,2)), hexdec(substr($backgroundColor,5,2)));
        } else {
            if (PHP_VERSION == '5.2.0-8+etch16') {
                $bgColor = imagecolorallocatealpha($im1, 255, 255, 255, 0);
            } else {
                $bgColor = imagecolorallocatealpha($im1, 255, 255, 255, 127);
            }
        }
        imageFill($im1, 0, 0, $bgColor);
        $textColorAllocated = ImageColorAllocate ($im1, hexdec(substr($color,1,2)), hexdec(substr($color,3,2)), hexdec(substr($color,5,2)));

        $textY = $fontY;
        //zeile für zeile schreiben
        foreach($text as $t) {
            $t = trim($t);
            $c = $textColorAllocated;
            if(!$antializing) $c = -$c; //negative farbe schaltet antializing aus
            imagettftext($im1, $fontSize*$factor, 0, $fontX, $textY, $c, $fontFile, $t);
            $pos = 0;
            $textY += $lineHeight*$factor;
        }
        if($factor==1) {
            $outputImage = $im1;
        } else if ($steps) {
            $curFactor = $factor;
            while($curFactor >= 2) {
                $im2 = imagecreatetruecolor ($width*$curFactor/2, $height*$curFactor/2) or die ("Error");
                imagecopyresampled ($im2, $im1, 0, 0, 0, 0, $width*$curFactor/2, $height*$curFactor/2, $width*$curFactor, $height*$curFactor);
                imagedestroy($im1);
                $im1 = $im2;
                $curFactor = $curFactor / 2;
            }
            $outputImage = $im2;
        } else {
            $im2 = imagecreatetruecolor ($width, $height) or die ("Error");
            imagecopyresampled ($im2, $im1, 0, 0, 0, 0, $width, $height, $width*$factor, $height*$factor);
            imagedestroy($im1);
            $outputImage = $im2;
        }
        ob_start();
        imagepng($outputImage);
        $contents = ob_get_contents();
        ob_end_clean();
        imagedestroy($outputImage);
        return $contents;
    }
}
