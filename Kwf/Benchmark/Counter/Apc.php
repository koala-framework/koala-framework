<?php
class Kwf_Benchmark_Counter_Apc implements Kwf_Benchmark_Counter_Interface
{
    public function increment($name, $value=1)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Kwf_Cache::getUniquePrefix().'bench-';
        apc_inc($prefix.$name, $value, $success);
        if (!$success) {
            apc_add($prefix.$name, $value);
        }
    }

    public function getValue($name)
    {
        $d = Kwf_Registry::get('config')->server->domain;
        $url = "http://$d/kwf/util/apc/get-counter-value?name=".rawurlencode($name);
        return (int)file_get_contents($url);
    }

}
