<?php
/**
 * @group Media
 * @group Image
 */
class Kwf_Media_UrlTest extends Kwf_Test_Kwc_TestCase
{
    public function testUrl()
    {
        $url = Kwf_Media::getUrl('Kwc_Basic_Image_TestComponent', 1600, 'foo', 'test.jpg');
        $url = explode('/', trim($url, '/'));
        $this->assertEquals('Kwc_Basic_Image_TestComponent', $url[1]);
        $this->assertEquals(1600, $url[2]);
        $this->assertEquals('foo', $url[3]);
        $this->assertEquals('test.jpg', $url[6]);

        $c1 = Kwf_Media::getChecksum('Kwc_Basic_Image_TestComponent', 1600, 'foo', 'test.jpg');
        $c2 = Kwf_Media::getChecksum($url[1], $url[2], $url[3], $url[6]);
        $this->assertEquals($c1, $c2);
    }

    public function testUrlByRow()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Media_TestOutputModel');
        $r = $m->createRow();
        $r->id = 23;
        $r->save();

        $url = Kwf_Media::getUrlByRow($r, 'default', 'test.jpg');
        $url = explode('/', trim($url, '/'));
        $this->assertEquals('Kwf_Media_TestOutputModel', $url[1]);
        $this->assertEquals(23, $url[2]);
        $this->assertEquals('default', $url[3]);
        $this->assertEquals('test.jpg', $url[6]);
    }

    public function testOutputCache()
    {

        Kwf_Media_TestMediaOutputClass::$called = 0;
        $id = time()+rand(0, 10000);
        Kwf_Media::clearCache('Kwf_Media_TestMediaOutputClass', $id, 'simple');
        $o = Kwf_Media::getOutput('Kwf_Media_TestMediaOutputClass', $id, 'simple');

        unset($o['mtime']);
        $this->assertEquals(array('mimeType' => 'text/plain', 'contents'=>'foobar'.$id), $o);
        $this->assertEquals(1, Kwf_Media_TestMediaOutputClass::$called);

        $o = Kwf_Media::getOutput('Kwf_Media_TestMediaOutputClass', $id, 'simple');
        unset($o['mtime']);
        $this->assertEquals(array('mimeType' => 'text/plain', 'contents'=>'foobar'.$id), $o);
        $this->assertEquals(1, Kwf_Media_TestMediaOutputClass::$called);

        Kwf_Media::clearCache('Kwf_Media_TestMediaOutputClass', $id, 'simple');
        Kwf_Media::getOutput('Kwf_Media_TestMediaOutputClass', $id, 'simple');
        $this->assertEquals(2, Kwf_Media_TestMediaOutputClass::$called);
    }

    public function testOutputReturnsNull()
    {
        Kwf_Media_TestMediaOutputClass::$called = 0;
        $id = time()+rand(0, 10000);
        $o = Kwf_Media::getOutput('Kwf_Media_TestMediaOutputClass', $id, 'nothing');
        unset($o['mtime']);
        $this->assertEquals(array(), $o);
        $this->assertEquals(1, Kwf_Media_TestMediaOutputClass::$called);

        $o = Kwf_Media::getOutput('Kwf_Media_TestMediaOutputClass', $id, 'nothing');
        unset($o['mtime']);
        $this->assertEquals(array(), $o);
        $this->assertEquals(2, Kwf_Media_TestMediaOutputClass::$called); //this results in an 404 and won't be cached
    }

    public function testOutputCacheWithMtimeFiles()
    {
        Kwf_Media_TestMediaOutputClass::$called = 0;

        $f = tempnam('/tmp', 'outputTest');
        Kwf_Media_TestMediaOutputClass::$mtimeFiles = array($f);

        $id = time()+rand(0, 10000);
        $o = Kwf_Media::getOutput('Kwf_Media_TestMediaOutputClass', $id, 'mtimeFiles');
        unset($o['mtime']);
        $this->assertEquals(array('mimeType' => 'text/plain',
                                  'contents'=>'foobar'.$id,
                                  'mtimeFiles'=>array($f)), $o);
        $this->assertEquals(1, Kwf_Media_TestMediaOutputClass::$called);

        $o = Kwf_Media::getOutput('Kwf_Media_TestMediaOutputClass', $id, 'mtimeFiles');
        $this->assertEquals(1, Kwf_Media_TestMediaOutputClass::$called);

        $newTime = time()+10;
        $this->assertTrue(touch($f, $newTime));
        clearstatcache();
        $this->assertEquals($newTime, filemtime($f));

        $o = Kwf_Media::getOutput('Kwf_Media_TestMediaOutputClass', $id, 'mtimeFiles');
        $this->assertEquals(array('mimeType' => 'text/plain',
                                  'contents'=>'foobar'.$id,
                                  'mtimeFiles'=>array($f),
                                  'mtime' => $newTime), $o);
        $this->assertEquals(2, Kwf_Media_TestMediaOutputClass::$called);

        $o = Kwf_Media::getOutput('Kwf_Media_TestMediaOutputClass', $id, 'mtimeFiles');
        $this->assertEquals(2, Kwf_Media_TestMediaOutputClass::$called);
    }
}
