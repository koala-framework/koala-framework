<?php
/**
 * In Ermangelung eines translate adapter interfaces von Zend wurden einfach
 * nur die methoden geschrieben, die wir benötigen.
 * @deprecated unused, TODO remove?
 * @internal
 */
class Kwf_Trl_ZendAdapter extends Zend_Translate_Adapter
{
    private $_locale = null;

    public function __construct($locale)
    {
        $this->_locale = $locale;
    }

    //muss false zurückgeben da sonst validator keine message bekommen
    public function isTranslated($messageId, $original = false, $locale = null)
    {
        return false;
    }

    public function translate($messageId, $locale = null)
    {
        return Zend_Registry::get('trl')->trlStaticExecute($messageId, $this->_locale);
    }

    protected function _loadTranslationData($data, $locale, array $options = array())
    {
    }

    public function toString()
    {
        return 'kwf';
    }
}
