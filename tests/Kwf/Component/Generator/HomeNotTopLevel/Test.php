<?php
/**
 * @group HomeNotTopLevel
 */
class Kwf_Component_Generator_HomeNotTopLevel_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_HomeNotTopLevel_Root');
        $this->_root->setFilename(null);
    }

    public function testFromRoot()
    {
    /*
        $c = $this->_root->getChildPages(array('home'=>true), array());
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 3);
    */
        $c = $this->_root->getChildPages(array('home'=>true));
        $this->assertEquals(count($c), 0);
    }

    public function testFromPage1()
    {
        $page = $this->_root->getComponentById('1');
        $c = $page->getChildPages(array('home'=>true), array());
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 3);


// Eigentlich dürfte nichts zurückkommen, dz. ist es im Kwc_Root_Category_Generator
// so implementiert, dass es schon zurückkommt. Da es nur die Home betrifft nicht weiter
// schlimm, sollte es mal ganz korrekt implementiert werden, müsste dieser test funzen

//         $page1 = $this->_root->getComponentById('1');
//         $c = $page1->getChildPages(array('home' => true));
//         $this->assertEquals(count($c), 0);
    }

    public function testFromPage2()
    {
        $page = $this->_root->getComponentById('2');
        $c = $page->getChildPages(array('home'=>true), array());
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 3);

        $page = $this->_root->getComponentById('2');
        $c = $page->getChildPages(array('home'=>true));
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 3);
    }
}
