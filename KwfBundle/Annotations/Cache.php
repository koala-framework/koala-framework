<?php
namespace KwfBundle\Annotations;

use Doctrine\Common\Cache\CacheProvider;

class Cache extends CacheProvider
{
    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return \Kwf_Cache_SimpleStatic::fetch($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return \Kwf_Cache_SimpleStatic::exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        return \Kwf_Cache_SimpleStatic::add($id, $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        throw new \Kwf_Exception_NotYetImplemented();
        //return \Kwf_Cache_SimpleStatic::_delete($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        throw new \Kwf_Exception_NotYetImplemented();
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetchMultiple(array $keys)
    {
        return \Kwf_Cache_SimpleStatic::fetchMultiple($keys);
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        return null;
    }
}