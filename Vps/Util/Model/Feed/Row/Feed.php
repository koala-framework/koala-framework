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
        $encoding = false;

        if (substr($data['url'], 0, 7)=='file://') {
            $str = file_get_contents($data['url']);
        } else {
            $response = $config['model']->getHttpRequestor()->request($data['url']);
            if ($response->getStatusCode() != 200) {
                throw new Vps_Exception("invalid status response from server: ".$response->getStatusCode());
            }
            $str = $response->getBody();
        }
        $originalContent = $str;
        $str = trim($str);
        if (preg_match('#<?xml[^>]* encoding=["\']([^"\']*)["\']#', $str, $m)) {
            $encoding = trim(strtolower($m[1]));
            if ($encoding != 'utf8' && $encoding != 'utf-8') {
                $str = iconv($encoding, 'utf-8', $str);
                $str = preg_replace('#(<?xml[^>]* encoding=["\'])([^"\']*)(["\'])#', '\1utf-8\3', $str);
            }
        } else if (isset($response)) {
            $ct = $response->getContentType();
            if ($ct) {
                if (preg_match('#charset=([^;]*)#i', strtolower($ct), $m)) {
                    $encoding = trim($m[1]);
                    if ($encoding != 'utf8' && $encoding != 'utf-8') {
                        $str = iconv($encoding, 'utf-8', $str);
                    }
                }
            }
        }

        if (!$encoding) {
            $encoding = 'iso-8859-1';
            $str = iconv($encoding, 'utf-8', $str);
        }

        Vps_Benchmark::count('loaded feed');
        $this->_xml = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOERROR|LIBXML_NOWARNING);
        if (!$this->_xml) {
            //try with another encoding
            $this->_xml = simplexml_load_string(iconv('iso-8859-1', 'utf-8', $str), 'SimpleXMLElement', LIBXML_NOERROR|LIBXML_NOWARNING);
            if ($this->_xml) {
                $encoding = 'iso-8859-1';
            }
        }

        if (!$this->_xml) {
            if (class_exists('tidy')) {
                $c = array(
                        'indent'         => true,
                        'input-xml' => true,
                        'output-xml' => true,
                        'wrap'           => '86',
                        'char-encoding'  =>'utf8',
                        'newline'        =>'LF',
                        );
                $tidy = new tidy;
                $tidy->parseString($str, $c, 'utf8');
                $tidy->cleanRepair();
                $str = $tidy->value;
                $this->_xml = simplexml_load_string($str, 'SimpleXMLElement');
            }
        }

        if (!$this->_xml) {
            throw new Vps_Exception('Can\'t load feed: '.$originalContent);
        }
        if ($this->_xml->channel) {
            $data['format'] = self::FORMAT_RSS;
        } else if ($this->_xml->getName() == 'feed') {
            $data['format'] = self::FORMAT_ATOM;
        } else {
            throw new Vps_Exception('Can\'t load feed, unknown format: '.$originalContent);
        }
        $data['encoding'] = $encoding;
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
