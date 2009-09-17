<?php
class Vps_Test_SeleniumTestCase_Driver extends PHPUnit_Extensions_SeleniumTestCase_Driver
{
    public function __call($command, $arguments)
    {
        if ($command == 'waitForElementPresent' || $command == 'waitForElementNotPresent') {
            if (count($arguments) == 1) {
                $arguments[] = $this->seleniumTimeout * 1000;
            }
            $this->doCommand($command, $arguments);
        } else {
            return parent::__call($command, $arguments);
        }
    }
}
