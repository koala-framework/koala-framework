<?php
/**
 * @group slow
 * @group Vpc_Trl
 * @group Vpc_Trl_ImageEnlarge
 *
ansicht frontend:
http://fnprofile.markus.vivid/vps/vpctest/Vpc_Trl_ImageEnlarge_Root/de/test1 (...2,3,4,5,6)
http://fnprofile.markus.vivid/vps/vpctest/Vpc_Trl_ImageEnlarge_Root/en/test1 (...2,3,4,5,6)
 */
class Vpc_Trl_ImageEnlarge_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_ImageEnlarge_Root');
    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_test1');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        $c = $this->_root->getComponentById('root-master_test2');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 2, 120, 101);

        $c = $this->_root->getComponentById('root-master_test3');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        $c = $this->_root->getComponentById('root-master_test4');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 2, 120, 101);

        $c = $this->_root->getComponentById('root-master_test5');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 2, 120, 101);

        $c = $this->_root->getComponentById('root-master_test6');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 2, 120, 101);
    }

    public function testEn()
    {
        $c = $this->_root->getComponentById('root-en_test1');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        $c = $this->_root->getComponentById('root-en_test2');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 2, 120, 101);

        $c = $this->_root->getComponentById('root-en_test3');
        $this->_checkTheSizes($c->render(), 6, 180, 330, 6, 65, 120);

        $c = $this->_root->getComponentById('root-en_test4');
        $this->_checkTheSizes($c->render(), 6, 180, 330, 2, 120, 101);

        $c = $this->_root->getComponentById('root-en_test5');
        $this->_checkTheSizes($c->render(), 6, 180, 330, 5, 95, 120);

        $c = $this->_root->getComponentById('root-en_test6');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 5, 95, 120);
    }

    private function _checkTheSizes($html, $largeImageNum, $largeWidth, $largeHeight, $smallImageNum, $smallWidth, $smallHeight)
    {
        $this->assertRegExp('#^.*?<a.*?href=".+?'.$largeImageNum.'\.jpg.+?enlarge_'.$largeWidth.'_'.$largeHeight.'.+?<img.+?src=".+?'.$smallImageNum.'\.jpg.+width="'.$smallWidth.'".+height="'.$smallHeight.'".+$#ms', $html);

        preg_match('#href="(.+?)".*?src="(.+?)"#ms', $html, $matches);

        $largeSrcSize = getimagesize('http://'.Vps_Registry::get('testDomain').$matches[1]);
        $smallSrcSize = getimagesize('http://'.Vps_Registry::get('testDomain').$matches[2]);

        $this->assertEquals($largeWidth, $largeSrcSize[0]);
        $this->assertEquals($largeHeight, $largeSrcSize[1]);

        $this->assertEquals($smallWidth, $smallSrcSize[0]);
        $this->assertEquals($smallHeight, $smallSrcSize[1]);
    }
}
