<?php
/**
 * Dynamisches Asset, um Dependencies Cachen zu können den Dateinamen aber
 * dynamisch zu ermitteln.
 *
 * Wird verwendet von Kwc_Basic_Text
 **/
interface Kwf_Assets_Dynamic_Interface
{
    public function __construct(Kwf_Assets_Loader $loader, $assetsType, $rootComponent, $arguments);
    public function getContents();
    public function getMTimeFiles();
    public function getMTime();
    public function getType();
    public function getIncludeInAll();
}
