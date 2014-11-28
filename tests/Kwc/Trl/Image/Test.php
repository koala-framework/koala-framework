<?php
/**
 * @group slow
 * @group Kwc_Trl
 * @group Kwc_Trl_Image
 * @group Image
 *
ansicht frontend:
http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_Image_Root/de/test1 (...2)
http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_Image_Root/en/test1 (...2)
 */
class Kwc_Trl_Image_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_Image_Root');

        //master image
        Kwf_Model_Abstract::getInstance('Kwc_Trl_Image_Image_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-master_test1', 'kwf_upload_id'=>'1'),
                array('component_id'=>'root-master_test2', 'kwf_upload_id'=>'1'),
            ));


        //trl image
        Kwf_Model_Abstract::getInstance('Kwc_Trl_Image_Image_Trl_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1', 'own_image'=>0),
                array('component_id'=>'root-en_test2', 'own_image'=>1),
            ));

        //own image (below trl image)
        Kwf_Model_Abstract::getInstance('Kwc_Trl_Image_Image_Trl_Image_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1-image', 'kwf_upload_id'=>null),
                array('component_id'=>'root-en_test2-image', 'kwf_upload_id'=>'2'),
            ));
    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_test1');
        $this->_checkTheSizes($c->render(), 1, 120, 120);

        $c = $this->_root->getComponentById('root-master_test2');
        $this->_checkTheSizes($c->render(), 1, 120, 120);
    }

    public function testEn()
    {
        $c = $this->_root->getComponentById('root-en_test1');
        $this->_checkTheSizes($c->render(), 1, 120, 120);

        $c = $this->_root->getComponentById('root-en_test2');
        $this->_checkTheSizes($c->render(), 2, 120, 101);
    }

    public function testDeClearCache()
    {
        $c = $this->_root->getComponentById('root-master_test1');
        $this->_checkTheSizes($c->render(), 1, 120, 120);
        $row = $c->getComponent()->getRow();
        $row->kwf_upload_id = 2;
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 2, 120, 101);

    }

    public function testEnClearCache()
    {
        $c = $this->_root->getComponentById('root-en_test1');
        $this->_checkTheSizes($c->render(), 1, 120, 120);
        $row = $this->_root->getComponentById('root-master_test1')->getComponent()->getRow();
        $row->kwf_upload_id = 2;
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 2, 120, 101);
    }

    public function testEnAlternativeClearCache()
    {
        $c = $this->_root->getComponentById('root-en_test1');

        $this->_checkTheSizes($c->render(), 1, 120, 120);

        $this->_process();
        $this->_checkTheSizes($c->render(), 1, 120, 120);

        $row = $c->getComponent()->getRow();
        $row->own_image = 1;
        $row->save();
        $this->_process();

        $row = $this->_root->getComponentById('root-en_test1-image')->getComponent()->getRow();
        $row->kwf_upload_id = 2;
        $row->save();
        $this->_process();

        sleep(1);
        $this->_checkTheSizes($c->render(), 2, 120, 101);
    }

    private function _checkTheSizes($html, $smallImageNum, $smallWidth, $smallHeight)
    {
        // getMediaOutput aufrufen, damit Cache-Meta geschrieben wird (wegen d0cf3812b20fa19c40617ac5b08ed08a18ff808d)
        // muss so gemacht werden, weil der request über getimagesize weiter unten
        // nicht das FnF-Cache Model dieses Request schreiben kann
        preg_match('# src=".*/media/([^/]+)/([^/]+)/([^/]+)#', $html, $matches);
        Kwf_Media::getOutput($matches[1], $matches[2], $matches[3]);

        $this->assertRegExp('#<img.+?src=".+?'.$smallImageNum.'\.jpg.+width="'.$smallWidth.'".+height="'.$smallHeight.'"#ms', $html);

        preg_match('# src="(.+?)"#ms', $html, $matches);
        $smallSrcSize = getimagesize('http://'.Kwf_Registry::get('testDomain').$matches[1]);

        $this->assertEquals($smallWidth, $smallSrcSize[0]);
        $this->assertEquals($smallHeight, $smallSrcSize[1]);
    }
}
