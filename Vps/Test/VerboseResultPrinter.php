<?php
class Vps_Test_VerboseResultPrinter extends PHPUnit_TextUI_ResultPrinter
{
    public function __construct($out = NULL, $colors = FALSE)
    {
        parent::__construct($out, false, $colors);
    }
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        echo "\n".date('Y-m-d H:i:s').': finished '.$test->toString()." in $time\n";
        return parent::endTest($test, $time);
    }
    public function startTest(PHPUnit_Framework_Test $test)
    {
        echo "\n".date('Y-m-d H:i:s').': starting '.$test->toString()."\n";
        return parent::startTest($test);
    }

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        echo $e;
        return parent::addError($test, $e, $time);
    }
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        echo $e;
        return parent::addFailure($test, $e, $time);
    }
}
