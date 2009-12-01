<?php
class Vps_Benchmark_Counter
{
    public static function getInstance()
    {
        static $i;
        if (!isset($i)) {
            if (class_exists('Memcache')) {
                $i = new Vps_Benchmark_Counter_Memcache();
            } else {
                $i = new Vps_Benchmark_Counter_File();
            }
        }
        return $i;
    }
}
