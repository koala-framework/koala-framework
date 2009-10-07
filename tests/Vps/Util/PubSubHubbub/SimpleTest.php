<?php
/**
 * @group PubSubHubbub
 * @group slow
 */
class Vps_Util_PubSubHubbub_SimpleTest extends Vps_Util_PubSubHubbub_AbstractTest
{
    public function testIt()
    {
        $domain = Vps_Registry::get('config')->server->domain;
        $urlPrefix = 'http://'.$domain.'/vps/test';

        $s = new Vps_Util_PubSubHubbub_Subscriber($this->_hubUrl.'/');
        $s->setCallbackUrl($urlPrefix.'/vps_util_pub-sub-hubbub_callback?id='.$this->_testId);
        $feed = $urlPrefix.'/vps_util_pub-sub-hubbub_test-feed?id='.$this->_testId;
        $this->assertEquals(202, $s->subscribe($feed)->getStatus());

        $this->_runHubTasks('subscriptions');
        $this->_runHubTasks('mappings');
        $this->assertFeedRequested(1);

        $this->_writeTestFeedContent(2);
        $this->_publishTestFeedUpdate();

        $this->_runHubTasks('feed-pulls');
        $this->_runHubTasks('event-delivery');
        $this->assertFeedRequested(2);

        $newC = file_get_contents('/tmp/lastCallback'.$this->_testId);
        $this->assertTrue(strpos($newC, '<title>blub2</title>') !== false);
        //$this->assertEquals(1, substr_count($newC, '<entry>'));
    }
}
