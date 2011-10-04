<?php
class Vps_Benchmark_Counter_Apc implements Vps_Benchmark_Counter_Interface
{
    public function increment($name, $value=1)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix().'bench-';
        apc_inc($prefix.$name, $value, $success);
        if (!$success) {
            apc_add($prefix.$name, $value);
        }
    }

    public function getValue($name)
    {
        $d = Vps_Registry::get('config')->server->domain;
        $url = "http://$d/vps/util/apc/get-counter-value?name=".rawurlencode($name);
        return (int)file_get_contents($url);
    }

}
