<?php
class Kwf_Benchmark_Counter_Apc
{
    public function increment($name, $value=1)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'bench-';
        apc_inc($prefix.$name, $value, $success);
        if (!$success) {
            apc_add($prefix.$name, $value);
        }
    }

    public function getValue($name)
    {
        if (php_sapi_name() == 'cli') {
            return (int)Kwf_Util_Apc::callUtil('get-counter-value', array('name'=>$name), array('returnBody'=>true));
        } else {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'bench-';
            return (int)apc_fetch($prefix.$name);
        }
    }

}
