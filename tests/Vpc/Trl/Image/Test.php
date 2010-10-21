<?php
/**
 * @group slow
 * @group Vpc_Trl
 * @group Vpc_Trl_Image
 *
ansicht frontend:
http://vps.vps.niko.vivid/vps/vpctest/Vpc_Trl_Image_Root/de/test1 (...2)
http://vps.vps.niko.vivid/vps/vpctest/Vpc_Trl_Image_Root/en/test1 (...2)
 */
class Vpc_Trl_Image_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_Image_Root');

        //master image
        Vps_Model_Abstract::getInstance('Vpc_Trl_Image_Image_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-master_test1', 'vps_upload_id'=>'1'),
                array('component_id'=>'root-master_test2', 'vps_upload_id'=>'1'),
            ));


        //trl image
        Vps_Model_Abstract::getInstance('Vpc_Trl_Image_Image_Trl_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1', 'own_image'=>0),
                array('component_id'=>'root-en_test2', 'own_image'=>1),
            ));

        //own image (below trl image)
        Vps_Model_Abstract::getInstance('Vpc_Trl_Image_Image_Trl_Image_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test2-image', 'vps_upload_id'=>'2'),
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
        $row->vps_upload_id = 2;
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 2, 120, 101);

    }

    public function testEnClearCache()
    {
        $c = $this->_root->getComponentById('root-en_test1');
        $this->_checkTheSizes($c->render(), 1, 120, 120);
        $row = $this->_root->getComponentById('root-master_test1')->getComponent()->getRow();
        $row->vps_upload_id = 2;
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 2, 120, 101);
    }

    public function testEnAlternativeClearCache()
    {
//         file_put_contents('log', "\ntest start testEnAlternativeClearCache\n", FILE_APPEND);
        $c = $this->_root->getComponentById('root-en_test1');

//         file_put_contents('log', "\n120x120 zum ersten mal\n", FILE_APPEND);
        $this->_checkTheSizes($c->render(), 1, 120, 120);

        $this->_process();
//         file_put_contents('log', "\n120x120 gleich nochmal, jetzt gecached\n", FILE_APPEND);
        $this->_checkTheSizes($c->render(), 1, 120, 120);

        $row = $c->getComponent()->getRow();
        $row->own_image = 1;
        $row->save();
        $this->_process();

        $row = $this->_root->getComponentById('root-en_test1-image')->getComponent()->getRow();
        $row->vps_upload_id = 2;
        $row->save();
        $this->_process();
        
//         file_put_contents('log', "\n120x101 wurde geaendert, ungecached\n", FILE_APPEND);
        $this->_checkTheSizes($c->render(), 2, 120, 101);
    }

    private function _checkTheSizes($html, $smallImageNum, $smallWidth, $smallHeight)
    {
        $this->assertRegExp('#<img.+?src=".+?'.$smallImageNum.'\.jpg.+width="'.$smallWidth.'".+height="'.$smallHeight.'"#ms', $html);

        preg_match('#src="(.+?)"#ms', $html, $matches);

        file_put_contents('log', "\nhttp request\n", FILE_APPEND);
        $smallSrcSize = getimagesize('http://'.Vps_Registry::get('testDomain').$matches[1]);

        $this->assertEquals($smallWidth, $smallSrcSize[0]);
        $this->assertEquals($smallHeight, $smallSrcSize[1]);
    }
}
