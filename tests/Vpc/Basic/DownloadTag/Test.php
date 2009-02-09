<?php
/**
 * @group Basic_DownloadTag
 */
class Vpc_Basic_DownloadTag_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Basic_DownloadTag_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function tearDown()
    {
        $m = Vps_Model_Abstract::getInstance('Vpc_Basic_DownloadTag_UploadsModel');
        $dir = $m->getUploadDir();
        if (substr($dir, 0, 4)=='/tmp') {
            system('rm -r '.$dir);
        }
        Vps_Model_Abstract::clearInstances();
    }

    public function testUrl()
    {
        $c = $this->_root->getComponentById('1700');
        $this->assertTrue($c->hasContent());

        $url = $c->getComponent()->getDownloadUrl();
        $url = explode('/', trim($url, '/'));
        $this->assertEquals('Vpc_Basic_DownloadTag_TestComponent', $url[1]);
        $this->assertEquals('1700', $url[2]);
        $this->assertEquals('default', $url[3]);
        $this->assertEquals('foo.png', $url[5]);
    }

    public function testUrlWithOwnFilename()
    {
        $c = $this->_root->getComponentById('1701');
        $url = $c->getComponent()->getDownloadUrl();
        $url = explode('/', trim($url, '/'));
        $this->assertEquals('myname.png', $url[5]);
    }

    public function testGetMediaOutput()
    {
        $o = Vpc_Basic_DownloadTag_Component::getMediaOutput('1700', 'default', 'Vpc_Basic_DownloadTag_TestComponent');
        $this->assertEquals('image/png', $o['mimeType']);
        $this->assertEquals(Vps_Model_Abstract::getInstance('Vpc_Basic_DownloadTag_UploadsModel')->getUploadDir().'/1', $o['file']);
    }

    public function testHtml()
    {
        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($this->_root->getComponentById(1700));
        $this->assertEquals('<a href="/media/Vpc_Basic_DownloadTag_TestComponent/1700/default/26ef864633eb161c415779746271adc8/foo.png">'."\n", $html);
    }

    public function testEmpty()
    {
        $c = $this->_root->getComponentById('1702');
        $this->assertFalse($c->hasContent());

        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($c);
        $this->assertEquals('', $html);
    }
}
