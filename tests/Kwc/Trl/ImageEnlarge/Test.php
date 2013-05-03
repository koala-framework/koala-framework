<?php
/**
 * @group slow
 * @group Kwc_Trl
 * @group Kwc_Trl_ImageEnlarge
 *
 * Was wo angezeigt werden soll siehe Kwc_Trl_ImageEnlarge_Master
 *
ansicht frontend:
http://fnprofile.markus.vivid/kwf/kwctest/Kwc_Trl_ImageEnlarge_Root/de/test1 (...2,3,4,5,6)
http://fnprofile.markus.vivid/kwf/kwctest/Kwc_Trl_ImageEnlarge_Root/en/test1 (...2,3,4,5,6)

backend
http://kwf.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_ImageEnlarge_Root/Kwc_Trl_ImageEnlarge_ImageEnlarge_Component/Index?componentId=root-master_test1
http://kwf.kwf.niko.vivid/kwf/componentedittest/Kwc_Trl_ImageEnlarge_Root/Kwc_Trl_ImageEnlarge_ImageEnlarge_Trl_Component.Kwc_Trl_ImageEnlarge_ImageEnlarge_Component/Index?componentId=root-en_test1
 */
class Kwc_Trl_ImageEnlarge_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_ImageEnlarge_Root');

        //master image
        Kwf_Model_Abstract::getInstance('Kwc_Trl_ImageEnlarge_ImageEnlarge_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-master_test1', 'kwf_upload_id'=>'1'),
                array('component_id'=>'root-master_test2', 'kwf_upload_id'=>'1'),
                array('component_id'=>'root-master_test3', 'kwf_upload_id'=>'1'),
                array('component_id'=>'root-master_test4', 'kwf_upload_id'=>'1'),
                array('component_id'=>'root-master_test5', 'kwf_upload_id'=>'1'),
                array('component_id'=>'root-master_test6', 'kwf_upload_id'=>'1'),
            ));

        //image trl
        Kwf_Model_Abstract::getInstance('Kwc_Trl_ImageEnlarge_ImageEnlarge_Trl_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1', 'own_image'=>0),
                array('component_id'=>'root-en_test2', 'own_image'=>0),
                array('component_id'=>'root-en_test3', 'own_image'=>1),
                array('component_id'=>'root-en_test4', 'own_image'=>1),
                array('component_id'=>'root-en_test5', 'own_image'=>1),
                array('component_id'=>'root-en_test6', 'own_image'=>0),
            ));

        //image trl own image
        Kwf_Model_Abstract::getInstance('Kwc_Trl_ImageEnlarge_ImageEnlarge_Trl_Image_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1-image', 'kwf_upload_id'=>null),
                array('component_id'=>'root-en_test2-image', 'kwf_upload_id'=>null),
                array('component_id'=>'root-en_test3-image', 'kwf_upload_id'=>'6'),
                array('component_id'=>'root-en_test4-image', 'kwf_upload_id'=>'6'),
                array('component_id'=>'root-en_test5-image', 'kwf_upload_id'=>'6'),
                array('component_id'=>'root-en_test6-image', 'kwf_upload_id'=>null),
            ));


        //master enlarge tag
        Kwf_Model_Abstract::getInstance('Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-master_test1-linkTag', 'kwf_upload_id'=>null, 'preview_image' => 0),
                array('component_id'=>'root-master_test2-linkTag', 'kwf_upload_id'=>'2', 'preview_image' => 1),
                array('component_id'=>'root-master_test3-linkTag', 'kwf_upload_id'=>null, 'preview_image' => 0),
                array('component_id'=>'root-master_test4-linkTag', 'kwf_upload_id'=>'2', 'preview_image' => 1),
                array('component_id'=>'root-master_test5-linkTag', 'kwf_upload_id'=>'2', 'preview_image' => 1),
                array('component_id'=>'root-master_test6-linkTag', 'kwf_upload_id'=>'2', 'preview_image' => 1),
            ));

        //enlarge tag trl
        Kwf_Model_Abstract::getInstance('Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1-linkTag', 'own_image'=>0),
                array('component_id'=>'root-en_test2-linkTag', 'own_image'=>0),
                array('component_id'=>'root-en_test3-linkTag', 'own_image'=>0),
                array('component_id'=>'root-en_test4-linkTag', 'own_image'=>0),
                array('component_id'=>'root-en_test5-linkTag', 'own_image'=>1),
                array('component_id'=>'root-en_test6-linkTag', 'own_image'=>1),
            ));

        //enlarge tag trl own image
        Kwf_Model_Abstract::getInstance('Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_Image_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1-linkTag-image', 'kwf_upload_id'=>null),
                array('component_id'=>'root-en_test2-linkTag-image', 'kwf_upload_id'=>null),
                array('component_id'=>'root-en_test3-linkTag-image', 'kwf_upload_id'=>null),
                array('component_id'=>'root-en_test4-linkTag-image', 'kwf_upload_id'=>null),
                array('component_id'=>'root-en_test5-linkTag-image', 'kwf_upload_id'=>'5'),
                array('component_id'=>'root-en_test6-linkTag-image', 'kwf_upload_id'=>'5'),
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

    public function testDeClearCacheNoCustomPreviewImage()
    {
        $c = $this->_root->getComponentById('root-master_test1');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);
        $row = $c->getComponent()->getRow();
        $row->kwf_upload_id = '6';
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 6, 180, 330, 6, 65, 120);
    }

    public function testDeClearCacheCustomPreviewImage()
    {
        $c = $this->_root->getComponentById('root-master_test2');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 2, 120, 101);
        $row = $c->getComponent()->getRow();
        $row->kwf_upload_id = '6';
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 6, 180, 330, 2, 120, 101);
    }

    public function testDeClearCacheChangePreviewImage()
    {
        $c = $this->_root->getComponentById('root-master_test2');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 2, 120, 101);
        $row = $c->getChildComponent('-linkTag')->getComponent()->getRow();
        $row->kwf_upload_id = '6';
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 1, 560, 560, 6, 65, 120);
    }

    public function testDeClearCacheAddPreviewImage()
    {
        $c = $this->_root->getComponentById('root-master_test1');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);
        $row = $c->getChildComponent('-linkTag')->getComponent()->getRow();
        $row->preview_image = true;
        $row->kwf_upload_id = '6';
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 1, 560, 560, 6, 65, 120);
    }

    public function testDeClearCacheRemovePreviewImage()
    {
        $c = $this->_root->getComponentById('root-master_test2');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 2, 120, 101);
        $row = $c->getChildComponent('-linkTag')->getComponent()->getRow();
        $row->preview_image = false;
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);
    }

    public function testEnClearCacheAddOwnImage()
    {
        $c = $this->_root->getComponentById('root-en_test1');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        $row = $c->getComponent()->getRow();
        $row->own_image = 1;
        $row->save();
        $row = $c->getChildComponent('-image')->getComponent()->getRow(); //own preview image in en
        $row->kwf_upload_id = '6';
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 6, 180, 330, 6, 65, 120);
    }

    /**
     * root
     *  master
     *    test1 (has image)
     *      enlargeTag
     *  en
     *    test1  (<- must display 6 after upload)
     *      enlargeTag (gets own_image set)
     *        image    (gets image uploaded)
     *      image
     */
    public function testEnClearCacheAddOwnPreviewImage()
    {
        $c = $this->_root->getComponentById('root-en_test1');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        //change own_image setting plus image
        $row = $c->getChildComponent('-linkTag')->getComponent()->getRow();
        $row->own_image = 1;
        $row->save();
        $row = $c->getChildComponent('-linkTag')->getChildComponent('-image')->getComponent()->getRow();
        $row->kwf_upload_id = '6';
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 1, 560, 560, 6, 65, 120);

        //change only the image
        $row = $c->getChildComponent('-linkTag')->getChildComponent('-image')->getComponent()->getRow();
        $row->kwf_upload_id = '1';
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        //change the image back again
        $row = $c->getChildComponent('-linkTag')->getChildComponent('-image')->getComponent()->getRow();
        $row->kwf_upload_id = '6';
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 1, 560, 560, 6, 65, 120);

        //change own_image back to false, without changing kwf_upload_id
        $row = $c->getChildComponent('-linkTag')->getComponent()->getRow();
        $row->own_image = 0;
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);
    }

    public function testEnClearCacheChangeMasterImage()
    {
        $c = $this->_root->getComponentById('root-en_test1');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        $row = $this->_root->getComponentById('root-master_test1')->getComponent()->getRow();
        $row->kwf_upload_id = '6';
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 6, 180, 330, 6, 65, 120);
    }

    public function testEnClearCacheChangeMasterPreviewImage()
    {
        $c = $this->_root->getComponentById('root-en_test2');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 2, 120, 101);

        $row = $this->_root->getComponentById('root-master_test2-linkTag')->getComponent()->getRow();
        $row->kwf_upload_id = '6';
        $row->save();
        $this->_process();
        $this->_checkTheSizes($c->render(), 1, 560, 560, 6, 65, 120);
    }

    private function _checkTheSizes($html, $largeImageNum, $largeWidth, $largeHeight, $smallImageNum, $smallWidth, $smallHeight)
    {
        // getMediaOutput aufrufen, damit Cache-Meta geschrieben wird (wegen d0cf3812b20fa19c40617ac5b08ed08a18ff808d)
        // muss so gemacht werden, weil der request über getimagesize weiter unten
        // nicht das FnF-Cache Model dieses Request schreiben kann
        preg_match_all('/.*\/media\/([\w\.]+)\/([\w\-]+)\/(\w+)\/.*/', $html, $matches);
        foreach ($matches[0] as $key => $m) {
            Kwf_Media::getOutput($matches[1][$key], $matches[2][$key], $matches[3][$key]);
        }
        preg_match('#^.*?<a.+?&quot;width&quot;:(\d+),&quot;height&quot;:(\d+).+?<img.+?src=".+?(\d+)\.jpg.+width="(\d+)".+height="(\d+)".+$#ms', $html, $matches);
        $this->assertEquals($matches[1], $largeWidth);
        $this->assertEquals($matches[2], $largeHeight);
        $this->assertEquals($matches[3], $smallImageNum);
        $this->assertEquals($matches[4], $smallWidth);
        $this->assertEquals($matches[5], $smallHeight);

        preg_match('#href="(.+?)".*?src="(.+?)"#ms', $html, $matches);
        $smallSrcSize = getimagesize('http://'.Kwf_Registry::get('testDomain').$matches[2]);
        $this->assertEquals($smallWidth, $smallSrcSize[0]);
        $this->assertEquals($smallHeight, $smallSrcSize[1]);

        $c = Kwf_Component_Data_Root::getInstance()
            ->getPageByUrl('http://'.Kwf_Registry::get('testDomain').str_replace('/kwf/kwctest/'.Kwf_Component_Data_Root::getComponentClass(), '', $matches[1]), '');
        $c->render(true, true); //first render in-process so we find the cache entry when doing a clear-cache

        //then render "real" thru http
        $largeHtml = file_get_contents('http://'.Kwf_Registry::get('testDomain').$matches[1]);
        preg_match('#class="lightboxBody.*?<img .*?src="(.*?)"#s', $largeHtml, $matches);
        $this->assertRegExp('#'.$largeImageNum.'\.jpg#', $matches[1]);
        $largeSrcSize = getimagesize('http://'.Kwf_Registry::get('testDomain').$matches[1]);
        $this->assertEquals($largeWidth, $largeSrcSize[0]);
        $this->assertEquals($largeHeight, $largeSrcSize[1]);

    }
}
