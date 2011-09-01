<?php
/**
 * @group DownloadTag
 * @group DownloadTagLogin
 * @group slow
 * @group selenium
 */
class Vpc_Basic_DownloadTagBehindLogin_Test extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Basic_DownloadTagBehindLogin_Root');
        parent::setUp();
    }

    public function testIt()
    {
        $this->openVpc('/test');

        //nicht eingeloggt, link darf nicht da sein
        $this->assertElementNotPresent("link=test");

        //login formular muss da sein
        $this->assertElementPresent("css=div.login_password input");

        //einloggen
        $this->type("css=div.login_password input", "planet");

        $this->submitAndWait("css=form");

        //login formular muss weg sein
        $this->assertElementNotPresent("css=div.login_password input");

        //link muss da sein
        $this->assertElementPresent("link=test");

        //test ob der link geht
        $this->clickAndWait("link=test");
        $downloadLocation = $this->getLocation();

        //noch immer eingeloggt, link muss noch da sein
        $this->openVpc('/test');
        $this->assertElementPresent("link=test");

        //ausloggen, link darf nicht mehr da sein
        $this->deleteAllVisibleCookies();
        $this->refreshAndWait();
        $this->assertElementNotPresent("link=test");

        $ok = false;
        try {
            file_get_contents($downloadLocation);
        } catch (Exception $e) {
            //todo auf 403 überprüfen
            $ok = true;
        }
        if (!$ok) {
            $this->fail("file was accesible although logged out");
        }
    }
}
