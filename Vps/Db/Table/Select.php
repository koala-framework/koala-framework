<?php
class Vps_Db_Table_Select extends Zend_Db_Table_Select
{
    public function where($cond, $value = null, $type = null)
    {
        if (is_array($cond)) {
            foreach ($cond as $key => $val) {
                // is $key an int?
                if (is_int($key)) {
                    // $val is the full condition
                    $this->where($val);
                } else {
                    // $key is the condition with placeholder,
                    // and $val is quoted into the condition
                    $this->where($key, $val);
                }
            }
            return $this;
        } else {
            return parent::where($cond, $value, $type);
        }
    }
    public function limit($count = null, $offset = null)
    {
        if (is_array($count)) {
            $offset = $count['start'];
            $count = $count['limit'];
        }
        return parent::limit($count, $offset);
    }

    public function getTableName()
    {
        return $this->_info['name'];
    }

    public function info()
    {
        return $this->_info;
    }

    /**
     * Adds the needed wheres for a search
     *
     * @param string|array $searchValues An array with the db-field in the key
     * and the search value as value. Field query means to search in all given fields
     * for this value (see the second argument). If a string is given, it is interpreted
     * like array('query' => $searchValues).
     * @param string|array $searchFields The fields that should be searched with
     * search field 'query'. If a string is given, it is treated like array($string).
     * Value '*' means to search in all fields in the table.
     * @return object $this The select object itself
     */
    public function searchLike($searchValues = array(), $searchFields = '*')
    {
        if (is_string($searchValues)) $searchValues = array('query' => $searchValues);
        if (is_string($searchFields)) $searchFields = array($searchFields);

        $selectInfo = $this->info();
        foreach ($searchValues as $column => $value) {
            if (empty($value) ||
                ($column != 'query' && !in_array($column, $selectInfo['cols']))
            ) {
                continue;
            }
            $searchWords = preg_split('/[\s-+,;]/', $value);
            foreach ($searchWords as $searchWord) {
                $searchWord = trim($searchWord);
                if (empty($searchWord)) continue;

                if ($column == 'query') {
                    if (!$searchFields) {
                        throw new Vps_Exception("Search field 'query' was found, "
                        ."but no 'searchFields' were given as second argument in "
                        ."'searchLike' method of Vps_Db_Table_Select object");
                    }
                    if (in_array('*', $searchFields)) {
                        $searchFields = array_merge($searchFields, $selectInfo['cols']);
                        foreach ($searchFields as $sfk => $sfv) {
                            if ($sfv == '*') unset($searchFields[$sfk]);
                            if (substr($sfv, 0, 1) == '!') {
                                unset($searchFields[$sfk]);
                                unset($searchFields[array_search(substr($sfv, 1), $searchFields)]);
                            }
                        }
                    }

                    $wheres = array();
                    foreach ($searchFields as $field) {
                        if (strpos($field, '.') === false) {
                            $field = $selectInfo['name'].'.'.$field;
                        }
                        $wheres[] = Vps_Registry::get('db')->quoteInto(
                            $field.' LIKE ?', "%$searchWord%"
                        );
                    }
                    $this->where(implode(' OR ', $wheres));
                } else {
                    $this->where($column.' LIKE ?', "%".$searchWord."%");
                }
            }
        }
        return $this;
    }
}
