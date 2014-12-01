<?php
/**
 * @package Model
 */
class Kwf_Model_Xml extends Kwf_Model_Data_Abstract
{
    protected $_filepath;
    protected $_xpath;
    protected $_topNode;
    protected $_rootNode;
    protected $_xmlContent;
    private $_simpleXml;

    protected $_rowClass = 'Kwf_Model_Xml_Row';
    protected $_rowSetClass = 'Kwf_Model_Xml_Rowset';

    public function __construct(array $config = array())
    {
        if (isset($config['filepath'])) $this->_filepath = $config['filepath'];
        if (isset($config['xmlContent'])) $this->_xmlContent = $config['xmlContent'];
        if (isset($config['xpath'])) $this->_xpath = $config['xpath'];
        if (isset($config['topNode'])) $this->_topNode = $config['topNode'];
        if (isset($config['rootNode'])) $this->_rootNode = $config['rootNode'];
        parent::__construct($config);
    }

    public function getData()
    {
        if (!isset($this->_data)) {
            $data = array();
            foreach ($this->_getElements() as $key=>$element) {
                $data[$key] = array();
                foreach ($element as $eKey => $eVal) {
                    $data[$key][$eKey] = (string)$eVal;
                }
            }
            $this->_data = $data;
        }
        return $this->_data;
    }

    public function getRowByDataKey($key)
    {
        if (!isset($this->_rows[$key])) {
            $elements = $this->_getElements();
            $data = array();
            foreach ($elements[$key] as $eKey => $eVal) {
                $data[$eKey] = (string)$eVal;
            }
            $this->_rows[$key] = new $this->_rowClass(array(
                'data' => $data,
                'model' => $this
            ));
        }
        return $this->_rows[$key];
    }

    public function update(Kwf_Model_Row_Interface $row, $rowData)
    {
        $id = $row->{$this->getPrimaryKey()};
        $simpleXml = $this->_getSimpleXml();

        foreach ($this->_getElements() as $f) {
            if ($f->{$this->getPrimaryKey()} == $id) {
                foreach ($rowData as $k=>$i) {
                    if ($i === null) {
                       unset ($f->$k);
                    } else {
                        $f->$k = $i;
                    }

                }
            }
        }
        //löscht aus der der klasse gespeicherten data
        $data = $this->getData();
        foreach ($data as $datakey => $dataelement) {
            if ($dataelement[$this->getPrimaryKey()] == $id) {
            foreach ($rowData as $k=>$i) {
                    if ($i === null) {
                       unset ($data[$datakey][$k]);
                    } else {
                       $data[$datakey][$k] = $i;
                    }
                }
            }
        }
        $this->_data = $data;
        if ($this->_filepath) {
            file_put_contents($this->_filepath, self::asPrettyXML($simpleXml->asXML()));
        }
        return $row->{$this->getPrimaryKey()};
    }

    public function insert(Kwf_Model_Row_Interface $row, $rowData)
    {
        $data = $this->getData();
        if (!$this->getPrimaryKey()) {
            throw new Kwf_Exception("No Insertion without a primary key");
        }
        if ($this->_idExists($rowData[$this->getPrimaryKey()])){
            throw new Kwf_Exception("Id is already used");
        }


        $simpleXml = $this->_getSimpleXml();
        $toAddXml = $this->_getRootElement();

        $node = $toAddXml->addChild($this->_topNode);

        $id = null;

        if (!array_key_exists($this->getPrimaryKey(), $rowData)) {
            throw new Kwf_Exception("No Id was set, inserting impossible");
        }
        foreach ($rowData as $k=>$i) {

           if ($k == $this->getPrimaryKey()) {
               if (!$i) {
                   $i = $this->_getNewId();
               }
               $id = $i;
           }
           if (is_array($i)) {
               throw  new Kwf_Exception("No arguments allowed in a Xml Node");
           }
           if ($i !== null) {
               $i =  str_replace('&', '&amp;', $i); //bugfix für php bug, sonst kommt der fehler "unterminated entity reference"
               $node->addChild($k, $i);
           }
        }

        if ($this->_filepath) {
            file_put_contents($this->_filepath, self::asPrettyXML($simpleXml->asXML()));
        }
        $row->{$this->getPrimaryKey()} = $id;
        $rowData[$this->getPrimaryKey()] = $id;


        foreach ($rowData as $k => $row1) {
            if ($k != $this->getPrimaryKey() && $row1 === null) {
                unset($rowData[$k]);
            }
        }
        $key = $this->_generateKey();
        $this->_data[$key] = $rowData;
        $this->_rows[$key] = $row;
        return $id;
    }

