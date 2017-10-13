<?php
/**
 * @group slow
 * @group Kwc_Trl
 * @group Kwc_Trl_ImageEnlarge
 * @group Image
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
                array('component_id'=>'root-master_test7', 'kwf_upload_id'=>'1'),
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
                array('component_id'=>'root-en_test7', 'own_image'=>0),
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
                array('component_id'=>'root-en_test7-image', 'kwf_upload_id'=>'3'),
                array('component_id'=>'root-en_test7-image', 'kwf_upload_id'=>'3'),
            ));


        //master enlarge tag
        Kwf_Model_Abstract::getInstance('Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-master_test1-linkTag'),
                array('component_id'=>'root-master_test2-linkTag'),
                array('component_id'=>'root-master_test3-linkTag'),
                array('component_id'=>'root-master_test4-linkTag'),
                array('component_id'=>'root-master_test5-linkTag'),
                array('component_id'=>'root-master_test6-linkTag'),
                array('component_id'=>'root-master_test7-linkTag'),
            ));

        //enlarge tag trl
        Kwf_Model_Abstract::getInstance('Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1-linkTag'),
                array('component_id'=>'root-en_test2-linkTag'),
                array('component_id'=>'root-en_test3-linkTag'),
                array('component_id'=>'root-en_test4-linkTag'),
                array('component_id'=>'root-en_test5-linkTag'),
                array('component_id'=>'root-en_test6-linkTag'),
                array('component_id'=>'root-en_test7-linkTag'),
            ));

        //enlarge tag trl own image
        Kwf_Model_Abstract::getInstance('Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_Image_TestModel')
            ->getProxyModel()->setData(array(
                array('component_id'=>'root-en_test1-linkTag-image'),
                array('component_id'=>'root-en_test2-linkTag-image'),
                array('component_id'=>'root-en_test3-linkTag-image'),
                array('component_id'=>'root-en_test4-linkTag-image'),
                array('component_id'=>'root-en_test5-linkTag-image'),
                array('component_id'=>'root-en_test6-linkTag-image'),
                array('component_id'=>'root-en_test7-linkTag-image'),
            ));
    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_test1');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        $c = $this->_root->getComponentById('root-master_test2');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        $c = $this->_root->getComponentById('root-master_test3');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        $c = $this->_root->getComponentById('root-master_test4');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        $c = $this->_root->getComponentById('root-master_test5');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        $c = $this->_root->getComponentById('root-master_test6');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);
    }

    public function testEn()
    {
        $c = $this->_root->getComponentById('root-en_test1');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        $c = $this->_root->getComponentById('root-en_test2');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);

        $c = $this->_root->getComponentById('root-en_test3');
        $this->_checkTheSizes($c->render(), 6, 180, 330, 6, 65, 120);

        $c = $this->_root->getComponentById('root-en_test4');
        $this->_checkTheSizes($c->render(), 6, 180, 330, 6, 65, 120);

        $c = $this->_root->getComponentById('root-en_test5');
        $this->_checkTheSizes($c->render(), 6, 180, 330, 6, 65, 120);

        $c = $this->_root->getComponentById('root-en_test6');
        $this->_checkTheSizes($c->render(), 1, 560, 560, 1, 120, 120);
    }

    public function testDeImageEnlargeDimensions()
    {
        $c = $this->_root->getComponentById('root-master_test2-linkTag');
        $dim = $c->getComponent()->getImageDimensions();
        $this->assertEquals(560, $dim['width']);
        $this->assertEquals(560, $dim['height']);
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

        $row = $c->getComponent()->getRow();
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

    private function _getImageFromHtml($html)
    {
        preg_match_all('#/media/([^/]+)/([^/]+)/([^/]+)#', $html, $matches);
        foreach ($matches[0] as $key => $m) {
            if (substr($matches[3][$key], 0, 10) == 'dh-{width}') continue;
            return Kwf_Media::getOutput($matches[1][$key], $matches[2][$key], $matches[3][$key]);
        }
        return null;
    }

    public function testDeChangeImageMedia_CacheOfEnlargeTagTrlDeletedWithSameWidth()
    {
        $imageEnlargeTrlComponent = $this->_root->getComponentById('root-en_test1');
        $c = $imageEnlargeTrlComponent
                ->getChildComponent('-linkTag')
                ->getChildComponent('_imagePage');
        $imageWithNumber1 = $this->_getImageFromHtml($c->render());

        // Gets row of Master-Component, changes images
        $row = $imageEnlargeTrlComponent->chained->getComponent()->getRow();
        $row->kwf_upload_id = 3;
        $row->save();
        $this->_process();
        $imageWithNumber3 = $this->_getImageFromHtml($c->render());

        $image1 = new Imagick($imageWithNumber1['file']);
        $image3 = new Imagick($imageWithNumber3['file']);

        $this->assertEquals(560, $image1->getImageWidth());
        $this->assertEquals(320, $image3->getImageHeight());
    }

    public function testDeChangeImageMedia_CacheOfEnlargeTagTrlDeletedWithSameWidth2()
    {
        $imageEnlargeTrlComponent = $this->_root->getComponentById('root-en_test1');
        $c = $imageEnlargeTrlComponent
                ->getChildComponent('-linkTag')
                ->getChildComponent('_imagePage');
        $imageWithNumber1 = $this->_getImageFromHtml($c->render());

        $row = $imageEnlargeTrlComponent->getComponent()->getRow();
        $row->own_image = 1;
        $row->save();
        $row = $imageEnlargeTrlComponent->getChildComponent('-image')->getComponent()->getRow(); //own preview image in en
        $row->kwf_upload_id = '3';
        $row->save();
        $this->_process();
        $imageWithNumber3FromTrl = $this->_getImageFromHtml($c->render());

        $image1 = new Imagick($imageWithNumber1['file']);
        $image3 = new Imagick($imageWithNumber3FromTrl['file']);

        $this->assertEquals(560, $image1->getImageWidth());
        $this->assertEquals(560, $image3->getImageWidth());
        $this->assertEquals(320, $image3->getImageHeight());
    }

    public function testEnChangeOwnImageMedia_CacheOfEnlargeTagTrlDeletedWhenSameWidth()
    {
        $imageEnlargeTrlComponent = $this->_root->getComponentById('root-en_test7');
        $c = $imageEnlargeTrlComponent
                ->getChildComponent('-linkTag')
                ->getChildComponent('_imagePage');
        $imageWithNumber1 = $this->_getImageFromHtml($c->render());

        // Gets row of trl-component, adds image
        $row = $imageEnlargeTrlComponent->getComponent()->getRow();
        $row->own_image = 1;
        $row->save();
        $this->_process();
        $imageWithNumber3 = $this->_getImageFromHtml($c->render());

        $image1 = new Imagick($imageWithNumber1['file']);
        $image3 = new Imagick($imageWithNumber3['file']);
        $this->assertEquals($image1->getImageWidth(), 560);
        $this->assertEquals($image1->getImageHeight(), 560);
        $this->assertEquals($image3->getImageWidth(), 560);
        $this->assertEquals($image3->getImageHeight(), 320);
    }

    public function testEnChangeOwnImageMedia_CacheOfEnlargeTagTrlDeletedWhenSameWidth2()
    {
        $imageEnlargeTrlComponent = $this->_root->getComponentById('root-en_test3');
        $c = $imageEnlargeTrlComponent
                ->getChildComponent('-linkTag')
                ->getChildComponent('_imagePage');
        $imageWithNumber6 = $this->_getImageFromHtml($c->render());

        // Gets row of trl-component, adds image
        $row = $imageEnlargeTrlComponent->getComponent()->getRow();
        $row->own_image = 0;
        $row->save();
        $this->_process();
        $imageWithNumber1 = $this->_getImageFromHtml($c->render());

        $image6 = new Imagick($imageWithNumber6['file']);
        $image1 = new Imagick($imageWithNumber1['file']);
        $this->assertEquals($image6->getImageWidth(), 180);
        $this->assertEquals($image6->getImageHeight(), 330);
        $this->assertEquals($image1->getImageWidth(), 560);
        $this->assertEquals($image1->getImageHeight(), 560);
    }

    public function testEnChangeOwnImageAndSetUploadMedia_CacheOfEnlargeTagTrlDeletedWhenSameWidth()
    {
        $imageEnlargeTrlComponent = $this->_root->getComponentById('root-en_test1');
        $c = $imageEnlargeTrlComponent
                ->getChildComponent('-linkTag')
                ->getChildComponent('_imagePage');
        $imageWithNumber1 = $this->_getImageFromHtml($c->render());

        // Gets row of trl-component, adds image
        $row = $imageEnlargeTrlComponent->getComponent()->getRow();
        $row->own_image = 1;
        $row->save();
        $row = $imageEnlargeTrlComponent->getChildComponent('-image')->getComponent()->getRow(); //own preview image in en
        $row->kwf_upload_id = '3';
        $row->save();
        $this->_process();
        $imageWithNumber3 = $this->_getImageFromHtml($c->render());

        $image1 = new Imagick($imageWithNumber1['file']);
        $image3 = new Imagick($imageWithNumber3['file']);
        $this->assertNotEquals($image1->getImageHeight(), $image3->getImageHeight());
        $this->assertEquals($image1->getImageWidth(), 560);
        $this->assertEquals($image1->getImageHeight(), 560);
        $this->assertEquals($image3->getImageWidth(), 560);
        $this->assertEquals($image3->getImageHeight(), 320);
    }

    private function _checkTheSizes($html, $largeImageNum, $largeWidth, $largeHeight, $smallImageNum, $smallWidth, $smallHeight)
    {
        // getMediaOutput aufrufen, damit Cache-Meta geschrieben wird (wegen d0cf3812b20fa19c40617ac5b08ed08a18ff808d)
        // muss so gemacht werden, weil der request über getimagesize weiter unten
        // nicht das FnF-Cache Model dieses Request schreiben kann
        preg_match_all('#/media/([^/]+)/([^/]+)/([^/]+)#', $html, $matches);
        foreach ($matches[0] as $key => $m) {
            if (substr($matches[3][$key], 0, 10) == 'dh-{width}') continue;
            Kwf_Media::getOutput($matches[1][$key], $matches[2][$key], $matches[3][$key]);
        }
        preg_match('#^.*?<a.+?&quot;width&quot;:(\d+),&quot;height&quot;:(\d+).+?<img.+?src=".+?(\d+)\.jpg.+width="(\d+)".+height="(\d+)".+$#ms', $html, $matches);
        $this->assertEquals($matches[1], $largeWidth);
        $this->assertEquals($matches[2], $largeHeight);
        $this->assertEquals($matches[3], $smallImageNum);
        $this->assertEquals($matches[4], $smallWidth);
        $this->assertEquals($matches[5], $smallHeight);

        preg_match('#href="(.+?)".*? src="(.+?)"#ms', $html, $matches);
        $smallSrcSize = getimagesize('http://'.Kwf_Registry::get('testDomain').$matches[2]);
        $this->assertEquals($smallWidth, $smallSrcSize[0]);
        $this->assertEquals($smallHeight, $smallSrcSize[1]);

        preg_match('#data-kwc-lightbox="([^"]*)"#ms', $html, $matches);
        $lightboxData = json_decode(Kwf_Util_HtmlSpecialChars::decode($matches[1]), true);

        $c = Kwf_Component_Data_Root::getInstance()
            ->getPageByUrl('http://'.Kwf_Registry::get('testDomain').str_replace('/kwf/kwctest/'.Kwf_Component_Data_Root::getComponentClass(), '', $lightboxData['lightboxUrl']), '');
        $c->render(true, true); //first render in-process so we find the cache entry when doing a clear-cache

        //then render "real" thru http
        $largeHtml = file_get_contents('http://'.Kwf_Registry::get('testDomain').$lightboxData['lightboxUrl']);
        preg_match('#class="lightboxBody.*?<img .*?src="(.*?)"#s', $largeHtml, $matches);
        $this->assertRegExp('#'.$largeImageNum.'\.jpg#', $matches[1]);
        $largeSrcSize = getimagesize('http://'.Kwf_Registry::get('testDomain').$matches[1]);
        $this->assertEquals($largeWidth, $largeSrcSize[0]);
        $this->assertEquals($largeHeight, $largeSrcSize[1]);
    }
}
