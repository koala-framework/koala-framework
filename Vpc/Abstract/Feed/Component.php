<?php
abstract class Vpc_Abstract_Feed_Component extends Vpc_Abstract
{
    abstract protected function _getRssEntries();
    protected function _getRssTitle()
    {
        return Zend_Registry::get('config')->application->name;
    }

    public function sendContent($decoratedPage)
    {
        // prepare an array that our feed is based on
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

        // create feed document
        $feed = Zend_Feed::importArray($feedArray, 'rss');

        // adjust created DOM document
        foreach ($feed as $entry) {
            $element = $entry->summary->getDOM();
            // modify summary DOM node
        }

        // send feed XML to client
        $feed->send();
    }
}
