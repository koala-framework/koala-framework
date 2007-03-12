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
 * Zend_Mail_Storage_Abstract
 */
require_once 'Zend/Mail/Storage/Abstract.php';

/**
 * Zend_Mail_Message
 */
require_once 'Zend/Mail/Message.php';

/**
 * Zend_Mail_Storage_Exception
 */
require_once 'Zend/Mail/Storage/Exception.php';


/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_Storage_Maildir extends Zend_Mail_Storage_Abstract
{
    /**
     * data of found message files in maildir dir
     * @var array
     */
    protected $_files = array();

    /**
     * known flag chars in filenames
     * @var array
     */
    protected static $_knownFlags = array('P' => 'Passed',
                                          'R' => 'Replied',
                                          'S' => 'Seen',
                                          'T' => 'Trashed',
                                          'D' => 'Draft',
                                          'F' => 'Flagged');

    /**
     * Count messages all messages in current box
     *
     * @return int number of messages
     * @throws Zend_Mail_Storage_Exception
     */
    public function countMessages()
    {
        return count($this->_files);
    }

    /**
     * Get one or all fields from file structure. Also checks if message is valid
     *
     * @param  int         $id    message number
     * @param  string|null $field wanted field
     * @return string|array wanted field or all fields as array
     * @throws Zend_Mail_Storage_Exception
     */
    protected function _getFileData($id, $field = null)
    {
        if (!isset($this->_files[$id - 1])) {
            throw new Zend_Mail_Storage_Exception('id does not exist');
        }

        if (!$field) {
            return $this->_files[$id - 1];
        }

        if (!isset($this->_files[$id - 1][$field])) {
            throw new Zend_Mail_Storage_Exception('field does not exist');
        }

        return $this->_files[$id - 1][$field];
    }

    /**
     * Get a list of messages with number and size
     *
     * @param  int|null $id number of message or null for all messages
     * @return int|array size of given message of list with all messages as array(num => size)
     * @throws Zend_Mail_Storage_Exception
     */
    public function getSize($id = null)
    {
        if ($id !== null) {
            return filesize($this->_getFileData($id, 'filename'));
        }

        $result = array();
        foreach ($this->_files as $num => $pos) {
            $result[$num + 1] = filesize($this->_files[$num]['filename']);
        }

        return $result;
    }



    /**
     * Fetch a message
     *
     * @param  int $id number of message
     * @return Zend_Mail_Message
     * @throws Zend_Mail_Storage_Exception
     */
    public function getMessage($id)
    {
        return new Zend_Mail_Message(array('handler' => $this, 'id' => $id, 'headers' => $this->getRawHeader($id)));
    }

    /*
     * Get raw header of message or part
     *
     * @param  int               $id       number of message
     * @param  null|array|string $part     path to part or null for messsage header
     * @param  int               $topLines include this many lines with header (after an empty line)
     * @return string raw header
     * @throws Zend_Mail_Storage_Exception
     */
    public function getRawHeader($id, $part = null, $topLines = 0)
    {
        if ($part !== null) {
            // TODO: implement
            throw new Zend_Mail_Storage_Exception('not implemented');
        }

        $fh = fopen($this->_getFileData($id, 'filename'), 'r');

        $content = '';
        while (!feof($fh)) {
            $line = fgets($fh);
            if (!trim($line)) {
                break;
            }
            $content .= $line;
        }

        fclose($fh);
        return $content;
    }

    /*
     * Get raw content of message or part
     *
     * @param  int               $id   number of message
     * @param  null|array|string $part path to part or null for messsage content
     * @return string raw content
     * @throws Zend_Mail_Storage_Exception
     */
    public function getRawContent($id, $part = null)
    {
        if ($part !== null) {
            // TODO: implement
            throw new Zend_Mail_Storage_Exception('not implemented');
        }

        $fh = fopen($this->_getFileData($id, 'filename'), 'r');

        while (!feof($fh)) {
            $line = fgets($fh);
            if (!trim($line)) {
                break;
            }
        }

        $content = stream_get_contents($fh);
        fclose($fh);
        return $content;
    }

    /**
     * Create instance with parameters
     * Supported parameters are:
     *   - dirname dirname of mbox file
     *
     * @param  $params array mail reader specific parameters
     * @throws Zend_Mail_Storage_Exception
     */
    public function __construct($params)
    {
        if (!isset($params['dirname']) || !is_dir($params['dirname'])) {
            throw new Zend_Mail_Storage_Exception('no valid dirname given in params');
        }

        if (!$this->_isMaildir($params['dirname'])) {
            throw new Zend_Mail_Storage_Exception('invalid maildir given');
        }

        $this->_has['top'] = true;
        $this->_openMaildir($params['dirname']);
    }

    /**
     * check if a given dir is a valid maildir
     *
     * @param string $dirname name of dir
     * @return bool dir is valid maildir
     */
    protected function _isMaildir($dirname)
    {
        return is_dir($dirname . '/cur');
    }

    /**
     * open given dir as current maildir
     *
     * @param string $dirname name of maildir
     * @return null
     * @throws Zend_Mail_Storage_Exception
     */
    protected function _openMaildir($dirname)
    {
        if ($this->_files) {
            $this->close();
        }

        $dh = @opendir($dirname . '/cur/');
        if (!$dh) {
            throw new Zend_Mail_Storage_Exception('cannot open maildir');
        }
        while (($entry = readdir($dh)) !== false) {
            if ($entry[0] == '.' || !is_file($dirname . '/cur/' . $entry)) {
                continue;
            }
            list($uniq, $info) = explode(':', $entry, 2);
            list($version, $flags) = explode(',', $info, 2);
            if ($version != 2) {
                $flags = '';
            } else {
                $named_flags = array();
                $length = strlen($flags);
                for ($i = 0; $i < $length; ++$i) {
                    $flag = $flags[$i];
                    $named_flags[$flag] = isset(self::$_knownFlags[$flag]) ? self::$_knownFlags[$flag] : '';
                }
            }

            $this->_files[] = array('uniq'     => $uniq,
                                    'flags'    => $named_flags,
                                    'filename' => $dirname . '/cur/' . $entry);
        }
        closedir($dh);
    }


    /**
     * Close resource for mail lib. If you need to control, when the resource
     * is closed. Otherwise the destructor would call this.
     *
     * @return void
     */
    public function close()
    {
        $this->_files = array();
    }


    /**
     * Waste some CPU cycles doing nothing.
     *
     * @return void
     */
    public function noop()
    {
        return true;
    }


    /**
     * stub for not supported message deletion
     *
     * @return null
     * @throws Zend_Mail_Storage_Exception
     */
    public function removeMessage($id)
    {
        throw new Zend_Mail_Storage_Exception('maildir is (currently) read-only');
    }

}
