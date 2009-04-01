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
    }

    public function testRss10()
    {
        $feed = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')
            ->getRow('file://'.dirname(__FILE__).'/rss1.0.xml');
        $this->assertNotNull($feed);
        $this->assertEquals('file://'.dirname(__FILE__).'/rss1.0.xml', $feed->url);
        $this->assertEquals('news.ORF.at', $feed->title);
        $this->assertEquals('http://news.orf.at', $feed->link);
        $this->assertEquals('Mehr als 30 Mal am Tag aktualisiert die ORF.at-Redaktion Nachrichten aus Österreich und aller Welt. Aktuelles rund um die Uhr in optimaler Mischung aus Text, Bild und Ton.', $feed->description);

        $entries = $feed->getChildRows('Entries');
        $this->assertEquals(35, count($entries));
        $this->assertEquals('TA muss 1,5 Millionen Euro Strafe zahlen', $entries->current()->title);
        $this->assertEquals('', $entries->current()->description);
        $this->assertEquals('http://news.orf.at/ticker/322523.html', $entries->current()->link);
        $this->assertEquals('2009-03-26 17:17:20', $entries->current()->date);
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
        $urls[] = 'http://rss.orf.at/news.xml';
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

    /**
     * @group slow
     */
    public function testFindFeeds()
    {
        Vps_Model_Abstract::clearInstances();
        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds');
        $feeds = $m->findFeeds('http://www.vivid-planet.com');
        $this->assertEquals(1, count($feeds));
        $this->assertEquals('http://www.vivid-planet.com/news/aktuelle_news/rss/', $feeds[0]->url);

        $feeds = $m->findFeeds('http://www.prosalzburg.at');
        $this->assertEquals(2, count($feeds));
        $this->assertEquals('http://www.prosalzburg.at/news/feed', $feeds[1]->url);
        $this->assertEquals('http://www.prosalzburg.at/forum/feed', $feeds[0]->url);

        $feeds = $m->findFeeds('http://www.orf.at');
        $this->assertEquals(1, count($feeds));
        $this->assertEquals('http://rss.orf.at/news.xml', $feeds[0]->url);
    }
}
