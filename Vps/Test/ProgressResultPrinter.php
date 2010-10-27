<?php
class Vps_Test_ProgressResultPrinter extends PHPUnit_TextUI_ResultPrinter
{
    private $_times = array();
    private $_progressBar;
    private $_expectedTimes;
    private $_currentProgress;
    private $_currentTest;


    private function _getProgressBar()
    {
        if (!$this->_expectedTimes) return null;

        if (!isset($this->_progressBar)) {
            $adapter = new Zend_ProgressBar_Adapter_Console();
            $adapter->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                 Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                 Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
            $adapter->setTextWidth(30);
            $this->_progressBar = new Zend_ProgressBar(
                $adapter,
                0,
                array_sum($this->_expectedTimes)
            );
            $this->_currentProgress = 0;
            $this->_currentTest = 0;
        }
        return $this->_progressBar;
    }
    public function __construct(array $expectedTimes, $out = NULL, $verbose = FALSE, $colors = FALSE)
    {
        parent::__construct($out, $verbose, $colors);
        $this->_expectedTimes = $expectedTimes;
    }
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        return parent::addError($test, $e, $time);
    }
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        return parent::addFailure($test, $e, $time);
    }
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        return parent::addIncompleteTest($test, $e, $time);
    }
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        return parent::addSkippedTest($test, $e, $time);
    }
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $app = Vps_Registry::get('config')->application->id;
        if (!file_exists("/www/testtimes/$app")) mkdir("/www/testtimes/$app");
        file_put_contents("/www/testtimes/$app/{$test->toString()}", $time);
        if ($this->_expectedTimes) {
            $this->_currentProgress += $this->_expectedTimes[$test->toString()];
            $this->_currentTest++;
        }
        return parent::endTest($test, $time);
    }
    public function startTest(PHPUnit_Framework_Test $test)
    {
        if ($this->_getProgressBar()) {
            //erstellt sie beim ersten aufruf, nicht im kostruktor machen da sonst zu früh was rausgeschrieben wird
            $this->writeProgress('.');
        }
        return parent::startTest($test);
    }

    protected function writeProgress($progress)
    {
        if ($this->_getProgressBar()) {
            if ($progress != '.') {
                echo $progress."\n";
            }
            $t = round(array_sum($this->_expectedTimes)-$this->_currentProgress, 1);
            if ($t > 120) {
                $t = floor($t/60).' min '.($t%60).' sec';
            } else {
                $t = $t.' sec';
            }
            $this->_getProgressBar()->update($this->_currentProgress,
                $this->_currentTest.'/'.count($this->_expectedTimes).' noch '.$t);
        } else {
            return parent::writeProgress($progress);
        }
    }
}
