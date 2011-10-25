<?php
class Kwf_Benchmark_Counter
{
    public static function getInstance()
    {
        static $i;
        if (!isset($i)) {
            if (extension_loaded('apc')) {
                if (function_exists('apc_inc')) { //apc >= 3.1.1
                    $i = new Kwf_Benchmark_Counter_Apc();
                } else {
                    //kein memcache-fallback, da der dann *nur* für den counter verwendet werden würde
                    $i = new Kwf_Benchmark_Counter_File();
                }
            } else if (class_exists('Memcache')) {
                $i = new Kwf_Benchmark_Counter_Memcache();
            } else {
                $i = new Kwf_Benchmark_Counter_File();
            }
        }
        return $i;
    }
}
