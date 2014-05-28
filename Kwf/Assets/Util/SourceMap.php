<?php
class Kwf_Assets_Util_SourceMap
{
    protected $_map;
    protected $_file;
    public function __construct($mapContents, $fileContents)
    {
        $this->_map = json_decode($mapContents);
        if (!$this->_map) {
            throw new Kwf_Exception("Failed parsing map: ".json_last_error());
        }
        $this->_file = $fileContents;
    }

    public function stringReplace($string, $replace)
    {
        if (strpos("\n", $string)) throw new Kwf_Exception('string must not contain \n');
        if (strpos("\n", $replace)) throw new Kwf_Exception('replace must not contain \n');

        $adjustOffsets = array();
        $pos = 0;
        $str = $this->_file;
        $offset = 0;
        while (($pos = strpos($str, $string, $pos)) !== false) {
            $this->_file = substr($this->_file, 0, $pos+$offset).$replace.substr($this->_file, $pos+$offset+strlen($string));
            $offset += strlen($replace)-strlen($string);
            $line = substr_count(substr($str, 0, $pos), "\n")+1;
            $column = $pos - strrpos(substr($str, 0, $pos), "\n"); //strrpos can return false for first line which will subtract 0 (=false)
            $adjustOffsets[$line][] = array(
                'column' => $column,
                'absoluteOffset' => $offset,
                'offset' => strlen($replace)-strlen($string)
            );
            $pos = $pos + strlen($string);
        }
        $generatedLine = 1;
        $previousGeneratedColumn = 0;
        $newPreviousGeneratedColumn = 0;
        $mappingSeparator = '/^[,;]/';

        $str = $this->_map->mappings;

        $newMappings = '';
        while (strlen($str) > 0) {
            if ($str[0] === ';') {
                $generatedLine++;
                $newMappings .= $str[0];
                $str = substr($str, 1);
                $previousGeneratedColumn = 0;
                $newPreviousGeneratedColumn = 0;
            } else if ($str[0] === ',') {
                $newMappings .= $str[0];
                $str = substr($str, 1);
            } else {
                // Generated column.
                $temp = Kwf_Assets_Util_Base64VLQ::decode($str);
                $generatedColumn = $previousGeneratedColumn + $temp['value'];
                $previousGeneratedColumn = $generatedColumn;
                $newGeneratedColumn = $newPreviousGeneratedColumn + $temp['value'];
                $str = $temp['rest'];

                $offset = 0;
                if (isset($adjustOffsets[$generatedLine])) {
                    foreach ($adjustOffsets[$generatedLine] as $col) {
                        if ($generatedColumn > $col['column']) {
                            $offset += $col['offset'];
                        }
                    }
                }
                $generatedColumn += $offset;
                $newMappings .= Kwf_Assets_Util_Base64VLQ::encode($generatedColumn - $newPreviousGeneratedColumn);
                $newPreviousGeneratedColumn = $generatedColumn;

                //read rest of block as it is
                while (strlen($str) > 0 && !preg_match($mappingSeparator, $str[0])) {
                    $newMappings .= $str[0];
                    $str = substr($str, 1);
                }
            }
        }
        $this->_map->mappings = $newMappings;
        unset($this->_map->{'_x_org_koala-framework_last'}); //has to be re-calculated
    }

