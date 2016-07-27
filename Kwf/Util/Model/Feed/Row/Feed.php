<?php
class Kwf_Util_Model_Feed_Row_Feed extends Kwf_Model_Row_Data_Abstract
{
    private $_xml;
    const FORMAT_RSS = 'rss';
    const FORMAT_ATOM = 'atom';
    public function __construct($config)
    {
        $data['url'] = $config['url'];
        $encoding = false;

        if (substr($data['url'], 0, 7)=='file://' || substr($data['url'], 0, 6)=='php://') {
            $str = file_get_contents($data['url']);
        } else {
            $response = $config['model']->getHttpRequestor()->request($data['url']);
            if ($response->getStatusCode() != 200) {
                throw new Kwf_Exception("invalid status response from server: ".$response->getStatusCode()." for '$data[url]'");
            }
            $str = $response->getBody();
        }
        if (!$str) {
            throw new Kwf_Exception("Can't load feed '$data[url]', response is empty");
        }
        $originalContent = $str;
        $str = trim($str);

        if (preg_match('#<?xml[^>]* encoding=["\']([^"\']*)["\']#', $str, $m)) {
            $encoding = trim(strtolower($m[1]));
            if ($encoding != 'utf8' && $encoding != 'utf-8') {
                try {
                    $str = iconv($encoding, 'utf-8', $str);
                    $str = preg_replace('#(<?xml[^>]* encoding=["\'])([^"\']*)(["\'])#', '\1utf-8\3', $str);
                } catch (Exception $e) {}
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
            try {
                $encoding = $config['model']->getDefaultEncoding();
                $str = iconv($encoding, 'utf-8', $str);
            } catch (Exception $e) {}
        }

        $entityLoaderWasDisabled = libxml_disable_entity_loader(true);
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
                //maximum length; simply cut off, tidy will repair it properly
                $maxLength = 5*1024*1024;
                if (strlen($str) > $maxLength) $str = substr($str, 0, $maxLength);
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
                $str = preg_replace('#(<?phpxml[^>]* encoding=["\'])([^"\']*)(["\'])#', '\1utf-8\3', $str);
                $str = str_replace("\0", '', $str);
                $this->_xml = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOERROR|LIBXML_NOWARNING);
            }
        }
        libxml_disable_entity_loader($entityLoaderWasDisabled);

        if (!$this->_xml) {
            throw new Kwf_Exception("Can't load feed: '$data[url]' ".$originalContent);
        }
        if ($this->_xml->channel) {
            $data['format'] = self::FORMAT_RSS;
        } else if ($this->_xml->getName() == 'feed') {
            $data['format'] = self::FORMAT_ATOM;
        } else {
            throw new Kwf_Exception("Can't load feed '$data[url]', unknown format: ".$originalContent);
        }
        $data['encoding'] = $encoding;
        $data['hub'] = null;
        if ($data['format'] == self::FORMAT_RSS) {
            $data['title'] = (string)$this->_xml->channel->title;
            $data['link'] = (string)$this->_xml->channel->link;
            $data['description'] = (string)$this->_xml->channel->description;

            //der hub ist im atom namespace in einem rss20 feed
            //also in diesen namespace wechseln
            foreach ($this->_xml->channel->children('http://www.w3.org/2005/Atom')->link as $link) {
                $link = $link->attributes(''); //die attribute sind aber wida im default namespace, also wida rauswechseln
                if ($link['rel'] == 'hub') {
                    $data['hub'] = (string)$link['href'];
                    break;
                }
            }
        } else {
            $data['title'] = (string)$this->_xml->title;
            $data['link'] = null;
            foreach ($this->_xml->link as $link) {
                if (!$link['href']) continue;
                if ($link['rel'] != 'self') {
                    $data['link'] = (string)$link['href'];
                    break;
                }
            }
            foreach ($this->_xml->link as $link) {
                if (!$link['href']) continue;
                if ($link['rel'] == 'hub') {
                    $data['hub'] = (string)$link['href'];
                    break;
                }
            }
        }
        $config['data'] = $data;
        Kwf_Benchmark::count('loaded feed');

        parent::__construct($config);
    }

    public function getEntries($select = array())
    {
        return $this->_model->getDependentModel('Entries')
                ->_getFeedEntries($this, $this->_xml, $select);
    }
}
