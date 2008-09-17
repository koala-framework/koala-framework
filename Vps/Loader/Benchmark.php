<?php
class Vps_Loader_Benchmark extends Vps_Loader
{
    public static function autoload($class)
    {
        $ret = parent::autoload($class);
        if ($ret && substr($class, 0, 4) == 'Vpc_') {
            if (is_subclass_of($class, 'Vpc_Abstract')) {
                Vps_Benchmark::count('component classes included', $class);
            }
        }
        Vps_Benchmark::count('classes included', $class);
        return $ret;
    }
}
