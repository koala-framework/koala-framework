<?php
class Vps_Assets_Effects
{
    public static function home(Imagick $image)
    {
        $overlay = new Imagick();
        $overlay->readImage(VPS_PATH."/Vps/Assets/Overlay/home.png");
        $image->compositeImage($overlay, Imagick::COMPOSITE_OVER, (16-12)/2, (16-11)/2);
    }

    public static function chained(Imagick $image)
    {
        $overlay = new Imagick();
        $overlay->readImage(VPS_PATH."/Vps/Assets/Overlay/chain.png");
        $image->compositeImage($overlay, Imagick::COMPOSITE_OVER, 16-14, 16-7);
    }

    public static function invisible(Imagick $image)
    {
        $image->setImageOpacity(0.2);
    }

    public static function forbidden(Imagick $image)
    {
        $overlay = new Imagick();
        $overlay->readImage(VPS_PATH."/Vps/Assets/Overlay/forbidden.png");
        $image->compositeImage($overlay, Imagick::COMPOSITE_OVER, 16-10, 16-10);
    }
}
