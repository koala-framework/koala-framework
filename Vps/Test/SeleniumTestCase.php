<?php
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class Vps_Test_SeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase
{
    public static $browsers = array(
    /*
      array(
        'name'    => 'Firefox on Linux',
        'browser' => '*firefox /usr/lib/firefox/firefox-bin',
        'host'    => 'my.linux.box',
        'port'    => 4444,
        'timeout' => 30000,
      ),
      array(
        'name'    => 'Safari on MacOS X',
        'browser' => '*safari',
        'host'    => 'my.macosx.box',
        'port'    => 4444,
        'timeout' => 30000,
      ),
      array(
        'name'    => 'Safari on Windows XP',
        'browser' => '*custom C:\Programme\Safari\Safari.exe -url',
        'host'    => 'vivid-test',
        'port'    => 4444,
        'timeout' => 30000,
      ),
      */
      array(
        'name'    => 'Internet Explorer on Windows XP',
        'browser' => '*iexplore',
        'host'    => 'vivid-test',
        'port'    => 4444,
        'timeout' => 30000,
      ),
      array(
        'name'    => 'Firefox 2 on Windows XP',
        'browser' => '*firefox c:\Programme\MozillaFirefox2\firefox.exe',
        'host'    => 'vivid-test',
        'port'    => 4444,
        'timeout' => 30000,
      )
    );
}
