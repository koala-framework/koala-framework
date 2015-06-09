<?php
class Kwf_Loader_Benchmark extends Kwf_Loader
{
    public static function loadClass($class)
    {
        parent::loadClass($class);
        Kwf_Benchmark::count('classes included', $class);
    }
}
