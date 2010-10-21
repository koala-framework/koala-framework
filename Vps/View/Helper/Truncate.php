<?php
/**
 * Borrowed from smarty and extended
 */
class Vps_View_Helper_Truncate
{
    /**
     * The truncate method itself.
     *
     * @todo Possibility to put ready html into $string and act the way like it would be an array
     *
     * @param string|array $string The Input to be truncated
     * (string) The string to be truncated
     * (array) An array of strings to be truncated. Can be one of the following:
     *         array('String text 1', ' - String text 2')
     *         -- or --
     *         array(
     *             array('string' => 'String text 1'),
     *             array('string' => ': String text 2', 'tag' => 'span'),
     *             array('string' => ' - String text 3', 'tag' => 'strong', 'cssClass' => 'thirdPart')
     *         )
     * @param integer $length The maximum string length that should be returned. Default: 80
     * @param string $etc The extension of the string if (and only if!) it has been cut. Default: '…' (&hellip;)
     * @param boolean $break_words Wether to break within words or not. Default: false
     * @param boolean $middle Truncates the string in the middle, not at the end.
     *                        Not possible if the first argument is an array. Default: false
     * @return string $string The truncated string, or the original string if it's shorter than $length.
     */
    public function truncate($string, $length = 80, $etc = '…', $break_words = false, $middle = false)
    {
        if ($length === false) return $string;
        if ($length == 0) return '';

        if (is_array($string)) {
            if ($middle !== false) {
                throw new Vps_Exception("Using the 'middle' parameter is not possible when using an array as input");
            }
            return $this->_truncateArray($string, $length, $etc, $break_words);
        }

        if (mb_strlen($string) > $length) {
            $length -= mb_strlen($etc);
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1));
            }
            if(!$middle) {
                return mb_substr($string, 0, $length).$etc;
            } else {
                return mb_substr($string, 0, $length/2) . $etc . mb_substr($string, -$length/2);
            }
        } else {
            return $string;
        }
    }

    private function _truncateArray($parts, $length = 80, $etc = '…', $break_words = false)
    {
        $retItems = array();
        $lengthLeft = $length;
        foreach ($parts as $part) {
            if (is_string($part)) $part = array('string' => $part);
            if (empty($part['tag'])) $part['tag'] = '';
            if (empty($part['cssClass'])) $part['cssClass'] = '';

            if ($part['cssClass'] && !$part['tag']) {
                throw new Vps_Exception("A tag must be set when using cssClass with array truncating");
            }
            $retItem = $part;

            $retItem['string'] = $this->truncate($retItem['string'], $lengthLeft, $etc, $break_words, false);
            $lengthLeft -= mb_strlen($retItem['string']);
            if ($lengthLeft <= 0) {
                $etcLen = mb_strlen($etc);
                if (mb_strlen($retItem['string']) >= $etcLen
                    && mb_substr($retItem['string'], (-1) * $etcLen) != $etc
                ) {
                    $retItem['string'] = mb_substr($retItem['string'], 0, (-1) * $etcLen).$etc;
                }
                $retItems[] = $retItem;
                break;
            }
            $retItems[] = $retItem;
        }

        $ret = '';
        foreach ($retItems as $retItem) {
            if ($retItem['tag']) {
                $ret .= '<'.$retItem['tag'];
                if ($retItem['cssClass']) $ret .= ' class="'.$retItem['cssClass'].'"';
                $ret .= '>';
            }
            $ret .= $retItem['string'];
            if ($retItem['tag']) $ret .= '</'.$retItem['tag'].'>';
        }
        return $ret;
    }
}

