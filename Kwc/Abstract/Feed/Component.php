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
        $cache = Kwf_Component_Cache::getInstance();
        if (!$xml = $cache->load($this->getData())) {
            $xml = $this->_getFeedXml();
            $directory = $this->getData()->parent->getComponent()->getItemDirectory();
            $cache->save($this->getData(), $xml, 'component', 'page', null, $directory->componentId, null);
            Kwf_Component_Cache::getInstance()->writeBuffer();
        }
        return $xml;
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
