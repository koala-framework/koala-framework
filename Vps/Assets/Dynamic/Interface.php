<?php
/**
 * Dynamisches Asset, um Dependencies Cachen zu können den Dateinamen aber
 * dynamisch zu ermitteln.
 *
 * Wird verwendet von Vpc_Basic_Text
 **/
interface Vps_Assets_Dynamic_Interface
{
    public function __construct(Vps_Assets_Loader $loader, $assetsType, $rootComponent);
    public function getContents();
    public function getMTimeFiles();
    public function getMTime();
    public function getType();
    public function getIncludeInAll();
}
