<?php
class Kwf_Cache_CacheTest extends Kwf_Test_TestCase
{
    public function testCacheMtimeFiles()
    {
        $checkCmpMod = Kwf_Registry::get('config')->debug->componentCache->checkComponentModification;
        Kwf_Registry::get('config')->debug->componentCache->checkComponentModification = false;
        Kwf_Config::deleteValueCache('debug.componentCache.checkComponentModification');

        $f = tempnam('/tmp', 'cacheMtimeTest');
        $filemtime = filemtime($f);

        $cacheId = time().mt_rand(0, 10000).'a';
        $cacheDataOrig = array(
            'mimeType' => 'text/plain',
            'contents' => 'foobar'.$cacheId,
            'mtimeFiles' => array($f),
            'mtime' => $filemtime
        );
        $cacheData = $cacheDataOrig;

        $cache = new Kwf_Cache_CacheClass();
        $cache->save($cacheData, $cacheId);
        $this->assertEquals($cacheDataOrig, $cacheData);

        $this->assertEquals($cacheData, $cache->load($cacheId));

        $newTime = $filemtime + 10;
        $this->assertTrue(touch($f, $newTime));
        clearstatcache();

        $this->assertEquals($cacheData, $cache->load($cacheId));

        Kwf_Registry::get('config')->debug->componentCache->checkComponentModification = true;
        Kwf_Config::deleteValueCache('debug.componentCache.checkComponentModification');

        $this->assertFalse($cache->load($cacheId));

        unlink($f);
        $cache->cleanUp();

        Kwf_Registry::get('config')->debug->componentCache->checkComponentModification = $checkCmpMod;
        Kwf_Config::deleteValueCache('debug.componentCache.checkComponentModification');
    }

    public function testCacheMtimeFilesCheckAlways()
    {
        $checkCmpMod = Kwf_Registry::get('config')->debug->componentCache->checkComponentModification;
        Kwf_Registry::get('config')->debug->componentCache->checkComponentModification = false;
        Kwf_Config::deleteValueCache('debug.componentCache.checkComponentModification');

        $f = tempnam('/tmp', 'cacheMtimeTestCheckAlways');
        $filemtime = filemtime($f);

        $cacheId = time().mt_rand(0, 10000).'b';
        $cacheDataOrig = array(
            'mimeType' => 'text/plain',
            'contents' => 'foobar'.$cacheId,
            'mtimeFilesCheckAlways' => array($f),
            'mtime' => $filemtime
        );
        $cacheData = $cacheDataOrig;

        $cache = new Kwf_Cache_CacheClass();
        $cache->save($cacheData, $cacheId);
        $this->assertEquals($cacheDataOrig, $cacheData);

        $this->assertEquals($cacheData, $cache->load($cacheId));

        $newTime = $filemtime + 10;
        $this->assertTrue(touch($f, $newTime));
        clearstatcache();

        $this->assertFalse($cache->load($cacheId));

        unlink($f);
        $cache->cleanUp();

        Kwf_Registry::get('config')->debug->componentCache->checkComponentModification = $checkCmpMod;
        Kwf_Config::deleteValueCache('debug.componentCache.checkComponentModification');
    }
}