<?php
class Vps_Test_TestResult extends PHPUnit_Framework_TestResult
{
    private $_retryOnError;
    public function setRetryOnError($f)
    {
        $this->_retryOnError = (bool)$f;
    }
    private function _askForRetry(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        if (!$this->_retryOnError) return false;
        if ($e instanceof PHPUnit_Framework_IncompleteTest) return false;
        if ($e instanceof PHPUnit_Framework_SkippedTest) return false;
        
        $error = new PHPUnit_Framework_TestFailure($test, $e);

        if ($test instanceof PHPUnit_Framework_SelfDescribing) {
            echo $test->toString();
        } else {
            echo get_class($test);
        }
        echo "\n";
        echo $error->getExceptionAsString() .
        PHPUnit_Util_Filter::getFilteredStacktrace(
            $error->thrownException(),
            FALSE
        );

        echo "\nTest failed. Try again? [y/N]";
        $stdin = fopen('php://stdin', 'r');
        $input = fgets($stdin, 2);
        fclose($stdin);
        if (strtolower($input) == 'j' || strtolower($input) == 'y') {
            $this->run($test);
            return true;
        }
        return false;
    }
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        if (!$this->_askForRetry($test, $e, $time)) {
            parent::addFailure($test, $e, $time);
        }
    }

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        if (!$this->_askForRetry($test, $e, $time)) {
            parent::addError($test, $e, $time);
        }
    }
}
