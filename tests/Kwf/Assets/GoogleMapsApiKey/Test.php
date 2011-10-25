<?php
/**
 * @group Assets
 */
class Kwf_Assets_GoogleMapsApiKey_Test extends Kwf_Test_TestCase
{
    public function testBuildConfigDomain()
    {
        $configDomain = Kwf_Assets_GoogleMapsApiKey::getConfigDomain('vivid-planet.com');
        $this->assertEquals('vivid-planetcom', $configDomain);

        $configDomain = Kwf_Assets_GoogleMapsApiKey::getConfigDomain('bla.vivid-planet.com');
        $this->assertEquals('vivid-planetcom', $configDomain);

        $configDomain = Kwf_Assets_GoogleMapsApiKey::getConfigDomain('vivid-planet.or.at');
        $this->assertEquals('vivid-planetorat', $configDomain);

        $configDomain = Kwf_Assets_GoogleMapsApiKey::getConfigDomain('vivid-planet.co.at');
        $this->assertEquals('vivid-planetcoat', $configDomain);

        $configDomain = Kwf_Assets_GoogleMapsApiKey::getConfigDomain('vivid-planet.gv.at');
        $this->assertEquals('vivid-planetgvat', $configDomain);

        $configDomain = Kwf_Assets_GoogleMapsApiKey::getConfigDomain('vivid-planet.co.uk');
        $this->assertEquals('vivid-planetcouk', $configDomain);
    }
}
