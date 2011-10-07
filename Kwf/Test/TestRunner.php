<?php
class Vps_Test_TestRunner extends PHPUnit_TextUI_TestRunner
{
    private $_retryOnError;
    protected function handleConfiguration(array &$arguments)
    {
        parent::handleConfiguration($arguments);
        if (!isset($arguments['noProgress'])) $arguments['noProgress'] = false;
        if (!isset($arguments['retryOnError'])) $arguments['retryOnError'] = false;
    }

    protected function createTestResult()
    {
        $ret = new Vps_Test_TestResult;
        $ret->setRetryOnError($this->_retryOnError);
        return $ret;
    }

    public function doRun(PHPUnit_Framework_Test $suite, array $arguments = array())
    {
        $handlesArguments = $arguments;
        $this->handleConfiguration($handlesArguments);

        $this->_retryOnError = $handlesArguments['retryOnError'];

        if (!$handlesArguments['noProgress'] && file_exists('/www/testtimes')) {
            $expectedTimes = array();
            $unknownTimes = 0;
            $tests = $suite->getFilteredTests(
                            $handlesArguments['filter'],
                            $handlesArguments['groups'],
                            $handlesArguments['excludeGroups']);
            foreach ($tests as $test) {
                $app = Vps_Registry::get('config')->application->id;
                $f = "/www/testtimes/$app/{$test->toString()}";
                if (isset($expectedTimes[$test->toString()])) {
                    throw new Vps_Exception("same test exists twice?!");
                }
                if (file_exists($f)) {
                    $expectedTimes[$test->toString()] = (float)file_get_contents($f);
                } else {
                    if ($test instanceof PHPUnit_Extensions_SeleniumTestCase) {
                        $expectedTimes[$test->toString()] = 15;
                    } else {
                        $expectedTimes[$test->toString()] = 1;
                    }
                    $unknownTimes++;
                }
            }

            if (!$expectedTimes || $unknownTimes/count($expectedTimes) > 0.2) $expectedTimes = array();
            $printer = new Vps_Test_ProgressResultPrinter($expectedTimes, null, $handlesArguments['verbose'], true);
            $this->setPrinter($printer);
        } else if ($handlesArguments['verbose']) {
            $printer = new Vps_Test_VerboseResultPrinter(null, true);
            $this->setPrinter($printer);
        }

        return parent::doRun($suite, $arguments);
    }
}
