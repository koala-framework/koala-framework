<?php
/**
 * @group slow
 * @group Vpc_Trl
 * @group Vpc_Trl_ImageEnlarge
 *
 * Was wo angezeigt werden soll siehe Vpc_Trl_ImageEnlarge_Master
 *
ansicht frontend:
http://fnprofile.markus.vivid/vps/vpctest/Vpc_Trl_ImageEnlarge_Root/de/test1 (...2,3,4,5,6)
http://fnprofile.markus.vivid/vps/vpctest/Vpc_Trl_ImageEnlarge_Root/en/test1 (...2,3,4,5,6)

backend
http://vps.vps.niko.vivid/vps/componentedittest/Vpc_Trl_ImageEnlarge_Root/Vpc_Trl_ImageEnlarge_ImageEnlarge_Component/Index?componentId=root-master_test1
http://vps.vps.niko.vivid/vps/componentedittest/Vpc_Trl_ImageEnlarge_Root/Vpc_Trl_ImageEnlarge_ImageEnlarge_Trl_Component.Vpc_Trl_ImageEnlarge_ImageEnlarge_Component/Index?componentId=root-en_test1
 */
class Vpc_Trl_ImageEnlarge_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_ImageEnlarge_Root');

        //master image
        Vps_Model_Abstract::getInstance('Vpc_Trl_ImageEnlarge_ImageEnlarge_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-master_test1', 'vps_upload_id'=>'1'),
                array('component_id'=>'root-master_test2', 'vps_upload_id'=>'1'),
                array('component_id'=>'root-master_test3', 'vps_upload_id'=>'1'),
                array('component_id'=>'root-master_test4', 'vps_upload_id'=>'1'),
                array('component_id'=>'root-master_test5', 'vps_upload_id'=>'1'),
                array('component_id'=>'root-master_test6', 'vps_upload_id'=>'1'),
            ));

        //image trl
        Vps_Model_Abstract::getInstance('Vpc_Trl_ImageEnlarge_ImageEnlarge_Trl_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1', 'own_image'=>0),
                array('component_id'=>'root-en_test2', 'own_image'=>0),
                array('component_id'=>'root-en_test3', 'own_image'=>1),
                array('component_id'=>'root-en_test4', 'own_image'=>1),
                array('component_id'=>'root-en_test5', 'own_image'=>1),
                array('component_id'=>'root-en_test6', 'own_image'=>0),
            ));

        //image trl own image
        Vps_Model_Abstract::getInstance('Vpc_Trl_ImageEnlarge_ImageEnlarge_Trl_Image_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test2-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test3-image', 'vps_upload_id'=>'6'),
                array('component_id'=>'root-en_test4-image', 'vps_upload_id'=>'6'),
                array('component_id'=>'root-en_test5-image', 'vps_upload_id'=>'6'),
                array('component_id'=>'root-en_test6-image', 'vps_upload_id'=>null),
            ));


        //master enlarge tag
        Vps_Model_Abstract::getInstance('Vpc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-master_test1-linkTag', 'vps_upload_id'=>null, 'preview_image' => 0),
                array('component_id'=>'root-master_test2-linkTag', 'vps_upload_id'=>'2', 'preview_image' => 1),
                array('component_id'=>'root-master_test3-linkTag', 'vps_upload_id'=>null, 'preview_image' => 0),
                array('component_id'=>'root-master_test4-linkTag', 'vps_upload_id'=>'2', 'preview_image' => 1),
                array('component_id'=>'root-master_test5-linkTag', 'vps_upload_id'=>'2', 'preview_image' => 1),
                array('component_id'=>'root-master_test6-linkTag', 'vps_upload_id'=>'2', 'preview_image' => 1),
            ));

        //enlarge tag trl
        Vps_Model_Abstract::getInstance('Vpc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1-linkTag', 'own_image'=>0),
                array('component_id'=>'root-en_test2-linkTag', 'own_image'=>0),
                array('component_id'=>'root-en_test3-linkTag', 'own_image'=>0),
                array('component_id'=>'root-en_test4-linkTag', 'own_image'=>0),
                array('component_id'=>'root-en_test5-linkTag', 'own_image'=>1),
                array('component_id'=>'root-en_test6-linkTag', 'own_image'=>1),
            ));

        //enlarge tag trl own image
        Vps_Model_Abstract::getInstance('Vpc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_Image_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1-linkTag-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test2-linkTag-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test3-linkTag-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test4-linkTag-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test5-linkTag-image', 'vps_upload_id'=>'5'),
                array('component_id'=>'root-en_test6-linkTag-image', 'vps_upload_id'=>'5'),
            ));
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

    public function testDeClearCache()
    {
        $c = $this->_root->getComponentById('root-master_test1');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);
        $row = $c->getComponent()->getRow();
        $row->vps_upload_id = '6';
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 6, 180, 330, 6, 65, 120);
    }

    public function testEnClearCache()
    {
        $c = $this->_root->getComponentById('root-en_test1');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);
        $row = $c->getComponent()->getRow();
        $row->own_image = 1;
        $row->save();
        $row = $this->_root->getComponentById('root-en_test1-image')->getComponent()->getRow();
        $row->vps_upload_id = '6';
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 6, 180, 330, 6, 65, 120);
    }

    private function _checkTheSizes($html, $largeImageNum, $largeWidth, $largeHeight, $smallImageNum, $smallWidth, $smallHeight)
    {
        // getMediaOutput aufrufen, damit Cache-Meta geschrieben wird (wegen d0cf3812b20fa19c40617ac5b08ed08a18ff808d)
        // muss so gemacht werden, weil der request über getimagesize weiter unten
        // nicht das FnF-Cache Model dieses Request schreiben kann
        preg_match_all('/.*\/media\/([\w\.]+)\/([\w\-]+)\/(\w+)\/.*/', $html, $matches);
        foreach ($matches[0] as $key => $m) {
            $class = $matches[1][$key];
            $classWithoutDot = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
            call_user_func(array($classWithoutDot, 'getMediaOutput'), $matches[2][$key], $matches[3][$key], $class);
        }

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
