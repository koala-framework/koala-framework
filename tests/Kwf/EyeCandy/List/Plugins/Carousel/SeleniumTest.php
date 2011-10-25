<?php
/**
 * @group slow
 * @group selenium
 * @group Kwf_Js
 * @group Kwf_Js_EyeCandy_List
 * @group Kwf_Js_EyeCandy_List_Plugins_Carousel
 */
class Kwf_EyeCandy_List_Plugins_Carousel_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function testPrevious()
    {
        $this->open('/kwf/test/kwf_eye-candy_list_plugins_carousel_test');
        $checkStr = '';
        $this->assertEquals($this->getText('css=.testItem'), 'Item 1');

        $this->click('css=.carouselPrevious');
        sleep(1);
        $this->assertEquals($this->getText('css=.testItem'), 'Item 6');

        $this->click('css=.carouselPrevious');
        sleep(1);
        $this->assertEquals($this->getText('css=.testItem'), 'Item 5');
    }

    public function testNext()
    {
        $this->open('/kwf/test/kwf_eye-candy_list_plugins_carousel_test');
        $checkStr = '';
        $this->assertEquals($this->getText('css=.testItem'), 'Item 1');

        $this->click('css=.carouselNext');
        sleep(1);
        $this->assertEquals($this->getText('css=.testItem'), 'Item 2');

        $this->click('css=.carouselNext');
        sleep(1);
        $this->assertEquals($this->getText('css=.testItem'), 'Item 3');
    }
}
