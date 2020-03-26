<?php
/**
 * @package Model
 */
class Kwf_Model_Db extends Kwf_Model_Abstract
{
    protected $_rowClass = 'Kwf_Model_Db_Row';
    protected $_rowsetClass = 'Kwf_Model_Db_Rowset';
    protected $_table;
    protected $_db;
    private $_tableName;
    private $_columns;
    private $_columnTypes = array();
    private $_primaryKey;

    protected $_supportedImportExportFormats = array(self::FORMAT_SQL, self::FORMAT_CSV, self::FORMAT_ARRAY);

    private $_importBuffer;
    private $_importBufferOptions;
    private $_bufferLogFileName;

    public function __construct($config = array())
    {
        if (isset($config['tableName'])) {
            $this->_table = new $config['tableName']();
        }
        if (isset($config['table'])) {
            $this->_table = $config['table'];
        }
        if (isset($config['db'])) {
            $this->_db = $config['db'];
        }
        parent::__construct($config);
    }

    public function __sleep()
    {
        throw new Kwf_Exception_NotYetImplemented();
    }

    public function __destruct()
    {
        if (isset($this->_importBuffer)) {
            $this->writeBuffer();
        }
    }

    protected function _init()
    {
        parent::_init();
        if (!$this->_table) {
            if (isset($this->_name)) {
                throw new Kwf_Exception("You must rename _name to _table in '".get_class($this)."'");
            }
            throw new Kwf_Exception("No table set");
        }
    }

    public function getColumnType($col)
    {
        if (array_key_exists($col, $this->_columnTypes)) {
            return $this->_columnTypes[$col];
        }
        $info = $this->getTable()->info();
        if (isset($info['metadata'][$col])) {
            $type = $this->_getTypeFromDbType($info['metadata'][$col]['DATA_TYPE']);
            if ($col == 'pos' && $type == self::TYPE_BOOLEAN) {
                throw new Kwf_Exception('Column "pos" must not be of type TINYINT in table "' . $this->getTable()->getTableName() . '"');
            }
            $this->_columnTypes[$col] = $type;
            return $type;
        }
        return parent::getColumnType($col);
    }

    private function _getTypeFromDbType($type)
    {
        if ($type == 'varchar') $type = self::TYPE_STRING;
        else if (substr($type, 0, 7) == 'tinyint') $type = self::TYPE_BOOLEAN;
        else if ($type == 'text') $type = self::TYPE_STRING;
        else if ($type == 'tinytext') $type = self::TYPE_STRING;
        else if (substr($type, -3) == 'int') $type = self::TYPE_INTEGER;
        else if ($type == 'datetime') $type = self::TYPE_DATETIME;
        else if ($type == 'timestamp') $type = self::TYPE_DATETIME;
        else if ($type == 'date') $type = self::TYPE_DATE;
        else if ($type == 'decimal') $type = self::TYPE_FLOAT;
        else if ($type == 'float') $type = self::TYPE_FLOAT;
        else if (substr($type, 0, 6) == 'double') $type = self::TYPE_FLOAT;
        else if ($type == 'time') $type = null;
        else $type = null;
        return $type;
    }

    protected function _getOwnColumns()
    {
        if (!$this->_columns) {
            $cacheId = 'db_'.md5($this->getUniqueIdentifier()).'_columns';
            $this->_columns = Kwf_Cache_SimpleStatic::fetch($cacheId);
            if ($this->_columns === false) {
                $this->_columns = $this->getTable()->getColumns();
                Kwf_Cache_SimpleStatic::add($cacheId, $this->_columns);
            }
        }
        return $this->_columns;
    }

    public function createRow(array $data=array())
    {
        $ret = new $this->_rowClass(array(
            'row' => $this->getTable()->createRow(),
            'model' => $this
        ));
        $data = array_merge($this->_default, $data);
        foreach ($data as $k=>$i) {
            $ret->$k = $i;
        }
        return $ret;
    }

    public function beforeDelete($row)
    {
        $id = $this->_getUniqueId($row->getRow());
        unset($this->_rows[$id]);
    }

    /**
     * wird aufgerufen von row
     */
    public function afterInsert($row)
    {
        $id = $this->_getUniqueId($row->getRow());
        $this->_rows[$id] = $row;
    }

    protected function _getUniqueId($proxiedRow)
    {
        $keys = $this->getPrimaryKey();
        if (!is_array($keys)) return $proxiedRow->$keys;
        $ids = array();
        foreach ($keys as $key) {
            $key = $this->transformColumnName($key);
            $ids[] = $proxiedRow->$key;
        }
        return implode('_', $ids);
    }

    public function getRowByProxiedRow($proxiedRow)
    {
        $id = $this->_getUniqueId($proxiedRow);
        if (!isset($this->_rows[$id])) {
            $exprValues = array();
            foreach (array_keys($this->_exprs) as $k) {
                if (isset($proxiedRow->$k)) {
                    $exprValues[$k] = $proxiedRow->$k;
                }
            }
            $this->_rows[$id] = new $this->_rowClass(array(
                'row' => $proxiedRow,
                'model' => $this,
                'exprValues' => $exprValues
            ));
        }
        return $this->_rows[$id];
    }

    //public so we can access it from Model_Union, but not part of the public api
    public function _formatField($field, Zend_Db_Select $select = null, $tableNameAlias = null)
    {
        if ($field instanceof Zend_Db_Expr) return $field->__toString();

        if (in_array($field, $this->getOwnColumns())) {
            $f = $this->transformColumnName($field);
            return $this->_fieldWithTableName($f, $tableNameAlias);
        }
        $ret = $this->_formatFieldInternal($field, $select, $tableNameAlias);
        if ($ret === false) {
            throw new Kwf_Exception("Can't find field '$field' in model '".get_class($this)."' (Table '".$this->getTableName()."')");
        }

        return $ret;
    }

    protected function _fieldWithTableName($field, $tableNameAlias = null)
    {
        if ($tableNameAlias) {
            return $tableNameAlias.'.'.$field;
        } else {
            return $this->getTableName().'.'.$field;
        }
    }

    private function _formatFieldInternal($field, $dbSelect, $tableNameAlias)
    {
        $siblingOfModels = $this->_proxyContainerModels;
        $siblingOfModels[] = $this;
        foreach ($siblingOfModels as $siblingOf) {
            foreach ($siblingOf->getSiblingModels() as $k=>$m) {
                while ($m instanceof Kwf_Model_Proxy) {
                    $m = $m->getProxyModel();
                }
                if ($m instanceof Kwf_Model_Db) {
                    if (in_array($field, $m->getOwnColumns())) {
                        $ref = $m->getReferenceByModelClass(get_class($siblingOf), $k);
                        $siblingTableName = $m->getTableName();

                        $tablename = $tableNameAlias ? $tableNameAlias : $this->getTableName();
                        $joinCondition = $tablename.'.'.$this->getPrimaryKey()
                            .' = '.$siblingTableName.'.'.$ref['column'];
                        $alreadyJoined = false;
                        $fromPart = $dbSelect->getPart('from');
                        if ($fromPart) {
                            foreach ($fromPart as $join) {
                                if ($join['tableName'] == $siblingTableName && $join['joinCondition'] == $joinCondition) {
                                    $alreadyJoined = true;
                                    break;
                                }
                            }
                        }
                        if (!$alreadyJoined) {
                            $dbSelect->joinLeft($siblingTableName, $joinCondition, array());
                        }
                        // TODO: Eigentlich wäre _fieldWithTableName korrekt
                        // aber dann müsste auch der join über diese funktion laufen
                        return $m->getTableName().'.'.$field;
                    }
                    if (is_string($field)) {
                        $ret = $m->_formatFieldInternal($field, $dbSelect, $tableNameAlias);
                    } else {
                        $ret = $this->_createDbSelectExpression($field, $dbSelect, $m, $tableNameAlias);
                    }
                    if ($ret) return $ret;
                }
            }
        }
        return $this->_formatFieldExpr($field, $dbSelect, $tableNameAlias);
    }

