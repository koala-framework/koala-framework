<?php
/**
 * @group Feed
 */
class Vps_Util_Model_Feed_Test extends PHPUnit_Framework_TestCase
{
    public function testRss20()
    {
        $feed = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')
            ->getRow('file://'.dirname(__FILE__).'/rss2.0.xml');
        $this->assertNotNull($feed);
        $this->assertEquals('file://'.dirname(__FILE__).'/rss2.0.xml', $feed->url);
        $this->assertEquals('Internet Agentur Salzburg - Vivid Planet Software News', $feed->title);
        $this->assertEquals('http://www.vivid-planet.com/news/aktuelle_news/', $feed->link);
        $this->assertEquals('Die aktuellen News der Vivid Planet Software GmbH', $feed->description);

        $entries = $feed->getChildRows('Entries');
        $this->assertEquals(4, count($entries));
        $this->assertEquals('prosalzburg.at: Jobvermittler und Immobilienmakler profitieren', $entries->current()->title);
        $this->assertContains('Jobvermittler und Immobilienmakler haben einiges gemein', $entries->current()->description);
        $this->assertEquals('http://www.vivid-planet.com/news/aktuelle_news/2009/03/prosalzburg_at_jobvermittler_und_immobilienmakler_/', $entries->current()->link);
        $this->assertEquals('2009-03-02 00:00:00', $entries->current()->date);

        $feed = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')
            ->getRow('http://www.vivid-planet.com/news/aktuelle_news/rss/');
        $this->assertNotNull($feed);
        $entries = $feed->getChildRows('Entries');
        $this->assertTrue(count($entries) > 3);

        
    }
    public function testAtom()
    {
        $feed = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')
            ->getRow('file://'.dirname(__FILE__).'/atom.xml');
        $this->assertNotNull($feed);
        $this->assertEquals('file://'.dirname(__FILE__).'/atom.xml', $feed->url);
        $this->assertEquals('Example Feed', $feed->title);
        $this->assertEquals('http://example.org/', $feed->link);
        $this->assertEquals('', $feed->description);

        $entries = $feed->getChildRows('Entries');
        $this->assertEquals(1, count($entries));
        $this->assertEquals('Atom-Powered Robots Run Amok', $entries->current()->title);
        $this->assertEquals('Some text.', $entries->current()->description);
        $this->assertEquals('http://example.org/2003/12/13/atom03', $entries->current()->link);
        $this->assertEquals('2003-12-13 19:30:02', $entries->current()->date);
    }
    /**
     * @group slow
     */
    public function testRandomFeeds()
    {
        $urls = array();
        $urls[] = 'http://www.csmonitor.com/rss/top.rss';
        $urls[] = 'http://productnews.userland.com/newsItems/departments/radioUserland.xml';
        $urls[] = 'http://planetkde.org/rss20.xml';
        $urls[] = 'http://aseigo.blogspot.com/feeds/posts/default';
        $urls[] = 'http://aseigo.blogspot.com/feeds/posts/default?alt=rss';
        foreach ($urls as $u) {
            //echo "\n".$u."\n";
            $feed = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')
                ->getRow($u);
            $this->assertNotEquals('', $feed->title);
            $this->assertNotEquals('', $feed->url);
            $this->assertNotEquals('', $feed->link);
            $entries = $feed->getChildRows('Entries');
            $this->assertTrue(count($entries) > 4);
            foreach ($entries as $e) {
                //echo ".";
                $this->assertNotEquals('', $e->title);
                $this->assertNotEquals('', $e->link);
                $this->assertNotEquals('', $e->date);
            }
        }
    }

    public function testSerialize()
    {
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();
        Vps_Model_Abstract::clearInstances();
        $feed = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')
            ->getRow('file://'.dirname(__FILE__).'/rss2.0.xml');
        $entries = $feed->getChildRows('Entries');
        $this->assertEquals(4, count($entries));
        $e = $entries->current();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('loaded feed'));

        //einen entry serialisieren
        $e = unserialize(serialize($e));
        $this->assertEquals('prosalzburg.at: Jobvermittler und Immobilienmakler profitieren', $e->title);
        $this->assertContains('Jobvermittler und Immobilienmakler haben einiges gemein', $e->description);
        $this->assertEquals('http://www.vivid-planet.com/news/aktuelle_news/2009/03/prosalzburg_at_jobvermittler_und_immobilienmakler_/', $e->link);
        $this->assertEquals('2009-03-02 00:00:00', $e->date);

        //entries rowset serialisieren
        $entries = unserialize(serialize($entries));
        $this->assertEquals(4, count($entries));
        $this->assertEquals('prosalzburg.at: Jobvermittler und Immobilienmakler profitieren', $entries->current()->title);

        //kompletten feed serialisieren
        $feed = unserialize(serialize($feed));
        $this->assertEquals('file://'.dirname(__FILE__).'/rss2.0.xml', $feed->url);
        $this->assertEquals('Internet Agentur Salzburg - Vivid Planet Software News', $feed->title);
        $this->assertEquals('http://www.vivid-planet.com/news/aktuelle_news/', $feed->link);
        $this->assertEquals('Die aktuellen News der Vivid Planet Software GmbH', $feed->description);
        $entries = $feed->getChildRows('Entries');
        $this->assertEquals(4, count($entries));
        $this->assertEquals('prosalzburg.at: Jobvermittler und Immobilienmakler profitieren', $entries->current()->title);

        $this->assertEquals(1, Vps_Benchmark::getCounterValue('loaded feed'));
    }
}
