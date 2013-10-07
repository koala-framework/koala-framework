<?php
class RedMallee_Box_FooterLogos_Links_Image_Component extends Kwc_TextImage_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlKwf('default'),
                'width' => 400,
                'height' => 240,
                'scale' => Kwf_Media_Image::SCALE_BESTFIT
            ),
        );
        return $ret;
    }
    public function getImageData()
    {
        $ret = parent::getImageData();
        if (!$ret) return $ret;

        $dim = array(
            'width' => 400,
            'height' => 240,
            'scale' => Kwf_Media_Image::SCALE_BESTFIT
        );
        $imageContents = Kwf_Media_Image::scale($ret['file'], $dim);

        $image = new Imagick();
        $image->readImageBlob($imageContents);
        $image2 = new Imagick();
        $image2->readImageBlob($imageContents);
        $image2->setImageType(Imagick::IMGTYPE_GRAYSCALE);
        $im = new Imagick();
        $im->addImage($image);
        $im->addImage($image2);
        $im->resetIterator();
        $combined = $im->appendImages(true);

        $ret['image'] = $combined;
        return $ret;
    }
}
