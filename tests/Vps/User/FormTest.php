<?php
/**
 * @group slow
 * @group selenium
 * @group User
 * @group User_Form
 */
class Vps_User_FormTest extends Vps_Test_SeleniumTestCase
{
    public function test()
    {
        $email = 'seltest@vivid-planet.com';

        $this->open('/vps/test/vps_user_form');
        $this->waitForConnections();
        $this->type("//input[contains(@name, 'email')]", $email);
        $this->type("//input[contains(@name, 'firstname')]", 'Sel');
        $this->type("//input[contains(@name, 'lastname')]", 'Test');
        $this->type("//input[contains(@name, 'title')]", 'ttl');
        $this->click("//img[contains(@class, 'x-form-arrow-trigger')]");
        $this->click("//div[contains(@class, 'x-combo-list-inner')]/div[contains(@class, 'x-combo-list-item')]");
        $this->click("//button[contains(@class, 'x-btn-text')]");

        sleep(1);
        $this->waitForConnections();
    }

}
