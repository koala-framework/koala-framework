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
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Translate_Exception */
require_once 'Zend/Translate/Exception.php';

/** Zend_Locale */
require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Translate {
    /**
     * Adapter names constants
     */
    const AN_GETTEXT = 'gettext';
    const AN_ARRAY   = 'array';
    const AN_CSV     = 'csv';
    const AN_TMX     = 'tmx';
    const AN_XLIFF   = 'xliff';

    /**
     * Adapter
     *
     * @var Zend_Translate_Adapter
     */
    private $_adapter;


    /**
     * Generates the standard translation object
     *
     * @param  string              $adapter  Adapter to use
     * @param  array               $options  Options for this adapter to set
     *                                       Depends on the Adapter
     * @param  string|Zend_Locale  $locale   OPTIONAL locale to use
     * @throws Zend_Translate_Exception
     */
    public function __construct($adapter, $options, $locale = null)
    {
        $this->setAdapter($adapter, $options, $locale);
    }


    /**
     * Sets a new adapter
     *
     * @param  string              $adapter  Adapter to use
     * @param  array               $options  Options for the adapter to set
     * @param  string|Zend_Locale  $locale   OPTIONAL locale to use
     * @throws Zend_Translate_Exception
     */
    public function setAdapter($adapter, $options, $locale = null)
    {
        switch (strtolower($adapter)) {
            case 'array':
                /** Zend_Translate_Adapter_Array */
                require_once('Zend/Translate/Adapter/Array.php');
                $this->_adapter = new Zend_Translate_Adapter_Array($options, $locale);
                break;
            case 'gettext':
                /** Zend_Translate_Adapter_Gettext */
                require_once('Zend/Translate/Adapter/Gettext.php');
                $this->_adapter = new Zend_Translate_Adapter_Gettext($options, $locale);
                break;
            case 'tmx':
                /** Zend_Translate_Adapter_Tmx */
                require_once('Zend/Translate/Adapter/Tmx.php');
                $this->_adapter = new Zend_Translate_Adapter_Tmx($options, $locale);
                break;
            case 'csv':
                /** Zend_Translate_Adapter_Csv */
                require_once('Zend/Translate/Adapter/Csv.php');
                $this->_adapter = new Zend_Translate_Adapter_Csv($options, $locale);
                break;
            case 'xliff':
                /** Zend_Translate_Adapter_Xliff */
                require_once('Zend/Translate/Adapter/Xliff.php');
                $this->_adapter = new Zend_Translate_Adapter_Xliff($options, $locale);
                break;
            case 'qt':
            case 'sql':
            case 'tbx':
            case 'xmltm':
                throw new Zend_Translate_Exception("adapter '$adapter' is not supported for now");
                break;
            default:
                throw new Zend_Translate_Exception('no adapter selected');
                break;
        }
    }


    /**
     * Returns the adapters name and it's options
     *
     * @return Zend_Translate_Adapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }


    /**
     * Add translation data.
     *
     * It may be a new language or additional data for existing language
     * If $clear parameter is true, then translation data for specified
     * language is replaced and added otherwise
     *
     * @param  string|array        $options  Option for this adapter, depends on the adapter
     * @param  string|Zend_Locale  $locale   Locale/Language to add to this adapter
     * @param  boolean             $clear    If true the new translation is added to the existing one
     */
    public function addTranslation($options, $locale, $clear = false)
    {
        $this->_adapter->addTranslation($options, $locale, $clear);
    }


    /**
     * Sets a new locale/language
     *
     * @param  string|Zend_Locale  $locale  Locale/Language to set for translations
     */
    public function setLocale($locale)
    {
        $this->_adapter->setLocale($locale);
    }


    /**
     * Returns the actual set locale/language
     *
     * @return Zend_Locale|null
     */
    public function getLocale()
    {
        return $this->_adapter->getLocale();
    }


    /**
     * Returns all avaiable locales/anguages from this adapter
     *
     * @return array
     */
    public function getList()
    {
        return $this->_adapter->getList();
    }


    /**
     * is the wished language avaiable ?
     *
     * @param  string|Zend_Locale  $locale  Is the locale/language avaiable
     * @return boolean
     */
    public function isAvailable($locale)
    {
        return $this->_adapter->isAvailable($locale);
    }


    /**
     * Translate the given string
     *
     * @param  string              $messageId  Original to translate
     * @param  string|Zend_Locale  $locale     OPTIONAL locale/language to translate to
     * @return string
     */
    public function _($messageId, $locale = null)
    {
        return $this->_adapter->translate($messageId, $locale);
    }


    /**
     * Translate the given string
     *
     * @param  string              $messageId  Original to translate
     * @param  string|Zend_Locale  $locale     OPTIONAL locale/language to translate to
     * @return string
     */
    public function translate($messageId, $locale = null)
    {
        return $this->_adapter->translate($messageId, $locale);
    }
}
