<?php
class Kwf_Util_MemcacheSessionHandler_TestSessionHandler extends Kwf_Util_SessionHandler
{
    public function __construct($options = array())
    {
        if (isset($options['lifeTime'])) $this->_lifeTime = $options['lifeTime'];
        if (isset($options['refreshTime'])) $this->_refreshTime = $options['refreshTime'];
    }
}
