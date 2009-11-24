<?php
class Vps_Cache_Backend_TwoLevels extends Zend_Cache_Backend_TwoLevels
{
    public function __construct(array $options = array())
    {
        if (!isset($options['slow_backend_autoload'])) {
            $options['slow_backend_autoload'] = true;
        }
        if (!isset($options['slow_backend'])) {
            $options['slow_backend'] = 'Vps_Cache_Backend_File';
            $options['slow_backend_custom_naming'] = true;
            if (isset($options['cache_dir'])) {
                $options['slow_backend_options']['cache_dir'] = $options['cache_dir'];
                unset($options['cache_dir']);
            }
        }

        if (!isset($options['fast_backend_autoload'])) {
            $options['fast_backend_autoload'] = true;
        }
        if (!isset($options['fast_backend'])) {
            $options['fast_backend'] = 'Vps_Cache_Backend_Memcached';
            $options['fast_backend_custom_naming'] = true;
        }
        parent::__construct($options);
    }

}