    public function getMappings()
    {
        $generatedMappings = array();

        $generatedLine = 1;
        $previousGeneratedColumn = 0;
        $previousOriginalLine = 0;
        $previousOriginalColumn = 0;
        $previousSource = 0;
        $previousName = 0;
        $mappingSeparator = '/^[,;]/';

        $str = $this->_map->mappings;

        while (strlen($str) > 0) {
            if ($str[0] === ';') {
                $generatedLine++;
                $str = substr($str, 1);
                $previousGeneratedColumn = 0;
            } else if ($str[0] === ',') {
                $str = substr($str, 1);
            } else {
                $mapping = array();
                $mapping['generatedLine'] = $generatedLine;

                // Generated column.
                $temp = Kwf_Assets_Util_Base64VLQ::decode($str);
                $mapping['generatedColumn'] = $previousGeneratedColumn + $temp['value'];
                $previousGeneratedColumn = $mapping['generatedColumn'];
                $str = $temp['rest'];

                if (strlen($str) > 0 && !preg_match($mappingSeparator, $str[0])) {
                    // Original source.
                    $temp = Kwf_Assets_Util_Base64VLQ::decode($str);
                    $mapping['originalSource'] = (isset($this->_map->sourceRoot) ? $this->_map->sourceRoot.'/' : '')
                                                 . $this->_map->sources[$previousSource + $temp['value']];
                    $previousSource += $temp['value'];
                    $str = $temp['rest'];
                    if (strlen($str) === 0 || preg_match($mappingSeparator, $str[0])) {
                        throw new Error('Found a source, but no line and column');
                    }

                    // Original line.
                    $temp = Kwf_Assets_Util_Base64VLQ::decode($str);
                    $mapping['originalLine'] = $previousOriginalLine + $temp['value'];
                    $previousOriginalLine = $mapping['originalLine'];
                    // Lines are stored 0-based
                    $mapping['originalLine'] += 1;
                    $str = $temp['rest'];
                    if (strlen($str) === 0 || preg_match($mappingSeparator, $str[0])) {
                        throw new Error('Found a source and line, but no column');
                    }

                    // Original column.
                    $temp = Kwf_Assets_Util_Base64VLQ::decode($str);
                    $mapping['originalColumn'] = $previousOriginalColumn + $temp['value'];
                    $previousOriginalColumn = $mapping['originalColumn'];
                    $str = $temp['rest'];

                    if (strlen($str) > 0 && !preg_match($mappingSeparator, $str[0])) {
                        // Original name.
                        $temp = Kwf_Assets_Util_Base64VLQ::decode($str);
                        $mapping['name'] = $this->_map->names[$previousName + $temp['value']];
                        $previousName += $temp['value'];
                        $str = $temp['rest'];
                    }
                }
                $generatedMappings[] = $mapping;
//                 if (isset($mapping['originalLine']) && is_int($mapping['originalLine'])) {
//                     $generatedMappings[] = $mapping;
//                 }
            }
        }
        return $generatedMappings;
    }

    protected function _addLastExtension()
    {
        $previousGeneratedColumn = 0;
        $previousOriginalLine = 0;
        $previousOriginalColumn = 0;
        $previousSource = 0;
        $previousName = 0;
        $mappingSeparator = '/^[,;]/';

        $str = $this->_map->mappings;

        while (strlen($str) > 0) {
            if ($str[0] === ';') {
                $str = substr($str, 1);
                $previousGeneratedColumn = 0;
            } else if ($str[0] === ',') {
                $str = substr($str, 1);
            } else {
                // Generated column.
                $temp = Kwf_Assets_Util_Base64VLQ::decode($str);
                $previousGeneratedColumn = $previousGeneratedColumn + $temp['value'];
                $str = $temp['rest'];

                if (strlen($str) > 0 && !preg_match($mappingSeparator, $str[0])) {
                    // Original source.
                    $temp = Kwf_Assets_Util_Base64VLQ::decode($str);
                    $previousSource += $temp['value'];
                    $str = $temp['rest'];
                    if (strlen($str) === 0 || preg_match($mappingSeparator, $str[0])) {
                        throw new Error('Found a source, but no line and column');
                    }

                    // Original line.
                    $temp = Kwf_Assets_Util_Base64VLQ::decode($str);
                    $previousOriginalLine = $previousOriginalLine + $temp['value'];
                    $str = $temp['rest'];
                    if (strlen($str) === 0 || preg_match($mappingSeparator, $str[0])) {
                        throw new Error('Found a source and line, but no column');
                    }

                    // Original column.
                    $temp = Kwf_Assets_Util_Base64VLQ::decode($str);
                    $previousOriginalColumn = $previousOriginalColumn + $temp['value'];
                    $str = $temp['rest'];

                    if (strlen($str) > 0 && !preg_match($mappingSeparator, $str[0])) {
                        // Original name.
                        $temp = Kwf_Assets_Util_Base64VLQ::decode($str);
                        $previousName += $temp['value'];
                        $str = $temp['rest'];
                    }
                }
            }
        }
        $this->_map->{'_x_org_koala-framework_last'} = array(
            'source' => $previousSource,
            'originalLine' => $previousOriginalLine,
            'originalColumn' => $previousOriginalColumn,
            'name' => $previousName,
        );
    }

    public function getFileContents()
    {
        return $this->_file;
    }

    public function getMapContents($includeLastExtension = true)
    {
        if ($includeLastExtension && !isset($this->_map->{'_x_org_koala-framework_last'})) {
            $this->_addLastExtension();
        }
        return json_encode($this->_map);
    }

    public function save($mapFileName, $fileFileName = null)
    {
        if ($fileFileName !== null) file_put_contents($fileFileName, $this->_file);
        file_put_contents($mapFileName, $this->getMapContents());
    }
}
