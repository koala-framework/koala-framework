<?php
require_once 'Vps/Loader.php';
class Vps_Loader_Benchmark extends Vps_Loader
{
    public static function loadClass($class)
    {
        parent::loadClass($class);
        if (substr($class, 0, 4) == 'Vpc_') {
            if (is_subclass_of($class, 'Vpc_Abstract')) {
                Vps_Benchmark::count('component classes included', $class);
            }
        }
        Vps_Benchmark::count('classes included', $class);
    }
}
