<?php
/**
 * Testet ob cli und http zugriffe eh auch nur alle x minuten syncen
 * es gab ein problem mit berechtigungen
 * @group Model_MirrorCache
 * @group Model_MirrorCache_Delay
 * @group slow
 */
class Kwf_Model_MirrorCache_DelayWithHttpAndCliTest extends Kwf_Test_TestCase
{
    public function testSyncWithCli()
    {
        Kwf_Benchmark::enable();
        Kwf_Benchmark::reset();

        Kwf_Model_MirrorCache_TestController::setup();
        Kwf_Model_MirrorCache_TestController::$proxyModel->synchronize(Kwf_Model_MirrorCache::SYNC_ONCE);
        $this->assertEquals(1, (int)Kwf_Benchmark::getCounterValue('mirror sync'));

        $url = 'http://'.Kwf_Registry::get('testDomain').'/kwf/test/kwf_model_mirror-cache_test';
        $this->assertEquals(0, file_get_contents($url));

        sleep(6); //sync delay ist 5 sec
        $this->assertEquals(1, file_get_contents($url));
        $this->assertEquals(0, file_get_contents($url));

        Kwf_Benchmark::reset();
        Kwf_Model_MirrorCache_TestController::$proxyModel->countRows();
        $this->assertEquals(0, (int)Kwf_Benchmark::getCounterValue('mirror sync'));

        sleep(6); //sync delay ist 5 sec
        Kwf_Benchmark::reset();
        Kwf_Model_MirrorCache_TestController::$proxyModel->synchronize(Kwf_Model_MirrorCache::SYNC_ALWAYS);
        $this->assertEquals(1, (int)Kwf_Benchmark::getCounterValue('mirror sync'));
        $this->assertEquals(0, file_get_contents($url));
    }
}
