<?php
class Kwf_Trl_BuildFileMissingException extends Kwf_Exception
{
    public function __construct($message = 'trl build file does not exist')
    {
        parent::__construct($message);
    }

    public function getSettingsNonStaticTrlException()
    {
        $exceptionLocation = null;
        foreach ($this->getTrace() as $trace) {
            if (isset($trace['file']) && strpos($trace['file'], 'Kwf/Trl.php') === false
                && (
                    $trace['function'] == 'trlKwf' || $trace['function'] == 'trl'
                    || $trace['function'] == 'trlcKwf' || $trace['function'] == 'trlc'
                    || $trace['function'] == 'trlpKwf' || $trace['function'] == 'trlp'
                    || $trace['function'] == 'trlcpKwf' || $trace['function'] == 'trlcp'
                )
            ) {
                $exceptionLocation = $trace;
                break;
            }
        }
        if ($exceptionLocation) {
            $file = $exceptionLocation['file'];
            $line = $exceptionLocation['line'];
            return new Kwf_Exception("In getSettings-method only static version of trl is allowed $file:$line");
        }
        return false;
    }
}
