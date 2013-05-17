<?php
class Kwf_Component_Cache_MenuDeviceVisible_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_MenuDeviceVisible_Root_Component');
        /*
        root
          -menu
          _1
            -menu
            _2
              -menu
              _3
                -menu
          _4
            -menu
          _5 (invisible)
            -menu
         */
    }

    public function testChangePage()
    {
        $page = $this->_root->getComponentById(1);

        $html = $page->render(true, true);
        $this->assertEquals(1, substr_count($html, 'hideOnMobile'));
        Kwf_Component_Data_Root::reset();

        $row = $this->_root->getGenerator('page')->getModel()->getRow(1);
        $row->device_visible = 'onlyShowOnMobile';
        $row->save();
        $this->_process();

        $html = $page->render(true, true);

        $this->assertEquals(1, substr_count($html, 'onlyShowOnMobile'));
    }

    public function testAddPage()
    {
        $page = $this->_root->getComponentById(1);
        $page->render(true, true);

        Kwf_Component_Data_Root::reset();

        $row = $this->_root->getGenerator('page')->getModel()->createRow(array(
            'pos'=>3, 'visible'=>true, 'name'=>'f5', 'filename' => 'f5',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'device_visible' => 'onlyShowOnMobile', 'hide'=>false, 'custom_filename' => null
        ));
        $row->save();
        $this->_process();
        $html = $page->render(true, true);
        $this->assertEquals(1, substr_count($html, 'hideOnMobile'));
        $this->assertEquals(1, substr_count($html, 'onlyShowOnMobile'));
    }
}
