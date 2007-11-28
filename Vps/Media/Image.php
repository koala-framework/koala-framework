<?p
class Vps_Media_Ima

    const SCALE_BESTFIT = 'bestfit
    const SCALE_CROP = 'crop
    const SCALE_DEFORM = 'deform
    const SCALE_ORIGINAL = 'original

    public static function calculateScaleDimensions($source, $size, $scale = self::SCALE_BESTFI
   
        $width  = !isset($size['width'])  && isset($size[0]) ? $size[0] : 0
        $height = !isset($size['height']) && isset($size[1]) ? $size[1] : 0

        if ($width == 0 && $height == 0)
            return fals
       

        if (!is_file($source))
            return fals
       

        if ($width == 0)
            $size = getimagesize($source
            $width = round($height * ($size[0]/$size[1])
            return array('width'=>$width, 'height'=>$height
        } else if ($height == 0)
            $size = getimagesize($source
            $height = round($width * ($size[1]/$size[0])
            return array('width'=>$width, 'height'=>$height
        } else if ($scale == self::SCALE_CROP){ // Bild wird auf allen 4 Seiten gleichmäßig beschnitt

            $size = getimagesize($source
            if ($size[0] > $width) { // Wenn hochgeladenes Bild breiter als anzuzeigendes Bild i
                $x = ($size[0] - $width) / 2; // Ursprungs-X berechn
            } else
                $x = 0; // Bei 0 mit Beschneiden beginn
                $width = $size[0]; // Breite auf Originalgröße begrenz
           
            if ($size[1] > $height)
                $y = ($size[1] - $height) / 
            } else
                $y = 
                $height = $size[1
           
            return array('width'=>round($width), 'height'=>round($height
                        'x'=>round($x), 'y'=>round($y)

        } elseif ($scale == self::SCALE_BESTFIT) { // Bild wird auf größte Maximale Ausdehnung skalie

            $size = getimagesize($source
            $widthRatio = $size[0] / $widt
            $heightRatio = $size[1] / $heigh
            if ($widthRatio > $heightRatio
                $width = $size[0] / $widthRati
                $height = $size[1] / $widthRati
            } else
                $width = $size[0] / $heightRati
                $height = $size[1] / $heightRati
           
            return array('width'=>round($width), 'height'=>round($height)

        } elseif ($scale == self::SCALE_DEFORM)

            return array('width'=>$width, 'height'=>$height

        } elseif ($scale == self::SCALE_ORIGINAL)

            $size = getimagesize($source
            return array('width'=>$size[0], 'height'=>$size[1]

        } else

            return fals

       
   

    public static function scale($source, $target, $size, $scale = self::SCALE_BESTFI
   
        $size = self::calculateScaleDimensions($source, $size, $scale

        if ($size === false) return fals

        if ($scale == self::SCALE_CROP){ // Bild wird auf allen 4 Seiten gleichmäßig beschnitt

            $im = new Imagick(
            $im->readImage($source
            $im->cropImage($size['width'], $size['height'], $size['x'], $size['y']
            $im->writeImage($target
            $im->destroy(

        } elseif ($scale == self::SCALE_BESTFIT || $scale == self::SCALE_DEFORM)
            if (class_exists('Imagick'))
                $im = new Imagick(
                $im->readImage($source
                $im->thumbnailImage($size['width'], $size['height']
                $im->writeImage($target
                $im->destroy(
            } else
                $srcSize = getimagesize($source
                if ($srcSize[2] == 1)
                    $source = imagecreatefromgif($source
                } elseif ($srcSize[2] == 2)
                    $source = imagecreatefromjpeg($source
                } elseif ($srcSize[2] == 3)
                    $source = imagecreatefrompng($source
               
                $destination = imagecreatetruecolor($size['width'], $size['height']
                imagecopyresampled($destination, $source, 0, 0, 0, 
                                    $size['width'], $size['height'
                                    $srcSize[0], $srcSize[1]
                if ($srcSize[2] == 1)
                    $source = imagegif($destination, $target
                } elseif ($srcSize[2] == 2)
                    $source = imagejpeg($destination, $target
                } elseif ($srcSize[2] == 3)
                    $source = imagepng($destination, $target
               
           

        } elseif ($scale == self::SCALE_ORIGINAL

            copy($source, $target

        } else

            return fals

       

        chmod($target, 0644
        return tru
   