    private function _formatFieldExpr($field, $dbSelect, $tableNameAlias = null)
    {
        $expr = false;

        $depOfModels = $this->_proxyContainerModels;
        $depOfModels[] = $this;
        foreach ($depOfModels as $depOf) {
            if (isset($depOf->_exprs[$field])) {
                $expr = $depOf->_exprs[$field];
                break; // setzt $depOf
            }
        }
        if (!$expr) return false;

        return $this->_createDbSelectExpression($expr, $dbSelect, $depOf, $tableNameAlias);
    }

    /**
     * Workaround
     *
     * @see http://framework.zend.com/issues/browse/ZF-1343
     * @see http://bugs.php.net/bug.php?id=44251
     */
    private function _fixStupidQuoteBug($v)
    {
        if ((strpos($v, '?') !== false || strpos($v, ':') !== false) && strpos($v, '\'') !== false) {
            $v = str_replace('\'', '', $v);
        }
        return $v;
    }

    public function createDbSelect($select, $tableNameAlias = null)
    {
        if (!$select) return null;
        if (!$this->_db && !Kwf_Registry::get('db')) {
            throw new Kwf_Model_Db_DatabaseNotConfiguredException("Database not configured.");
        }
        $tablename = $tableNameAlias ? $tableNameAlias : $this->getTableName();
        $dbSelect = $this->getTable()->select();
        if ($tableNameAlias) {
            $dbSelect->from(array(
                $tableNameAlias => $this->getTableName()
            ));
        } else {
            $dbSelect->from($this->getTableName());
        }
        $this->_applySelect($dbSelect, $select);
        return $dbSelect;
    }

    public function _applySelect(Zend_Db_Select $dbSelect, Kwf_Model_Select $select)
    {
        if ($dbSelect instanceof Kwf_Db_Table_Select) {
            $dbSelect->setIntegrityCheck(false);
        }

        if ($whereEquals = $select->getPart(Kwf_Model_Select::WHERE_EQUALS)) {
            foreach ($whereEquals as $field=>$value) {
                if (is_array($value)) {
                    if ($value) {
                        foreach ($value as &$v) {
                            if (!is_int($v)) {
                                $v = $this->_fixStupidQuoteBug($v);
                                $v = $this->getAdapter()->quote($v);
                            }
                        }
                        $value = implode(', ', $value);
                        $dbSelect->where($this->_formatField($field, $dbSelect)." IN ($value)");
                    } else {
                        $dbSelect->where('0');
                    }
                } else {
                    $value = $this->_fixStupidQuoteBug($value);
                    $dbSelect->where($this->_formatField($field, $dbSelect)." = ?", $value);
                }
            }
        }
        if ($whereNotEquals = $select->getPart(Kwf_Model_Select::WHERE_NOT_EQUALS)) {
            foreach ($whereNotEquals as $field=>$value) {
                if (is_array($value)) {
                    foreach ($value as &$v) {
                        if (!is_int($v)) {
                            $v = $this->_fixStupidQuoteBug($v);
                            $v = $this->getAdapter()->quote($v);
                        }
                    }
                    $value = implode(', ', $value);
                    $dbSelect->where($this->_formatField($field, $dbSelect)." NOT IN ($value)");
                } else {
                    $value = $this->_fixStupidQuoteBug($value);
                    $dbSelect->where($this->_formatField($field, $dbSelect)." != ?", $value);
                }
            }
        }
        if ($this->_hasDeletedFlag && !$select->getPart(Kwf_Model_Select::IGNORE_DELETED)) {
            $dbSelect->where("deleted = 0");
        }
        if ($where = $select->getPart(Kwf_Model_Select::WHERE)) {
            foreach ($where as $w) {
                $dbSelect->where($w[0], $w[1], $w[2]);
            }
        }

        if ($whereId = $select->getPart(Kwf_Model_Select::WHERE_ID)) {
            $whereId = $this->_fixStupidQuoteBug($whereId);
            $dbSelect->where($this->_formatField($this->getPrimaryKey(), $dbSelect)." = ?", $whereId);
        }

        if ($whereNull = $select->getPart(Kwf_Model_Select::WHERE_NULL)) {
            foreach ($whereNull as $field) {
                $dbSelect->where("ISNULL(".$this->_formatField($field, $dbSelect).")");
            }
        }

        if ($other = $select->getPart(Kwf_Model_Select::OTHER)) {
            foreach ($other as $i) {
                call_user_func_array(array($dbSelect, $i['method']), $i['arguments']);
            }
        }
        if ($whereExpression = $select->getPart(Kwf_Model_Select::WHERE_EXPRESSION)) {
            foreach ($whereExpression as $expr) {
                $expr->validate();
                $dbSelect->where($this->_createDbSelectExpression($expr, $dbSelect));
            }
        }

        if ($exprs = $select->getPart(Kwf_Model_Select::EXPR)) {
            foreach ($exprs as $field) {
                if (!$col = $this->_formatField($field, $dbSelect)) {
                    throw new Kwf_Exception("Expression '$field' not found");
                }
                $dbSelect->from(null, array($field=>new Zend_Db_Expr($col)));
            }
        }
    }

    private static function _getInnerDbModel2($model)
    {
        if ($model instanceof Kwf_Model_Db) return $model;
        if ($model instanceof Kwf_Model_Union) return $model;
        if ($model instanceof Kwf_Model_Proxy) {
            $ret = self::_getInnerDbModel2($model->getProxyModel());
            if ($ret) return $ret;
        }
        if ($model instanceof Kwf_Model_MirrorCacheSimple || $model instanceof Kwf_Model_RowsSubModel_MirrorCacheSimple) {
            $ret = self::_getInnerDbModel2($model->getSourceModel());
            if ($ret) return $ret;
        }
        return null;
    }

    private static function _getInnerDbModel($model)
    {
        if (is_string($model)) $model = Kwf_Model_Abstract::getInstance($model);
        $ret = self::_getInnerDbModel2($model);
        if (!$ret) {
            throw new Kwf_Exception_NotYetImplemented();
        }
        return $ret;
    }

