<?php
class Vps_Util_Model_Feed_Row_Feed extends Vps_Model_Row_Data_Abstract
{
    private $_xml;
    private $_entries;
    const FORMAT_RSS = 'rss';
    const FORMAT_ATOM = 'atom';
    public function __construct($config)
    {
        $data['url'] = $config['url'];
        $this->_xml = simplexml_load_file($config['url']);
        Vps_Benchmark::count('loaded feed');
        if ($this->_xml->channel) {
            $data['format'] = self::FORMAT_RSS;
        } else if ($this->_xml->getName() == 'feed') {
            $data['format'] = self::FORMAT_ATOM;
        } else {
            throw new Vps_Exception_NotYetImplemented();
        }

        if ($data['format'] == self::FORMAT_RSS) {
            $data['title'] = (string)$this->_xml->channel->title;
            $data['link'] = (string)$this->_xml->channel->link;
            $data['description'] = (string)$this->_xml->channel->description;
        } else {
            $data['title'] = (string)$this->_xml->title;
            $data['link'] = null;
            foreach ($this->_xml->link as $link) {
                if ($link['href'] && $link['rel'] != 'self') {
                    $data['link'] = (string)$link['href'];
                    break;
                }
            }
        }
        $config['data'] = $data;
        parent::__construct($config);
    }

    public function getEntries()
    {
        if (!isset($this->_entries)) {
            $this->_entries = $this->_model->getDependentModel('Entries')
                    ->_getFeedEntries($this, $this->_xml);
        }
        return $this->_entries;
    }

    public function serialize()
    {
        return serialize(array(
            'parent' => parent::serialize(),
            'entries' => $this->getEntries()
        ));
    }

    public function unserialize($str)
    {
        $data = unserialize($str);
        parent::unserialize($data['parent']);
        $this->_entries = $data['entries'];
    }
}
