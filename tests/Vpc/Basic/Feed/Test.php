<?php
/**
 * @group Vpc_Basic_Feed
 **/
class Vpc_Basic_Feed_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_Feed_Root');
    }

    public function testFeed()
    {
        Vps_Component_Cache::setInstance(Vps_Component_Cache::CACHE_BACKEND_FNF);
        $feed = Vps_Component_Data_Root::getInstance()->getChildComponent('_feed');
        $xml = $feed->getComponent()->getXml();
        $rows = Vps_Component_Cache::getInstance()->getModel()->getRows();
        $row = $rows->current();
        $feedRow = Vps_Model_Abstract::getInstance('Vpc_Basic_Feed_Model')->getRows()->current();

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
        Vps_Component_Cache::getInstance()->cleanByRow($feedRow);
        $xml = $feed->getComponent()->getXml();
        $this->assertEquals('<?xml', substr($xml, 0, 5));
        $this->assertTrue(strpos($xml, '<rss') !== false);
    }
}
