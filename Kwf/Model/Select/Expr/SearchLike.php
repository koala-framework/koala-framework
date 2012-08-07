<?php
/**
 * Adds the needed wheres for a search
 *
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_SearchLike implements Kwf_Model_Select_Expr_Interface
{
    private $_searchValues;
    private $_searchFields;
    /**
     * @param string|array $searchValues An array with the db-field in the key
     * and the search value as value. Field query means to search in all given fields
     * for this value (see the second argument). If a string is given, it is interpreted
     * like array('query' => $searchValues).
     * @param string|array $searchFields The fields that should be searched with
     * search field 'query'. If a string is given, it is treated like array($string).
     * Value '*' means to search in all fields in the table.
     */
    public function __construct($searchValues, $searchFields = '*')
    {
        if (!is_array($searchValues)) $searchValues = array('query' => $searchValues);
        if (!is_array($searchFields)) $searchFields = array($searchFields);
        $this->_searchValues = $searchValues;
        $this->_searchFields = $searchFields;
    }

    public function getSearchValues()
    {
        return $this->_searchValues;
    }

    public function getSearchFields()
    {
        return $this->_searchFields;
    }

    /**
     * @internal helper function
     */
    public function getQueryExpr(Kwf_Model_Interface $model)
    {
        $valuesOrs = array();
        foreach ($this->_searchValues as $column => $value) {
            if (empty($value)) continue;

            $searchWords = preg_split('/[\s-+,;*]/', $value);
            foreach ($searchWords as $searchWord) {
                $searchWord = trim($searchWord);
                if (empty($searchWord)) continue;

                if ($column == 'query') {
                    $searchFields = $this->_searchFields;
                    if (in_array('*', $searchFields)) {
                        $searchFields = array_merge($searchFields, $model->getColumns());
                        foreach ($searchFields as $sfk => $sfv) {
                            if ($sfv == '*') unset($searchFields[$sfk]);
                            if (substr($sfv, 0, 1) == '!') {
                                unset($searchFields[$sfk]);
                                unset($searchFields[array_search(substr($sfv, 1), $searchFields)]);
                            }
                        }
                    }

                    $ors = array();
                    foreach ($searchFields as $field) {
                        $ors[] = new Kwf_Model_Select_Expr_Like($field, '%'.$searchWord.'%');
                    }
                    $valuesOrs[] = new Kwf_Model_Select_Expr_Or($ors);
                } else {
                    $valuesOrs[] = new Kwf_Model_Select_Expr_Like($column, '%'.$searchWord.'%');
                }
            }
        }
        if ($valuesOrs) {
            return new Kwf_Model_Select_Expr_Or($valuesOrs);
        } else {
            return null;
        }
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_BOOLEAN;
    }

    public function validate()
    {
    }

    public function toArray()
    {
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'searchValues' => $this->_searchValues,
            'searchFields' => $this->_searchFields,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        return new $cls($data['searchValues'], $data['searchFields']);
    }
}