    private function _generateKey()
    {
        if (!$this->_data) return 0;
        return max(array_keys($this->_data))+1;
    }

    public function delete(Kwf_Model_Row_Interface $row)
    {
        $id = $row->{$this->getPrimaryKey()};
        $xml = $this->_getRootElement();

        foreach ($this->_rows as $k=>$i) {
            if ($row === $i) {
                unset($this->_data[$k]);
                unset($this->_rows[$k]);
                break;
            }
        }
        $i = 0;
        $check = false;
        foreach ($xml as $k => $element) {
            if ($element->{$this->getPrimaryKey()} == $id) {
                unset($xml->{$element->getName()}[$i]);
                $check = true;
                break;
            }
            $i++;
        }

        if ($this->_filepath) {
            file_put_contents($this->_filepath, self::asPrettyXML($xml->asXML()));
        }
        if (!$check) throw new Kwf_Exception("Can't find entry with id '$id'");
    }

    private function _getSimpleXml()
    {
        if (!isset($this->_simpleXml)) {
            if ($this->_xmlContent) {
                $contents = $this->_xmlContent;
            } else {
                if (file_exists($this->_filepath)){
                    $contents = file_get_contents($this->_filepath);
                } elseif (isset($this->_rootNode)) {
                    $contents = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<$this->_rootNode></$this->_rootNode>";
                } else {
                    throw new Kwf_Exception("Neither a rootnode nor a filepath is set");
                }
            }
            $this->_simpleXml = new SimpleXMLElement($contents);
        }
        return $this->_simpleXml;
    }

    private function _getNewId ()
    {
        $simpleXml = $this->_getSimpleXml();
        $highestId = 0;
        foreach ($this->_getElements($simpleXml) as $f) {
            if (((int)$f->{$this->getPrimaryKey()}) > $highestId) $highestId = (int) $f->{$this->getPrimaryKey()};
        }
        return ++$highestId;
    }

    private function _getElements()
    {
        $simpleXml = $this->_getSimpleXml();

        if (!$simpleXml->xpath($this->_xpath)) {
            throw new Kwf_Exception("Wrong Xpath '$this->_xpath' for model '".get_class($this)."'");
        }
        return $simpleXml->xpath($this->_xpath."/".$this->_topNode);
    }

    private function _getRootElement()
    {
        $simpleXml = $this->_getSimpleXml();
        $ret = $simpleXml->xpath($this->_xpath);
        if (!$ret) {
            throw new Kwf_Exception("Wrong Xpath '$this->_xpath' for model '".get_class($this)."'");
        }
        return $ret[0];
    }

    public function getXmlContentString()
    {
        return $this->_getSimpleXml()->asXML();
    }

    public function getFilePath ()
    {
        return $this->_filepath;
    }

    public function getUniqueIdentifier() {
        if (isset($this->_filepath)) {
            return str_replace(array('/', '.', '-'), array('_', '_', '_'), $this->_filepath);
        } else if (isset($this->_xmlContent)) {
            return md5($this->_xmlContent);
        } else {
            throw new Kwf_Exception("no unique identifier set");
        }
    }

    public function isEqual(Kwf_Model_Interface $other)
    {
        if ($other instanceof Kwf_Model_Xml && $this->getFilepath() ==  $other->getFilepath()) {
            return true;
        } else {
            return false;
        }
    }

    public static function asPrettyXML($string)
    {
        $indent = 3;
        /**
         * put each element on it's own line
         */
        $string =preg_replace("/>(\f|\n|\t|\v)*</",">\n<",$string);

        /**
         * each element to own array
         */
        $xmlArray = explode("\n",$string);

        /**
         * holds indentation
         */
        $currIndent = 0;

        /**
         * set xml element first by shifting of initial element
         */
        $string = array_shift($xmlArray) . "\n";

        foreach($xmlArray as $element) {
            /** find open only tags... add name to stack, and print to string
             * increment currIndent
             */

            if (preg_match('/^<([\w])+[^>\/]*>$/U',$element)) {
                $string .=  str_repeat(' ', 0) . $element . "\n";
                $currIndent += $indent;
            }

            /**
             * find standalone closures, decrement currindent, print to string
             */
            elseif ( preg_match('/^<\/.+>$/',$element)) {
                $currIndent -= $indent;
                $string .=  str_repeat(' ', 0) . $element . "\n";
            }
            /**
             * find open/closed tags on the same line print to string
             */
            else {
                $string .=  str_repeat(' ', 0) . $element . "\n";
            }
        }

        return $string;

    }

}
