<?php
/**
 * @group Kwc_Basic_Feed
 **/
class Kwc_Basic_Feed_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_Feed_Root');
    }

    public function testFeed()
    {
        Kwf_Component_Cache::setInstance(Kwf_Component_Cache::CACHE_BACKEND_FNF);
        $feed = Kwf_Component_Data_Root::getInstance()->getChildComponent('_feed');
        $xml = $feed->getComponent()->getXml();
        $rows = Kwf_Component_Cache::getInstance()->getModel()->getRows();
        $row = $rows->current();
        $feedRow = Kwf_Model_Abstract::getInstance('Kwc_Basic_Feed_Model')->getRows()->current();

        // XML prüfen
        $this->assertEquals('<?xml', substr($xml, 0, 5));
        $this->assertTrue(strpos($xml, '<rss') !== false);
        $this->assertTrue(strpos($xml, 'testtitle') !== false);
        $this->assertTrue(strpos($xml, 'testdescription') !== false);
        $this->assertTrue(strpos($xml, 'testlink') !== false);

        // Cache-Eintrag prüfen
        $this->assertEquals($xml, $feed->getComponent()->getXml());
        $this->assertEquals(1, count($rows));
        $this->assertEquals($xml, $row->content);

        // Cache-Eintrag ändern um festzustellen, ob eh Cache verwendet wird
        $feedRow->description = 'foo';
        $feedRow->save();
        $this->assertEquals($row->content, $feed->getComponent()->getXml());

        // Cache löschen
        $this->_process();
        $xml = $feed->getComponent()->getXml();
        $this->assertEquals('<?xml', substr($xml, 0, 5));
        $this->assertTrue(strpos($xml, '<rss') !== false);
    }
}
