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
        $cache = Vps_Component_Cache::getInstance();
        $cacheId = $cache->getCacheIdFromComponentId($this->getData()->componentId);
        if (!$xml = $cache->load($cacheId)) {
            $feedArray = array(
                'title' => $this->_getRssTitle(),
                'link' => 'http://'.$_SERVER['HTTP_HOST'].$this->getUrl(),
                //'lastUpdate' => ,
                'charset' => 'utf-8',
                'description' => '',
                //'author' => 'Alexander Netkachev',
                //'email' => 'alexander.netkachev@gmail.com',
                'copyright' => Zend_Registry::get('config')->application->name,
                'generator' => 'Vivid Planet Software GmbH',
                'language' => 'de', //TODO
                'entries' => $this->_getRssEntries()
            );
            $feed = Zend_Feed::importArray($feedArray, 'rss');
            $xml = $feed->saveXml();
            $tags = array(
                'componentClass' => $this->getData()->componentClass,
                'pageId' => $this->getData()->getPage()->componentId
            );
            $cache->save($xml, $cacheId, $tags);
        }
        header('Content-type: application/rss+xml; charset: utf-8');
        echo $xml;
    }
}
