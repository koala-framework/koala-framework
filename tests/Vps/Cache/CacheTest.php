<?php
class Vps_Cache_CacheTest extends Vps_Test_TestCase
{
    public function testCacheMtimeFiles()
    {
        $checkCmpMod = Vps_Registry::get('config')->debug->componentCache->checkComponentModification;
        Vps_Registry::get('config')->debug->componentCache->checkComponentModification = false;

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

        $cache = new Vps_Cache_CacheClass();
        $cache->save($cacheData, $cacheId);
        $this->assertEquals($cacheDataOrig, $cacheData);

        $this->assertEquals($cacheData, $cache->load($cacheId));

        $newTime = $filemtime + 10;
        $this->assertTrue(touch($f, $newTime));
        clearstatcache();

        $this->assertEquals($cacheData, $cache->load($cacheId));

        Vps_Registry::get('config')->debug->componentCache->checkComponentModification = true;

        $this->assertFalse($cache->load($cacheId));

        unlink($f);
        $cache->cleanUp();

        Vps_Registry::get('config')->debug->componentCache->checkComponentModification = $checkCmpMod;
    }

    public function testCacheMtimeFilesCheckAlways()
    {
        $checkCmpMod = Vps_Registry::get('config')->debug->componentCache->checkComponentModification;
        Vps_Registry::get('config')->debug->componentCache->checkComponentModification = false;

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

        $cache = new Vps_Cache_CacheClass();
        $cache->save($cacheData, $cacheId);
        $this->assertEquals($cacheDataOrig, $cacheData);

        $this->assertEquals($cacheData, $cache->load($cacheId));

        $newTime = $filemtime + 10;
        $this->assertTrue(touch($f, $newTime));
        clearstatcache();

        $this->assertFalse($cache->load($cacheId));

        unlink($f);
        $cache->cleanUp();

        Vps_Registry::get('config')->debug->componentCache->checkComponentModification = $checkCmpMod;
    }
}