    private function _createDbSelectExpression($expr, $dbSelect, $depOf = null, $tableNameAlias = null)
    {
        // wenn die expr von anderen models kommt (bei ProxyModel), dann
        // brauchen wir das model, dass die expr gesetzt hat (siehe Child oder Parent)
        if (is_null($depOf)) {
            $depOf = $this;
        }
        if ($expr instanceof Kwf_Model_Select_Expr_CompareField_Abstract) {
            $quotedValue = $expr->getValue();
            if (is_array($quotedValue)) {
                foreach ($quotedValue as &$v) {
                    $v = $this->_fixStupidQuoteBug($v);
                    $v = $this->getTable()->getAdapter()->quote($v);
                }
            } else if ($quotedValue instanceof Kwf_Model_Select_Expr_Interface) {
                $quotedValue = $this->_createDbSelectExpression($quotedValue, $dbSelect, $depOf, $tableNameAlias);
            } else {
                if ($quotedValue instanceof Kwf_DateTime) {
                    $quotedValue = $quotedValue->format('Y-m-d H:i:s');
                } else if ($quotedValue instanceof Kwf_Date) {
                    $quotedValue = $quotedValue->format('Y-m-d');
                }
                $quotedValue = $this->_fixStupidQuoteBug($quotedValue);
                $quotedValue = $this->getTable()->getAdapter()->quote($quotedValue);
            }
        }
        if ($expr instanceof Kwf_Model_Select_Expr_CompareField_Abstract ||
            $expr instanceof Kwf_Model_Select_Expr_IsNull
        ) {
            $field = $expr->getField();
            if ($field instanceof Kwf_Model_Select_Expr_Interface) {
                $field = $this->_createDbSelectExpression($field, $dbSelect, $depOf, $tableNameAlias);
            } else {
                $field = $this->_formatField($field, $dbSelect, $tableNameAlias);
            }
        }
        if ($expr instanceof Kwf_Model_Select_Expr_CompareField_Abstract) {
            if ($expr->getValue() instanceof Kwf_Date && !$expr->getValue() instanceof Kwf_DateTime) {
                if (is_string($expr->getField()) && $this->getColumnType($expr->getField()) == self::TYPE_DATETIME) {
                    //when comparing DATETIME field with '2016-06-09' apply DATE() to field
                    $field = 'DATE('.$field.')';
                }
            }
        }
        if ($expr instanceof Kwf_Model_Select_Expr_Equal) {
            if (is_array($quotedValue)) {
                return $field." IN (".implode(',', $quotedValue).")";
            } else {
                return $field." = ".$quotedValue;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_NotEquals) {
            if (is_array($quotedValue)) {
                return $field." NOT IN (".implode(',', $quotedValue).")";
            } else {
                return $field." != ".$quotedValue;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_IsNull) {
            return $field." IS NULL";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Lower) {
            return $field." < ".$quotedValue;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Higher) {
            return $field." > ".$quotedValue;
        } else if ($expr instanceof Kwf_Model_Select_Expr_LowerEqual) {
            return $field." <= ".$quotedValue;
        } else if ($expr instanceof Kwf_Model_Select_Expr_HigherEqual) {
            return $field." >= ".$quotedValue;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Like) {
            $quotedValue = str_replace("_", "\\_", $quotedValue);
            if ($expr instanceof Kwf_Model_Select_Expr_Contains) {
                $v = $expr->getValue();
                $v = $this->_fixStupidQuoteBug($v);
                $quotedValueContains = $this->getTable()->getAdapter()->quote('%'.$v.'%');

                $quotedValue = str_replace("%", "\\%", $quotedValue);
                $quotedValue = str_replace(
                                substr($quotedValueContains, 2, strlen($quotedValueContains)-4),
                                substr($quotedValue, 1, strlen($quotedValue)-2),
                                $quotedValueContains);
            }
            return $field." LIKE ".$quotedValue;
        } else if ($expr instanceof Kwf_Model_Select_Expr_RegExp) {
            return $field." REGEXP ".$quotedValue;
        } else if ($expr instanceof Kwf_Model_Select_Expr_StartsWith) {
            $v = $expr->getValue();
            $v = str_replace("_", "\\_", $v);
            $v = str_replace("%", "\\%", $v);
            $v .= '%';
            $v = $this->_fixStupidQuoteBug($v);
            $v = $this->getTable()->getAdapter()->quote($v);
            return "$field LIKE $v";
        } else if ($expr instanceof Kwf_Model_Select_Expr_NOT) {
            return "NOT (".$this->_createDbSelectExpression($expr->getExpression(), $dbSelect, $depOf, $tableNameAlias).")";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Or) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression, $dbSelect, $depOf, $tableNameAlias).")";
            }
            return '(' . implode(" OR ", $sqlExpressions) . ')';
        } else if ($expr instanceof Kwf_Model_Select_Expr_And) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression, $dbSelect, $depOf, $tableNameAlias).")";
            }
            return '(' . implode(" AND ", $sqlExpressions) . ')';
        } else if ($expr instanceof Kwf_Model_Select_Expr_Add) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression, $dbSelect, $depOf, $tableNameAlias).")";
            }
            return implode(" + ", $sqlExpressions);
        } else if ($expr instanceof Kwf_Model_Select_Expr_Subtract) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression, $dbSelect, $depOf, $tableNameAlias).")";
            }
            $sql = implode(" - ", $sqlExpressions);
            if (!$expr->lowerNullAllowed) {
                $sql = "IF (($sql > 0), $sql, 0)";
            }
            return $sql;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Divide) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression, $dbSelect, $depOf, $tableNameAlias).")";
            }
            $sql = implode(" / ", $sqlExpressions);
            return $sql;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Multiply) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression, $dbSelect, $depOf, $tableNameAlias).")";
            }
            $sql = implode(" * ", $sqlExpressions);
            return $sql;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Concat) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                            if ($expression instanceof Kwf_Model_Select_Expr_Interface) {
                    $sqlExpressions[] = $this->_createDbSelectExpression($expression, $dbSelect, $depOf, $tableNameAlias);
                } else {
                    $sqlExpressions[] = $this->_formatField($expression, $dbSelect, $tableNameAlias);
                }
            }
            return 'CONCAT('.implode(", ", $sqlExpressions).')';
        } else if ($expr instanceof Kwf_Model_Select_Expr_StrPad) {
            $field = $expr->getField();
            if ($field instanceof Kwf_Model_Select_Expr_Interface) {
                $field = $this->_createDbSelectExpression($field, $dbSelect, $depOf, $tableNameAlias);
            } else {
                $field = $this->_formatField($field, $dbSelect, $tableNameAlias);
            }
            if ($expr->getPadType() == Kwf_Model_Select_Expr_StrPad::RIGHT) {
                $pad = 'RPAD';
            } else if ($expr->getPadType() == Kwf_Model_Select_Expr_StrPad::LEFT) {
                $pad = 'LPAD';
            } else {
                throw new Kwf_Exception_NotYetImplemented();
            }
            return $pad."($field, {$expr->getPadLength()}, {$expr->getPadStr()})";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Date_Year) {
            $field = $expr->getField();
            $format = $expr->getFormat();
            if ($field instanceof Kwf_Model_Select_Expr_Interface) {
                $field = $this->_createDbSelectExpression($field, $dbSelect, $depOf, $tableNameAlias);
            } else {
                $field = $this->_formatField($field, $dbSelect, $tableNameAlias);
            }
            return "DATE_FORMAT($field, '%$format')";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Date_Format) {
            $field = $expr->getField();
            $format = $expr->getFormat();
            if ($field instanceof Kwf_Model_Select_Expr_Interface) {
                $field = $this->_createDbSelectExpression($field, $dbSelect, $depOf, $tableNameAlias);
            } else {
                $field = $this->_formatField($field, $dbSelect, $tableNameAlias);
            }
            $format = str_replace(array('Y', 'm', 'd', 'H', 'i'), array('%Y', '%m', '%d', '%H', '%i'), $format);
            return "DATE_FORMAT($field, '$format')";
        } else if ($expr instanceof Kwf_Model_Select_Expr_String) {
            $quotedString = $this->_fixStupidQuoteBug($expr->getString());
            $quotedString = $this->getTable()->getAdapter()->quote($quotedString);
            return $quotedString;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Boolean) {
            return $expr->getValue() ? new Zend_Db_Expr('TRUE') : new Zend_Db_Expr('FALSE');
        } else if ($expr instanceof Kwf_Model_Select_Expr_Integer) {
            return $expr->getValue();
        } else if ($expr instanceof Kwf_Model_Select_Expr_Count) {
            $field = $expr->getField();
            if ($field != '*') {
                $field = $this->_formatField($field, $dbSelect, $tableNameAlias);
            }
            if ($expr->getDistinct()) $field = "DISTINCT $field";
            return "COUNT($field)";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Sum) {
            $field = $this->_formatField($expr->getField(), $dbSelect, $tableNameAlias);
            return "SUM($field)";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Max) {
            $field = $this->_formatField($expr->getField(), $dbSelect, $tableNameAlias);
            return "MAX($field)";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Min) {
            $field = $this->_formatField($expr->getField(), $dbSelect, $tableNameAlias);
            return "MIN($field)";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Area) {
            $lat1 = $this->_formatField('latitude', $dbSelect, $tableNameAlias);
            // calculate radian-values because mysql sin and cos does only work with radians
            $lat2 = deg2rad($expr->getLatitude());
            $long1 = $this->_formatField('longitude', $dbSelect, $tableNameAlias);
            // calculate radian-values because mysql sin and cos does only work with radians
            $long2 = deg2rad($expr->getLongitude());
            $radius = $expr->getRadius();
            // calculate radian-values also directly with mysql because we don't have the
            // values for lat1 and long1 but their field-name/expression
            return "
                (ACOS(
                    SIN(RADIANS($lat1)) * SIN($lat2) +
                    COS(RADIANS($lat1)) * COS($lat2) *
                    COS($long2 - RADIANS($long1))
                ) * 6378.137) <= $radius
            ";
        } else if ($expr instanceof Kwf_Model_Select_Expr_GroupConcat) {
            $field = $this->_formatField($expr->getField(), $dbSelect, $tableNameAlias);
            $orderField = $expr->getOrderField();
            $orderFieldValue = $orderField ? $this->_formatField($orderField['field'], $dbSelect, $tableNameAlias) : null;
            $orderBy = $orderField ? 'ORDER BY '.$orderFieldValue.' '.$orderField['direction'] : '';
            return "GROUP_CONCAT($field $orderBy SEPARATOR ".$this->getAdapter()->quote($expr->getSeparator()).")";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Child) {
            $d = $depOf->getDependentModelWithDependentOf($expr->getChild());
            $depM = $d['model'];
            $ref = $d['model']->getReferenceByModelClass(get_class($d['dependentOf']), isset($d['rule']) ? $d['rule'] : null);

            $dbDepM = self::_getInnerDbModel($depM);
            $dbDepOf = self::_getInnerDbModel($depOf);

            $depTableName = $dbDepM->getTableName();
            $depSelect = $expr->getSelect();
            if (!$depSelect) $depSelect = $dbDepM->select();
            $depDbSelect = $dbDepM->_getDbSelect($depSelect);
            $depDbSelect->where($dbDepM->_formatField($ref['column'])."=".$dbDepOf->_formatField($dbDepOf->getPrimaryKey(), $depDbSelect, $tableNameAlias));
            $exprStr = new Zend_Db_Expr($dbDepM->_createDbSelectExpression($expr->getExpr(), $depDbSelect));
            $depDbSelect->reset(Zend_Db_Select::COLUMNS);
            $depDbSelect->from(null, $exprStr);
            return "($depDbSelect)";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Child_Contains) {
            $i = $depOf->getDependentModelWithDependentOf($expr->getChild());
            $depM = $i['model'];
            $depOf = $i['dependentOf'];

            $dbDepOf = $depOf;
            while ($dbDepOf instanceof Kwf_Model_Proxy) {
                $dbDepOf = $dbDepOf->getProxyModel();
            }
            if (!$dbDepOf instanceof Kwf_Model_Db) {
                throw new Kwf_Exception_NotYetImplemented();
            }

            $depM = Kwf_Model_Abstract::getInstance($depM);
            $dbDepM = $depM;
            while ($dbDepM instanceof Kwf_Model_Proxy) {
                $dbDepM = $dbDepM->getProxyModel();
            }
            if (!$dbDepM instanceof Kwf_Model_Db && !$dbDepM instanceof Kwf_Model_Union) {
                throw new Kwf_Exception_NotYetImplemented();
            }

            $d = $depOf->getDependentModelWithDependentOf($expr->getChild());
            $ref = $d['model']->getReferenceByModelClass(get_class($d['dependentOf']), isset($d['rule']) ? $d['rule'] : null);
            $depSelect = $expr->getSelect();
            if (!$depSelect) $depSelect = new Kwf_Model_Select();

            if ($dbDepM instanceof Kwf_Model_Union) {
                $depDbSelects = $dbDepM->getDbSelects($depSelect, array($ref['column']));
                $depDbSelect = Kwf_Registry::get('db')->select();
                $depDbSelect->union($depDbSelects);
            } elseif ($dbDepM instanceof Kwf_Model_Db) {
                $col1 = $dbDepM->_formatField($ref['column'], null /* select fehlt - welches ist das korrekte?*/);
                $depDbSelect = $dbDepM->_getDbSelect($depSelect);
                $depDbSelect->reset(Zend_Db_Select::COLUMNS);
                $depDbSelect->from(null, $col1);
            }
            return $this->_fieldWithTableName($this->getPrimaryKey(), $tableNameAlias)." IN ($depDbSelect)";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Child_First) {
            $i = $depOf->getDependentModelWithDependentOf($expr->getChild());
            $depM = $i['model'];
            $depOf = $i['dependentOf'];

            $dbDepOf = $depOf;
            while ($dbDepOf instanceof Kwf_Model_Proxy) {
                $dbDepOf = $dbDepOf->getProxyModel();
            }
            if (!$dbDepOf instanceof Kwf_Model_Db) {
                throw new Kwf_Exception_NotYetImplemented();
            }

            $depM = Kwf_Model_Abstract::getInstance($depM);
            $dbDepM = $depM;
            while ($dbDepM instanceof Kwf_Model_Proxy) {
                $dbDepM = $dbDepM->getProxyModel();
            }
            if (!$dbDepM instanceof Kwf_Model_Db && !$dbDepM instanceof Kwf_Model_Union) {
                throw new Kwf_Exception_NotYetImplemented();
            }

            $d = $depOf->getDependentModelWithDependentOf($expr->getChild());
            $ref = $d['model']->getReferenceByModelClass(get_class($d['dependentOf']), isset($d['rule']) ? $d['rule'] : null);
            $depSelect = $expr->getSelect();
            if (!$depSelect) {
                $depSelect = $dbDepM->select();
            } else {
                //wir führen unten ein where aus, das darf nicht im original select bleiben
                $depSelect = clone $depSelect;
            }
            $col1 = $dbDepM->transformColumnName($ref['column']);
            $col2 = $dbDepOf->transformColumnName($dbDepOf->getPrimaryKey());
            $depSelect->where("{$dbDepM->_formatField($col1)}={$dbDepOf->_formatField($col2, null, $tableNameAlias)}");
            $depDbSelect = $dbDepM->_getDbSelect($depSelect);
            $exprStr = new Zend_Db_Expr($dbDepM->_formatField($expr->getField(), $depDbSelect));
            $depDbSelect->reset(Zend_Db_Select::COLUMNS);
            $depDbSelect->from(null, $exprStr);
            $depDbSelect->limit(1);
            return "($depDbSelect)";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Parent) {
            $refM = $depOf->getReferencedModel($expr->getParent());
            $dbRefM = self::_getInnerDbModel($refM);
            $dbDepOf = self::_getInnerDbModel($depOf);
            $ref = $depOf->getReference($expr->getParent());
            $refSelect = $dbRefM->select();
            $refSelect->ignoreDeleted(true);
            if ($ref === Kwf_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
                $ref = $dbDepOf->getReferenceByModelClass($depOf->getParentModel(), null);
            }
            $col1 = $dbDepOf->_formatField($ref['column'], $dbSelect, $tableNameAlias);
            if ($dbRefM instanceof Kwf_Model_Union) {
                $ret = "CASE\n";
                $f = $expr->getField();
                $unionModels = $dbRefM->getUnionModels();
                if (is_string($f)) {
                    foreach ($dbRefM->getDbSelects($refSelect, array($expr->getField())) AS $modelKey=>$dbSelect) {
                        $m = self::_getInnerDbModel($unionModels[$modelKey]);
                        $idCol = $m->_formatField($m->getPrimaryKey(), $dbSelect);
                        $dbSelect->where("$idCol=SUBSTR($col1, ".(strlen($modelKey)+1).")");
                        $ret .= "  WHEN SUBSTR($col1, 1, ".strlen($modelKey).")='$modelKey' THEN ($dbSelect)\n";
                    }
                } else {
                    $refSelect->where($f);
                    foreach ($dbRefM->getDbSelects($refSelect) as $modelKey=>$dbSelect) {
                        $m = self::_getInnerDbModel($unionModels[$modelKey]);
                        $idCol = $m->_formatField($m->getPrimaryKey(), $dbSelect);
                        $dbSelect->where("$idCol=SUBSTR($col1, ".(strlen($modelKey)+1).")");
                        $ret .= "  WHEN SUBSTR($col1, 1, ".strlen($modelKey).")='$modelKey' THEN ($dbSelect)=$col1\n";
                    }
                }
                $ret .= "END";
                return "$ret";
            } else {

                static $parentNum = 0;
                $refTableNameAlias = $dbRefM->getTableName().'_parent'.($parentNum++);
                $col2 = $dbRefM->_formatField($dbRefM->getPrimaryKey(), $dbSelect, $refTableNameAlias);

                $refSelect->where("$col2=$col1");

                $refDbSelect = $dbRefM->createDbSelect($refSelect, $refTableNameAlias);
                $f = $expr->getField();
                if (is_string($f)) {
                    $exprStr = new Zend_Db_Expr($dbRefM->_formatField($f, $refDbSelect, $refTableNameAlias));
                } else {
                    $exprStr = new Zend_Db_Expr($dbRefM->_createDbSelectExpression($f, $refDbSelect, $refM, $refTableNameAlias));
                }
                $refDbSelect->reset(Zend_Db_Select::COLUMNS);
                $refDbSelect->from(null, $exprStr);
                return "($refDbSelect)";
            }

        } else if ($expr instanceof Kwf_Model_Select_Expr_Field) {
            $field = $this->_formatField($expr->getField(), $dbSelect, $tableNameAlias);
            return $field;
        } else if ($expr instanceof Kwf_Model_Select_Expr_PrimaryKey) {
            $field = $this->_formatField($this->getPrimaryKey(), $dbSelect, $tableNameAlias);
            return $field;
        } else if ($expr instanceof Kwf_Model_Select_Expr_SumFields) {
            $sqlExpressions = array();
            foreach ($expr->getFields() as $expression) {
                if (is_int($expression)) {
                    $sqlExpressions[] = $expression;
                } else if (is_string($expression)) {
                    $sqlExpressions[] = $this->_formatField($expression, $dbSelect, $tableNameAlias);
                } else if ($expression instanceof Kwf_Model_Select_Expr_Interface) {
                    $sqlExpressions[] = $this->_createDbSelectExpression($expression, $dbSelect, $tableNameAlias);
                } else {
                    throw new Kwf_Exception_NotYetImplemented();
                }
            }
            return '('.implode('+ ', $sqlExpressions).')';
        } else if ($expr instanceof Kwf_Model_Select_Expr_Sql) {
            foreach ($expr->getUsedColumns() as $f) {
                //mit dem rückgabewert nichts machen, das ist nur zum joinen von sibling models
                $this->_formatFieldInternal($f, $dbSelect, $tableNameAlias);
            }
            $sql = $expr->getSql();
            if (preg_match_all('#expr\{([a-zA-Z0-9_]+)\}#', $sql, $exprs)) {
                foreach ($exprs[1] as $k => $e) {
                    $sql = str_replace($exprs[0][$k], $this->_formatField($e, $dbSelect, $tableNameAlias), $sql);
                }
            }
            return '('.$sql.')';
        } else if ($expr instanceof Kwf_Model_Select_Expr_If) {
            $if = $this->_createDbSelectExpression($expr->getIf(), $dbSelect, $depOf, $tableNameAlias);
            $then = $this->_createDbSelectExpression($expr->getThen(), $dbSelect, $depOf, $tableNameAlias);
            $else = $this->_createDbSelectExpression($expr->getElse(), $dbSelect, $depOf, $tableNameAlias);
            return "IF($if, $then, $else)";
        } else if ($expr instanceof Kwf_Model_Select_Expr_Position) {
            $field = $this->_formatField($expr->getField(), $dbSelect, $tableNameAlias);
            static $positionNum = 0;
            $alias = ($tableNameAlias ? $tableNameAlias : $this->getTableName()) . '_expr_position'.($positionNum++);
            $aliasIdField = $this->_formatField($this->getPrimaryKey(), null, $alias);
            $aliasField = $this->_formatField($expr->getField(), null, $alias);
            if ($expr->getDirection() == Kwf_Model_Select_Expr_Position::DIRECTION_ASC) {
                $direction = '<';
            } else {
                $direction = '>';
            }
            $ret = "SELECT COUNT($aliasIdField)+1 FROM ".$this->getTableName()." $alias
                WHERE $aliasField $direction $field";
            foreach ($expr->getGroupBy() as $g) {
                $field = $this->_formatField($g, $dbSelect, $tableNameAlias);
                $aliasField = $this->_formatField($g, null, $alias);
                $ret .= " AND $field=$aliasField";
            }
            return "($ret)";
        } elseif ($expr instanceof Kwf_Model_Select_Expr_Date_Age) {
            $birthDate = $this->_formatField($expr->getField(), $dbSelect, $tableNameAlias);
            $referenceYear = $expr->getDate()->format('Y');
            $referenceDate = $expr->getDate()->format();
            return "IF($birthDate,($referenceYear-YEAR($birthDate)) - (RIGHT('$referenceDate',5)<RIGHT($birthDate,5)), NULL)";
        } else if ($expr instanceof Kwf_Model_Select_Expr_SearchLike) {
            $e = $expr->getQueryExpr($this);
            if (!$e) return 'TRUE';
            return $this->_createDbSelectExpression($e, $dbSelect, $depOf, $tableNameAlias);
        } else if ($expr instanceof Kwf_Model_Select_Expr_Parent_Contains) {
            $dbRefM = self::_getInnerDbModel($depOf->getReferencedModel($expr->getParent()));
            $dbDepOf = self::_getInnerDbModel($depOf);
            $refTableName = $dbRefM->getTableName();
            $ref = $depOf->getReference($expr->getParent());
            if ($ref === Kwf_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
                $ref = $dbDepOf->getReferenceByModelClass($depOf->getParentModel(), null);
            }
            $refDbSelect = $dbRefM->createDbSelect($expr->getSelect());
            $refDbSelect->reset(Zend_Db_Select::COLUMNS);
            $refDbSelect->from(null, $dbRefM->_formatField($dbRefM->getPrimaryKey(), $refDbSelect));
            $ret = $this->_formatField($ref['column'], $dbSelect)." IN ($refDbSelect)";
            return $ret;
        } else {
            throw new Kwf_Exception_NotYetImplemented("Expression not yet implemented: ".get_class($expr));
        }
    }

    public function getExprValue($row, $name)
    {
        if ($name instanceof Kwf_Model_Select_Expr_Interface) {
            $expr = $name;
        } else {
            $expr = $this->_exprs[$name];
        }
        if ($expr instanceof Kwf_Model_Select_Expr_Sql) {
            $select = $this->select();
            $select->whereEquals($this->getPrimaryKey(), $row->{$this->getPrimaryKey()});
            $select->limit(1);
            $options = array(
                'columns' => array($name)
            );
            $dbSelect = $this->_createDbSelectWithColumns($select, $options);
            return $dbSelect->query()->fetchColumn();
        }

        return parent::getExprValue($row, $name);
    }

    //Nur zum Debuggen verwenden!
    public function getSqlForSelect($select)
    {
        $dbSelect = $this->_getDbSelect($select);
        return $dbSelect->__toString();
    }

    public function getRow($select)
    {
        if (is_string($select) || is_int($select)) {
            //primary key given, skip query and use already existing row
            if (!is_array($this->getPrimaryKey())) {
                if (isset($this->_rows[$select])) {
                    return $this->_rows[$select];
                }
            }
        }
        return parent::getRow($select);
    }

    public function getIds($select = array(), $order=null, $limit=null, $start=null)
    {
        if (!is_object($select)) {
            $select = $this->select($select, $order, $limit, $start);
        }
        $rows = $this->export(
            Kwf_Model_Abstract::FORMAT_ARRAY,
            $select,
            array('columns'=>array($this->getPrimaryKey()))
        );
        $ret = array();
        foreach ($rows as $i) {
            $ret[] = $i[$this->getPrimaryKey()];
        }
        return $ret;
    }

    public function getRows($where = array(), $order=null, $limit=null, $start=null)
    {
        $dbSelect = $this->_getDbSelect($where, $order, $limit, $start);
        return new $this->_rowsetClass(array(
            'rowset' => $this->getTable()->fetchAll($dbSelect),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    private function _getTableUpdateWhere($where)
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where);
        } else {
            $select = $where;
        }
        if ($select->getPart(Kwf_Model_Select::OTHER) ||
            $select->getPart(Kwf_Model_Select::LIMIT_COUNT) ||
            $select->getPart(Kwf_Model_Select::LIMIT_OFFSET))
            throw new Kwf_Exception('Select for update must only contain where* parts');
        $dbSelect = new Zend_Db_Select($this->getAdapter());
        $this->_applySelect($dbSelect, $select);
        $where = array();
        foreach ($dbSelect->getPart('where') as $part) {
            if (substr($part, 0, 4) == 'AND ') $part = substr($part, 4);
            $where[] = $part;
        }
        return $where;
    }

    public function deleteRows($where)
    {
        if ($this->hasDeletedFlag()) {
            $ret = $this->updateRows(array('deleted' => true), $where);
        } else {
            $ret = $this->getTable()->delete($this->_getTableUpdateWhere($where));
        }
        $this->_afterDeleteRows($where);
        return $ret;
    }

    public function updateRows($data, $where)
    {
        return $this->getTable()->update($data, $this->_getTableUpdateWhere($where));
    }

    private function _getDbSelect($where, $order=null, $limit=null, $start=null)
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        $dbSelect = $this->createDbSelect($select);
        if ($order = $select->getPart(Kwf_Model_Select::ORDER)) {
            foreach ($order as $o) {
                if ($o['direction']) {
                    if ($o['direction'] != 'ASC' && $o['direction'] != 'DESC') {
                        throw new Kwf_Exception("Invalid direction");
                    }
                }
                if ($o['field'] instanceof Zend_Db_Expr) {
                    $dbSelect->order($o['field']);
                } else if ($o['field'] instanceof Kwf_Model_Select_Expr_Interface) {
                    $dbSelect->order(new Zend_Db_Expr($this->_createDbSelectExpression($o['field'], $dbSelect).' '.$o['direction']));
                } else if ($o['field'] == Kwf_Model_Select::ORDER_RAND) {
                    $dbSelect->order('RAND()');
                } else {
                    $o['field'] = $this->_formatField($o['field'], $dbSelect);
                    $dbSelect->order(new Zend_Db_Expr($o['field'].' '.$o['direction']));
                }
            }
        }
        $limitCount = $select->getPart(Kwf_Model_Select::LIMIT_COUNT);
        $limitOffset = $select->getPart(Kwf_Model_Select::LIMIT_OFFSET);
        if ($limitCount || $limitOffset) {
            $dbSelect->limit($limitCount, $limitOffset);
        }

        if ($select->hasPart(Kwf_Model_Select::UNION)) {
            $unions = array($dbSelect);
            foreach ($select->getPart(Kwf_Model_Select::UNION) as $unionSel) {
                $unions[] = $this->_getDbSelect($unionSel);
            }
            $dbSelect = $this->getTable()->select()->union($unions);
        }

        return $dbSelect;
    }

    public function countRows($select = array())
    {
        if (!is_object($select)) {
            $select = $this->select($select);
        }
        if ($select->hasPart(Kwf_Model_Select::UNION)) {
            throw new Kwf_Exception_NotYetImplemented();
        }

        $dbSelect = $this->createDbSelect($select);
        $dbSelect->reset(Zend_Db_Select::COLUMNS);
        $dbSelect->setIntegrityCheck(false);
        if ($dbSelect->getPart('group')) {
            $group = $dbSelect->getPart('group');
            if (count($group) > 1) {
                $group = implode(', ', $group);
                $dbSelect->reset(Zend_Db_Select::GROUP);
                $dbSelect->from(null, new Zend_Db_Expr("DISTINCT $group c"));
                $outerDbSelect = new Zend_Db_Select($this->getTable()->getAdapter());
                $outerDbSelect->from($dbSelect, new Zend_Db_Expr("COUNT(*) d"));
                $dbSelect = $outerDbSelect;
            } else {
                $group = current($group);
                $dbSelect->reset(Zend_Db_Select::GROUP);
                $dbSelect->from(null, new Zend_Db_Expr("COUNT(DISTINCT $group) c"));
            }
        } else {
            $dbSelect->from(null, new Zend_Db_Expr('COUNT(*) c'));
        }
        return $this->getTable()->getAdapter()->query($dbSelect)->fetchColumn();
    }

    public function evaluateExpr(Kwf_Model_Select_Expr_Interface $expr, Kwf_Model_Select $select = null)
    {
        if (is_null($select)) $select = $this->select();
        $dbSelect = $this->createDbSelect($select);
        $dbSelect->reset(Zend_Db_Select::COLUMNS);
        $dbSelect->setIntegrityCheck(false);
        $dbSelect->from(null, $this->_createDbSelectExpression($expr, $dbSelect));
        return $this->getTable()->getAdapter()->query($dbSelect)->fetchColumn();
    }

    public function getPrimaryKey()
    {
        if (!$this->_primaryKey) {
            $cacheId = 'db_'.md5($this->getUniqueIdentifier()).'_primaryKey';
            $this->_primaryKey = Kwf_Cache_SimpleStatic::fetch($cacheId);
            if ($this->_primaryKey === false) {
                $this->_primaryKey = $this->getTable()->getPrimaryKey();
                if (sizeof($this->_primaryKey) == 1) {
                    $this->_primaryKey = array_values($this->_primaryKey);
                    $this->_primaryKey = $this->_primaryKey[0];
                }
                Kwf_Cache_SimpleStatic::add($cacheId, $this->_primaryKey);
            }
        }
        return $this->_primaryKey;
    }

    public function getTable()
    {
        if (is_string($this->_table)) {
            $this->_tableName = $this->_table;
            $this->_table = new Kwf_Db_Table(array(
                'name' => $this->_table,
                'db' => $this->_db
            ));
        }
        return $this->_table;
    }

    public function getAdapter()
    {
        return $this->getTable()->getAdapter();
    }

    public function getTableName()
    {
        if (!$this->_tableName) {
            if (is_string($this->_table)) return $this->_table;
            return $this->_table->getTableName();
        }
        return $this->_tableName;
    }

    public function isEqual(Kwf_Model_Interface $other) {
        if ($other instanceof Kwf_Model_Db &&
            $this->getTableName() == $other->getTableName()) {
            return true;
        }
        return false;
    }

    public function select($where = array(), $order = null, $limit = null, $start = null)
    {
        if (!is_array($where)) {
            $ret = new Kwf_Model_Select();
            if ($where) {
                $ret->whereEquals($this->getPrimaryKey(), $where);
            }
        } else {
            $ret = new Kwf_Model_Select($where);
        }
        if ($order) $ret->order($order);
        if ($limit || $start) $ret->limit($limit, $start);
        return $ret;
    }

    public function getUniqueIdentifier()
    {
        return ($this->_db ? $this->_db.'.' : '') . $this->getTableName();
    }

    //public so we can access it from Model_Union, but not part of the public api
    public function _createDbSelectWithColumns($select, $options)
    {
        $select = clone $select;
        if (isset($options['columns'])) {
            foreach ($options['columns'] as $c) {
                $select->expr($c);
            }
        }
        $dbSelect = $this->_getDbSelect($select);
        if (isset($options['columns'])) {
            $columns = $dbSelect->getPart(Zend_Db_Select::COLUMNS);
            unset($columns[0]); //unset *
            $dbSelect->reset(Zend_Db_Select::COLUMNS);
            $dbSelect->setPart(Zend_Db_Select::COLUMNS, $columns);
        }

        return $dbSelect;
    }

    public function export($format, $select = array(), $options = array())
    {
        if ($format == self::FORMAT_SQL) {
            $wherePart = '';
            if ($select) {
                if (!is_object($select)) {
                    if (is_string($select)) $select = array($select);
                    $select = $this->select($select);
                }
                $dbSelect = $this->_getDbSelect($select);
                $whereParts = $dbSelect->getPart(Zend_Db_Select::WHERE);
                $wherePart = implode(' ', $whereParts);

                // check if a row is exported and quit, if none
                $dbSelect->limit(1);
                $hasRows = $dbSelect->query()->fetchAll();
                if (!count($hasRows)) {
                    return '';
                }
            }
            if ($wherePart) {
                $wherePart = '--where="'.$wherePart.'" ';
            }

            $systemData = $this->_getSystemData();
            $filename = tempnam('/tmp', 'modelimport');

            $cmd = "{$systemData['mysqlDir']}mysqldump --add-drop-table=false ";
            $cmd .= "--skip-add-locks --complete-insert ";
            $cmd .= "--no-create-info=true ".$wherePart
                ."$systemData[mysqlOptions] $systemData[tableName] | gzip -c > $filename";
            exec($cmd, $output, $ret);
            if ($ret != 0) throw new Kwf_Exception("SQL export failed");
            $ret = file_get_contents($filename);
            unlink($filename);
            return $ret;
        } else if ($format == self::FORMAT_CSV) {
            if (!is_object($select)) {
                if (is_string($select)) $select = array($select);
                $select = $this->select($select);
            }

            $tmpExportFolder = realpath('temp').'/modelcsvex'.uniqid();
            $filename = $tmpExportFolder.'/csvexport';

            $dbSelect = $this->_createDbSelectWithColumns($select, $options);
            $sqlString = $dbSelect->assembleIntoOutfile($filename);

            $dbSelect->limit(1);
            $fieldResult = $dbSelect->query()->fetchAll();
            $columnsCsv = '';
            if (count($fieldResult)) {
                mkdir($tmpExportFolder, 0777);
                $columns = array_keys($fieldResult[0]);
                $columnsCsv = '"'.implode('","', $columns).'"';
                $this->executeSql($sqlString);
                $cmd = "{ echo '$columnsCsv'; cat $filename; } | gzip -c > $filename.gz";
                exec($cmd, $output, $ret);
                if ($ret != 0) throw new Kwf_Exception("CSV-SQL export failed");

                if (!file_exists($filename.'.gz')) {
                    throw new Kwf_Exception("Error exporting csv from model - target file has not been created");
                }
                unlink($filename);

                $ret = file_get_contents($filename.'.gz');
                unlink($filename.'.gz');
                rmdir($tmpExportFolder);
                return $ret;
            } else {
                return '';
            }
        } else if ($format == self::FORMAT_ARRAY) {
            if (!is_object($select)) {
                if (is_string($select)) $select = array($select);
                $select = $this->select($select);
            }
            if ($select->hasPart(Kwf_Model_Select::UNION)) {
                $select = clone $select;
                $unions = $select->getPart(Kwf_Model_Select::UNION);
                $select->unsetPart(Kwf_Model_Select::UNION);
                $selects = array($select);
                $selects = array_merge($selects, $unions);
                $ret = array();
                while ($selects) {
                    //split up into blocks of 150, mysql doesn't take more
                    $curSelects = array_splice($selects, 0, min(150, count($selects)));
                    $unions = array();
                    foreach ($curSelects as $s) {
                        $unions[] = $this->_createDbSelectWithColumns($s, $options);
                    }
                    $ret = array_merge($ret, $this->getAdapter()->query(implode(" UNION ", $unions))->fetchAll());
                }
                return $ret;
            } else {
                $dbSelect = $this->_createDbSelectWithColumns($select, $options);
                if (!$dbSelect) return array();
                $ret = $this->getAdapter()->query($dbSelect)->fetchAll();
                foreach ($this->getOwnColumns() as $c) {
                    $transformedColumn = $this->transformColumnName($c);
                    if ($transformedColumn != $c) {
                        foreach ($ret as $k=>$i) {
                            if (isset($i[$transformedColumn])) {
                                $ret[$k][$c] = $i[$transformedColumn];
                                unset($ret[$k][$transformedColumn]);
                            }
                        }
                    }
                }
                return $ret;
            }

        } else {
            return parent::export($format, $select);
        }
    }

    protected function _updateModelObserver($options, array $ids = null)
    {
        if (isset($options['skipModelObserver']) && $options['skipModelObserver']) return;

        Kwf_Events_ModelObserver::getInstance()->add('update', $this, $ids);
    }

    public function import($format, $data, $options = array())
    {
        if ($format == self::FORMAT_SQL) {
            // if no data is recieved, quit
            if (!$data) return;

            $filename = tempnam('/tmp', 'modelimport');
            file_put_contents($filename, $data);

            $systemData = $this->_getSystemData();
            $cmd = "gunzip -c $filename ";
            if (isset($options['replace']) && $options['replace']) {
                $cmd .= "| sed -e \"s|INSERT INTO|REPLACE INTO|\"";
            }
            if (isset($options['ignorePrimaryKey']) && $options['ignorePrimaryKey']) {
                $primaryKey = $this->getPrimaryKey();
                $cmd .= " | sed -e \"s|(\`".$primaryKey."\`,|(|\" -e \"s|([0-9]*,|(|g\"";
            }

            $cmd .= "| {$systemData['mysqlDir']}mysql $systemData[mysqlOptions] 2>&1";
            exec($cmd, $output, $ret);
            unlink($filename);
            $this->_updateModelObserver($options, null);
            if ($ret != 0) throw new Kwf_Exception("SQL import failed: ".implode("\n", $output));
            $this->_afterImport($format, $data, $options);
        } else if ($format == self::FORMAT_CSV) {
            // if no data is recieved, quit
            if (!$data) return;

            $tmpImportFolder = tempnam('temp/', 'modelcsvim');
            unlink($tmpImportFolder);
            mkdir($tmpImportFolder, 0777);
            $filename = $tmpImportFolder.'/csvimport';
            file_put_contents($filename.'.gz', $data);

            $cmd = "gunzip -c $filename.gz > $filename"
                ." && head --lines=1 $filename | sed -e 's|\"|`|g'";
            exec($cmd, $output, $ret);
            if ($ret != 0) throw new Kwf_Exception("CSV-SQL export failed");

            $fieldNames = trim($output[0]);
            if ($fieldNames) {
                // set the character_set_database MySQL system variable to utf8
                $this->executeSql("SET character_set_database = 'utf8'");

                $sqlString = "LOAD DATA INFILE '$filename'";
                if (isset($options['replace']) && $options['replace']) {
                    $sqlString .= " REPLACE";
                }
                $sqlString .= " INTO TABLE `".($this->getTableName())."`";
                $sqlString .= " FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\\\\' LINES TERMINATED BY '\\n'";
                $sqlString .= " IGNORE 1 LINES";
                $sqlString .= " ($fieldNames)";

                $this->executeSql($sqlString);
            }

            unlink($filename.'.gz');
            unlink($filename);
            rmdir($tmpImportFolder);
            $this->_updateModelObserver($options, null);
            $this->_afterImport($format, $data, $options);
        } else if ($format == self::FORMAT_ARRAY) {
            $ids = array();
            foreach ($data as $k => $v) {
                if (is_array($ids) && !is_array($this->getPrimaryKey()) && isset($v[$this->getPrimaryKey()])) {
                    $ids[] = $v[$this->getPrimaryKey()];
                } else {
                    //if we don't know all imported ids, pass null
                    $ids = null;
                }
            }
            if (isset($options['buffer']) && $options['buffer']) {
                if (isset($this->_importBuffer)) {
                    if ($options != $this->_importBufferOptions) {
                        throw new Kwf_Exception_NotYetImplemented("You can't buffer imports with different options (not yet implemented)");
                    }
                    $this->_importBuffer = array_merge($this->_importBuffer, $data);
                    // handling for not sending too much data to mysql in one query
                    // is in _importArray() function
                } else {
                    $this->_importBufferOptions = $options;
                    $this->_importBuffer = $data;
                }
                if ($this->_getBufferLogFileName()) {
                    file_put_contents($this->_getBufferLogFileName(), count($this->_importBuffer));
                }
                if ($options['buffer'] !== true && count($this->_importBuffer) > $options['buffer']) {
                    $this->writeBuffer();
                }
            } else {
                $this->_importArray($data, $options);
            }
            $this->_updateModelObserver($options, $ids);
            $this->_afterImport($format, $data, $options);
        } else {
            parent::import($format, $data);
        }
    }

    private function _getSystemData()
    {
        $ret = array();

        $dbConfig = Zend_Registry::get('db')->getConfig();
        $ret['mysqlOptions'] = "--host={$dbConfig['host']} --user={$dbConfig['username']} --password={$dbConfig['password']} {$dbConfig['dbname']} ";
        $config = Zend_Registry::get('config');

        $ret['mysqlDir'] = '';
        if (trim(`hostname`) == "vivid-sun") {
            $ret['mysqlDir'] = '/usr/local/mysql/bin/';
        }
        $ret['tableName'] = $this->getTableName();

        return $ret;
    }

    private function _getBufferLogFileName()
    {
        if (!isset($this->_bufferLogFileName)) {
            if (Kwf_Config::getValue('debug.dbBuffer')) {
                foreach (glob('cache/model/db-buffer-*') as $i) {
                    $time = (int)substr($i, 15);
                    if (time() - $time > 15) {
                        $bufferSize = file_get_contents($i);
                        $e = new Kwf_Exception("Not written import buffer with size '$bufferSize' found (age: ".(time()-$time)."): '$i'");
                        $e->logOrThrow();
                    }
                }
                $this->_bufferLogFileName = 'cache/model/db-buffer-'.time().'-'.uniqid();
            } else {
                $this->_bufferLogFileName = false;
            }
        }
        return $this->_bufferLogFileName;
    }

    public function writeBuffer()
    {
        parent::writeBuffer();
        if (isset($this->_importBuffer)) {
            $this->_importArray($this->_importBuffer, $this->_importBufferOptions);
            if ($this->_getBufferLogFileName()) {
                unlink($this->_getBufferLogFileName());
            }
            unset($this->_importBuffer);
            unset($this->_importBufferOptions);
        }
    }

    private function _importArray($data, $options)
    {
        if (empty($data)) return;
        if ($this->getSiblingModels()) {
            if ($options) {
                throw new Kwf_Exception_NotYetImplemented("import options together with siblingModels are not yet implemented");
            }
            foreach ($data as $r) {
                $this->createRow($r)->save();
            }
            return;
        }
        $data = array_values($data);
        $fields = array_keys($data[0]);
        if (isset($options['replace']) && $options['replace']) {
            $sqlTableAndColumns = 'REPLACE';
        } else {
            $sqlTableAndColumns = 'INSERT';
        }
        foreach ($fields as &$f) {
            $f = $this->transformColumnName($f);
        }
        if (isset($options['ignore']) && $options['ignore'])
            $sqlTableAndColumns .= ' IGNORE';
        $sqlTableAndColumns .= ' INTO '.$this->getTableName().' (`'.implode('`, `', $fields).'`) VALUES ';
        $sqlValues = '';
        foreach ($data as $d) {
            if (array_keys($d) != array_keys($data[0])) {
                throw new Kwf_Exception_NotYetImplemented("You must have always the same keys when importing");
            }
            $sqlValues .= '(';
            foreach ($d as $i) {
                if (is_null($i)) {
                    $sqlValues .= 'NULL';
                } else {
                    $sqlValues .= $this->getTable()->getAdapter()->quote($i);
                }
                $sqlValues .= ',';
            }
            $sqlValues = substr($sqlValues, 0, -1);
            $sqlValues .= '),';

            // write buffer when mysql string will get over 700K
            // normally mysql supports string up to 1M
            if (strlen($sqlValues) > 700000) {
                $this->executeSql($sqlTableAndColumns.substr($sqlValues, 0, -1));
                $sqlValues = '';
            }
        }
        if ($sqlValues) {
            $this->executeSql($sqlTableAndColumns.substr($sqlValues, 0, -1));
        }
    }

    public function executeSql($sql)
    {
        // Performance, bei Pdo wird der Adapter umgangen
        if ($this->getTable()->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mysql) {
            $q = $this->getTable()->getAdapter()->getProfiler()->queryStart($sql, Zend_Db_Profiler::INSERT);
            $this->getTable()->getAdapter()->getConnection()->exec($sql);
            $this->getTable()->getAdapter()->getProfiler()->queryEnd($q);
        } else {
            $this->getTable()->getAdapter()->query($sql);
        }
    }

    public function getSupportedImportExportFormats()
    {
        if ($this->getSiblingModels()) {
            return array(self::FORMAT_ARRAY);
        } else {
            $ret = $this->_supportedImportExportFormats;
            foreach ($ret as $k => $v) {
                if ($v === self::FORMAT_CSV) {
                    // check if csv is possible with current database rights
                    if (!Kwf_Util_Mysql::getFileRight()) {
                        unset($ret[$k]);
                    }
                } else if ($v === self::FORMAT_SQL) {
                    // check if mysql is available (mainly because of POI Servers,
                    // where mysql is on another server)
                    exec("whereis mysql", $output, $execRet);

                    if ($output && is_array($output) && trim($output[0]) != 'mysql:') {
                        // hier bleibts drin
                    } else {
                        unset($ret[$k]);
                    }
                }
            }
            $ret = array_values($ret);
            return $ret;
        }
    }

    public function fetchColumnByPrimaryId($column, $id)
    {
        if (in_array($column, $this->_getOwnColumns())) {
            $sql = "SELECT $column FROM ".$this->getTableName()." WHERE ".$this->getPrimaryKey()."=?";
            return Kwf_Registry::get('db')->query($sql, $id)->fetchColumn();
        } else {
            return parent::fetchColumnByPrimaryId($column, $id);
        }
    }

    public function fetchColumnsByPrimaryId(array $columns, $id)
    {
        $sql = "SELECT ".implode(',', $columns)." FROM ".$this->getTableName()." WHERE ".$this->getPrimaryKey()."=?";
        return Kwf_Registry::get('db')->query($sql, $id)->fetch();
    }

    /**
     * @internal
     */
    public function convertValueType($column, $value)
    {
        $type = $this->getColumnType($column);
        return self::convertValueToType($value, $type);
    }
}
