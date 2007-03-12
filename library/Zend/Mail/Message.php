<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_Mail_Exception
 */
require_once 'Zend/Mail/Exception.php';

/**
 * Zend_Mail_Part
 */
require_once 'Zend/Mail/Part.php';

/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_Message extends Zend_Mail_Part
{
    /**
     * Public constructor
     *
     * In addition to the parameters of Zend_Mail_Part::__construct() this constructor supports:
     * - file filename or file handle of a file with raw message content
     *
     * @param  string $rawMessage  full message with or without headers
     * @throws Zend_Mail_Exception
     */
    public function __construct(array $params)
    {
        if (isset($params['file'])) {
            if (!is_resource($params['file'])) {
                $params['raw'] = @file_get_contents($params['file']);
                if ($params['raw'] === false) {
                    throw new Zend_Mail_Exception('could not open file');
                }
            } else {
                $params['raw'] = stream_get_contents($params['file']);
            }
        }

        parent::__construct($params);
    }

    /**
     * return toplines as found after headers
     *
     * @return string toplines
     */
    public function getTopLines()
    {
        return $this->_topLines;
    }
}
