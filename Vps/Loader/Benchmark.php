<?php
require_once 'Vps/Loader.php';
class Vps_Loader_Benchmark extends Vps_Loader
{
    public static function loadClass($class)
    {
        parent::loadClass($class);
        Vps_Benchmark::count('classes included', $class);
    }
}
