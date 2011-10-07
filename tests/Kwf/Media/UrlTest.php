<?php
/**
 * @group Media
 */
class Vps_Media_UrlTest extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_Image_Root');
        $this->_root->setFilename(null);
    }

    public function testUrl()
    {
        $url = Vps_Media::getUrl('Vpc_Basic_Image_TestComponent', 1600, 'foo', 'test.jpg');
        $url = explode('/', trim($url, '/'));
        $this->assertEquals('Vpc_Basic_Image_TestComponent', $url[1]);
        $this->assertEquals(1600, $url[2]);
        $this->assertEquals('foo', $url[3]);
        $this->assertEquals('test.jpg', $url[6]);

        $c1 = Vps_Media::getChecksum('Vpc_Basic_Image_TestComponent', 1600, 'foo', 'test.jpg');
        $c2 = Vps_Media::getChecksum($url[1], $url[2], $url[3], $url[6]);
        $this->assertEquals($c1, $c2);
    }

    public function testUrlByRow()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Media_TestOutputModel');
        $r = $m->createRow();
        $r->id = 23;
        $r->save();

        $url = Vps_Media::getUrlByRow($r, 'default', 'test.jpg');
        $url = explode('/', trim($url, '/'));
        $this->assertEquals('Vps_Media_TestOutputModel', $url[1]);
        $this->assertEquals(23, $url[2]);
        $this->assertEquals('default', $url[3]);
        $this->assertEquals('test.jpg', $url[6]);
    }

    public function testOutputCache()
    {
        Vps_Media::getOutputCache()->clean();

        Vps_Media_TestMediaOutputClass::$called = 0;
        $id = time()+rand(0, 10000);
        $o = Vps_Media::getOutput('Vps_Media_TestMediaOutputClass', $id, 'simple');

        unset($o['mtime']);
        $this->assertEquals(array('mimeType' => 'text/plain', 'contents'=>'foobar'.$id), $o);
        $this->assertEquals(1, Vps_Media_TestMediaOutputClass::$called);

        $o = Vps_Media::getOutput('Vps_Media_TestMediaOutputClass', $id, 'simple');
        unset($o['mtime']);
        $this->assertEquals(array('mimeType' => 'text/plain', 'contents'=>'foobar'.$id), $o);
        $this->assertEquals(1, Vps_Media_TestMediaOutputClass::$called);

        Vps_Media::getOutputCache()->clean();
        Vps_Media::getOutput('Vps_Media_TestMediaOutputClass', $id, 'simple');
        $this->assertEquals(2, Vps_Media_TestMediaOutputClass::$called);
    }

    public function testOutputReturnsNull()
    {
        Vps_Media_TestMediaOutputClass::$called = 0;
        $id = time()+rand(0, 10000);
        $o = Vps_Media::getOutput('Vps_Media_TestMediaOutputClass', $id, 'nothing');
        unset($o['mtime']);
        $this->assertEquals(array(), $o);
        $this->assertEquals(1, Vps_Media_TestMediaOutputClass::$called);

        $o = Vps_Media::getOutput('Vps_Media_TestMediaOutputClass', $id, 'nothing');
        unset($o['mtime']);
        $this->assertEquals(array(), $o);
        $this->assertEquals(1, Vps_Media_TestMediaOutputClass::$called);
    }

    public function testOutputCacheWithMtimeFiles()
    {
        $checkCmpMod = Vps_Registry::get('config')->debug->componentCache->checkComponentModification;
        Vps_Registry::get('config')->debug->componentCache->checkComponentModification = true;
        Vps_Config::deleteValueCache('debug.componentCache.checkComponentModification');

        Vps_Media_TestMediaOutputClass::$called = 0;

        $f = tempnam('/tmp', 'outputTest');
        Vps_Media_TestMediaOutputClass::$mtimeFiles = array($f);

        $id = time()+rand(0, 10000);
        $o = Vps_Media::getOutput('Vps_Media_TestMediaOutputClass', $id, 'mtimeFiles');
        unset($o['mtime']);
        $this->assertEquals(array('mimeType' => 'text/plain',
                                  'contents'=>'foobar'.$id,
                                  'mtimeFiles'=>array($f)), $o);
        $this->assertEquals(1, Vps_Media_TestMediaOutputClass::$called);

        $o = Vps_Media::getOutput('Vps_Media_TestMediaOutputClass', $id, 'mtimeFiles');
        $this->assertEquals(1, Vps_Media_TestMediaOutputClass::$called);

        $newTime = time()+10;
        $this->assertTrue(touch($f, $newTime));
        clearstatcache();
        $this->assertEquals($newTime, filemtime($f));

        $o = Vps_Media::getOutput('Vps_Media_TestMediaOutputClass', $id, 'mtimeFiles');
        $this->assertEquals(array('mimeType' => 'text/plain',
                                  'contents'=>'foobar'.$id,
                                  'mtimeFiles'=>array($f),
                                  'mtime' => $newTime), $o);
        $this->assertEquals(2, Vps_Media_TestMediaOutputClass::$called);

        $o = Vps_Media::getOutput('Vps_Media_TestMediaOutputClass', $id, 'mtimeFiles');
        $this->assertEquals(2, Vps_Media_TestMediaOutputClass::$called);

        Vps_Registry::get('config')->debug->componentCache->checkComponentModification = $checkCmpMod;
        Vps_Config::deleteValueCache('debug.componentCache.checkComponentModification');
    }
}
