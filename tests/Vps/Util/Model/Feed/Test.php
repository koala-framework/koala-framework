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
        $this->assertTrue(strpos($feed->link, 'vivid-planet.com/news/aktuelle_news/') !== false);
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

    public function testBug1()
    {
        $feed = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')
            ->getRow('file://'.dirname(__FILE__).'/bug1.xml');
        $this->assertNotNull($feed);
        $this->assertEquals('file://'.dirname(__FILE__).'/bug1.xml', $feed->url);
        $this->assertEquals('Flux des offres issues de la base de données Emploi Local', $feed->title);
        $this->assertEquals('http://www.bae-78.com', $feed->link);
        $this->assertEquals('Les offres d\'emploi locales collectées ces 2 derniers mois par les Antennes Emploi des communes de Carrières-sur-Seine, Chatou, Croissy-sur-Seine, Le Pecq, Le Vésinet, Montesson, et par leur partenaire l\'Association Boucle Accueil Emploi (BAE).', $feed->description);

        $entries = $feed->getChildRows('Entries');
        $this->assertEquals(41, count($entries));
        $this->assertEquals('Conseillers de vente H/F', $entries->current()->title);
        $this->assertContains('impression numérique et', $entries->current()->description);
        $this->assertEquals('', $entries->current()->link);
        $this->assertEquals('2009-04-01 12:00:00', $entries->current()->date);
    }

    public function testBug2()
    {
        $feed = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')
            ->getRow('file://'.dirname(__FILE__).'/bug2.xml');
        $this->assertNotNull($feed);
        $this->assertEquals('file://'.dirname(__FILE__).'/bug2.xml', $feed->url);
        $this->assertEquals('Cultural Anthropology', $feed->title);

        $entries = $feed->getChildRows('Entries');
        $this->assertEquals(14, count($entries));
        $this->assertEquals('Assignments DUE - April 30, 2009', $entries->current()->title);
        $this->assertContains('Whether you\'ve opted to do Service Learning, an Ethnography', $entries->current()->description);
    }
    public function testBug3()
    {
        $feed = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')
            ->getRow('file://'.dirname(__FILE__).'/bug3.xml');

        $entries = $feed->getChildRows('Entries');
        $this->assertEquals(25, count($entries));
        $this->assertEquals('http://workwear1.blogspot.com/2009/04/horse-barn-maintenance.html', $entries->current()->link);
    }

    /**
     * @group slow
     */
    public function testRandomFeeds()
    {
        $urls = array();
        $urls[] = 'http://recombinomics.com/feed.xml';
        $urls[] = 'http://rss.orf.at/news.xml';
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
//                 echo ".";
                $this->assertNotEquals('', $e->title);
                $this->assertNotEquals('', $e->link);
            }
        }
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
        $feeds = array_keys($feeds);
        $this->assertEquals('http://www.vivid-planet.com/news/aktuelle_news/rss/', $feeds[0]);

        $feeds = $m->findFeeds('http://www.prosalzburg.at');
        $this->assertEquals(2, count($feeds));
        $feeds = array_keys($feeds);
        $this->assertEquals('http://www.prosalzburg.at/news/feed', $feeds[1]);
        $this->assertEquals('http://www.prosalzburg.at/forum/feed', $feeds[0]);

        $feeds = $m->findFeeds('http://www.orf.at');
        $this->assertEquals(1, count($feeds));
        $feeds = array_keys($feeds);
        $this->assertEquals('http://rss.orf.at/news.xml', $feeds[0]);
    }

    /**
     * @group slow
     * @group really_slow
     * Auskommentiert weil wirklich langsam
     *
    public function testALotOfRandomFeeds()
    {
        $urls = file(dirname(__FILE__).'/feedUrls.txt');
        foreach ($urls as $u) {
            $u = trim($u);
            echo "\n".$u."\n";
            $feed = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')
                ->getRow($u);
            echo $feed->encoding.' ';
            if ($u != 'http://www.ds-girls.com/forum/syndication.php?type=rss.php?count=3&fid=4'
                && $u != 'http://www.nst.com.my/Current_News/NST/rss/allSec') {
                $this->assertNotEquals('', $feed->title);
            }
            $this->assertNotEquals('', $feed->url);
            if ($u != 'http://feeds.feedburner.com/aol/movies/newintheaters'
                && $u != 'http://feeds.feedburner.com/beehive-govt-nz/updates'
                && $u != 'http://stop.hu/dumps/?format=rss&type=all'
                && $u != 'http://www.google.com/alerts/feeds/02158335529507078511/15158246043646228330'
                && $u != 'http://www.google.com/trends/hottrends/atom/hourly'
                && $u != 'http://www.game4fun.it/rss.xml'
                && $u != 'http://www.pcguru.hu/pcguru/rss/rss.xml'
                && $u != 'http://www.veoliawater.com/atom/news.php'
                && $u != 'http://www.wasterecyclingnews.com/rss.php') {
                $this->assertNotEquals('', $feed->link);
            }
            $entries = $feed->getChildRows('Entries');
            $this->assertTrue(count($entries) > 0);
            $numWithoutTitle = 0;
            foreach ($entries as $e) {
                echo ".";
                if (trim($e->title) == '') {
                    $numWithoutTitle++;
                }
                if ($e->title == 'Unable to read blog post') continue;
                if ($u != 'http://emplocal.fdeho.com/arss.php?x=cr32'
                   && $u != 'http://www.google.com/trends/hottrends/atom/hourly'
                   && $u != 'http://www.surgeryvaluer.co.uk/intranetnews.xml'
                   && $u != 'http://208.112.114.232/XML/AdkReviewBoard.xml') {
                    $this->assertNotEquals('', $e->link);
                }
            }
            if ($numWithoutTitle > 1 && $u != 'http://antwrp.gsfc.nasa.gov/apod.rss') {
                $this->fail("$numWithoutTitle entries without title");
            }
        }
    }
    */

    public function testLarge()
    {
        $this->assertEquals(120, substr_count(file_get_contents(dirname(__FILE__).'/large.xml'), '<item>'));
        $feed = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')
            ->getRow('file://'.dirname(__FILE__).'/large.xml');
        $this->assertNotNull($feed);

        $entries = $feed->getChildRows('Entries');
        $this->assertEquals(120, count($entries));
    }

    public function testLargeWithLimit()
    {
        $feed = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')
            ->getRow('file://'.dirname(__FILE__).'/large.xml');
        $this->assertNotNull($feed);

        $s = Vps_Model_Abstract::getInstance('Vps_Util_Model_Feed_Feeds')->select();
        $s->limit(50);
        $entries = $feed->getChildRows('Entries', $s);
        $this->assertEquals(50, count($entries));
    }
}
