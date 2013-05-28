<?php
/**
 * @group DownloadTag
 * @group DownloadTagLogin
 * @group slow
 * @group selenium
 */
class Kwc_Basic_DownloadTagBehindLogin_Test extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwc_Basic_DownloadTagBehindLogin_Root');
        parent::setUp();
    }

    public function testIt()
    {
        $this->openKwc('/test');

        //nicht eingeloggt, link darf nicht da sein
        $this->assertElementNotPresent("link=test");

        //login formular muss da sein
        $this->assertElementPresent("css=input[name=\"login_password\"]");

        //einloggen
        $this->type("css=input[name=\"login_password\"]", "planet");

        $this->submitAndWait("css=form");

        //login formular muss weg sein
        $this->assertElementNotPresent("css=input[name=\"login_password\"]");

        //link muss da sein
        $this->assertElementPresent("link=test");

        //test ob der link geht
        $this->clickAndWait("link=test");
        $downloadLocation = $this->getLocation();

        //noch immer eingeloggt, link muss noch da sein
        $this->openKwc('/test');
        $this->assertElementPresent("link=test");

        //ausloggen, link darf nicht mehr da sein
        $this->sessionRestart();
        $this->openKwc('/test');
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
