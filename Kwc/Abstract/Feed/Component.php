<?php
abstract class Kwc_Abstract_Feed_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Kwc_Abstract_Feed_ContentSender';
        return $ret;
    }

    abstract protected function _getRssEntries();

    protected function _getRssTitle()
    {
        return Zend_Registry::get('config')->application->name;
    }

    public function getXml()
    {
        $cacheId = 'feed-' . $this->getData()->componentClass;
        $data = Kwf_Cache_Simple::fetch($cacheId, $success);
        if (!$success) {
            $data = $this->_getFeedXml();
            Kwf_Cache_Simple::add($cacheId, $data);
        }
        return $data;
    }

    private function _getFeedXml()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : '';
        $feedArray = array(
            'title' => $this->_getRssTitle(),
            'link' => $host.$this->getUrl(),
            //'lastUpdate' => ,
            'charset' => 'utf-8',
            'description' => '',
            //'author' => ,
            //'email' => ,
            'copyright' => Zend_Registry::get('config')->application->name,
            'generator' => 'Koala Framework',
            'language' => $this->getData()->getLanguage(),
            'entries' => $this->_getRssEntries()
        );
        $feed = Zend_Feed::importArray($feedArray, 'rss');
        return $feed->saveXml();
    }
}
