<?php
class Kwf_Component_Cache_MemoryBlackHole
{
    public function loadWithMetaData($id)
    {
        return false;
    }

    public function save($data, $id, $ttl)
    {
        return true;
    }

    public function remove($id, $microtime)
    {
        return false;
    }
}
