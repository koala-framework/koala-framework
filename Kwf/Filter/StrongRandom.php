<?php
class Kwf_Filter_StrongRandom implements Zend_Filter_Interface
{
    private $_length;
    public function __construct($length = 26)
    {
        $this->_length = $length;
    }

    public function filter($value)
    {
        //source: http://www.php-security.org/2010/05/09/mops-submission-04-generating-unpredictable-session-ids-and-hashes/

        $entropy = '';

        // try ssl first
        if (function_exists('openssl_random_pseudo_bytes')) {
            $entropy = openssl_random_pseudo_bytes(64, $strong);
            // skip ssl since it wasn't using the strong algo
            if($strong !== true) {
                $entropy = '';
            }
        }

        // add some basic mt_rand/uniqid combo
        $entropy .= uniqid(mt_rand(), true);

        // try to read from the windows RNG
        if (class_exists('COM')) {
            try {
                $com = new COM('CAPICOM.Utilities.1');
                $entropy .= base64_decode($com->GetRandom(64, 0));
            } catch (Exception $ex) {
            }
        }

        // try to read from the unix RNG
        if (is_readable('/dev/urandom')) {
            $h = fopen('/dev/urandom', 'rb');
            $entropy .= fread($h, 64);
            fclose($h);
        }

        $hash = hash('whirlpool', $entropy);
        return substr($hash, 0, $this->_length);
    }
}
