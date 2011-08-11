<?php
abstract class Vpc_Abstract_Feed_Component extends Vpc_Abstract
{
    abstract protected function _getRssEntries();

    protected function _getRssTitle()
    {
        return Zend_Registry::get('config')->application->name;
    }

    public function sendContent()
    {
        $xml = $this->getXml();
        header('Content-type: application/rss+xml; charset: utf-8');
        echo $xml;
    }

    public function getXml()
    {
        $cache = Vps_Component_Cache::getInstance();
        if (!$xml = $cache->load($this->getData())) {
            $xml = $this->_getFeedXml();
            $cache->save($this->getData(), $xml);
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
            'generator' => 'Vivid Planet Software GmbH',
            'language' => 'de', //TODO
            'entries' => $this->_getRssEntries()
        );
        $feed = Zend_Feed::importArray($feedArray, 'rss');
        return $feed->saveXml();
    }
}
