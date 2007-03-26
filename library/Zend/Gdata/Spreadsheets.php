<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Gdata_InvalidArgumentException
 */
require_once 'Zend/Gdata/InvalidArgumentException.php';

/**
 * Gdata Spreadsheets
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Spreadsheets extends Zend_Gdata
{

    // Gdata-general request parameters:
    // @todo: request parameter 'q'
    // @todo: request parameter category, e.g. '/feeds/jo/-/Fritz'
    // @todo: request parameter entryId, e.g. '/feeds/jo/entry1
    // @todo: request parameter 'max-results'
    // @todo: request parameter 'start-index'
    // @todo: request parameter 'author'
    // @todo: request parameter 'alt' ('atom' or 'rss')
    // @todo: request parameter 'updated-min'
    // @todo: request parameter 'updated-max'
    // @todo: request parameter 'published-min'
    // @todo: request parameter 'published-max'

    // Spreadsheets-specific request parameters:
    // @todo: list-feed request parameter 'orderby'
    // @todo: list-feed request parameter 'reverse'
    // @todo: list-feed request parameter 'sq'
    // @todo: cell-feed request parameter 'min-row'
    // @todo: cell-feed request parameter 'max-row'
    // @todo: cell-feed request parameter 'min-col'
    // @todo: cell-feed request parameter 'max-col'
    // @todo: cell-feed request parameter 'range'
    // @todo: cell-feed request parameter 'min-col'

    // Spreadsheets-specific query structure:
    // @todo: sq query language

    // Spreadsheets-specific response content:
    // @todo: gs and gsx namespaces
    // @todo: <gs:rowCount>
    // @todo: <gs:colCount>
    // @todo: <gs:cell>
    // @todo: <gsx:_columnName_>

    /**
     * @param string $var
     * @param string $value
     * @throws Zend_Gdata_InvalidArgumentException
     */
    protected function __set($var, $value)
    {
        switch ($var) {
            case 'updatedMin':
            case 'updatedMax':
                throw new Zend_Gdata_InvalidArgumentException("Parameter '$var' is not currently supported in Spreadsheets.");
                break;
        }
        parent::__set($var, $value);
    }

}

