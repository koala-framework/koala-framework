<?php
class Vps_Model_Db extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_Db_Row';
    protected $_rowsetClass = 'Vps_Model_Db_Rowset';
    protected $_table;
    private $_tableName;
    private $_columns;

    protected $_supportedImportExportFormats = array(self::FORMAT_SQL, self::FORMAT_CSV, self::FORMAT_ARRAY);

    private $_importBuffer;
    private $_importBufferOptions;

    public function __construct($config = array())
    {
        if (isset($config['tableName'])) {
            $this->_table = new $config['tableName']();
        }
        if (isset($config['table'])) {
            $this->_table = $config['table'];
        }
        parent::__construct($config);
    }

    public function __sleep()
    {
        throw new Vps_Exception_NotYetImplemented();
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
        if (is_string($this->_table)) {
            $this->_tableName = $this->_table;
            $this->_table = new Vps_Db_Table(array(
                'name' => $this->_table
            ));
        }
        if (!$this->_table) {
            if (isset($this->_name)) {
                throw new Vps_Exception("You must rename _name to _table in '".get_class($this)."'");
            }
            throw new Vps_Exception("No table set");
        }
        if (!$this->_table instanceof Zend_Db_Table_Abstract) {
            throw new Vps_Exception("'".get_class($this->_table)."' is not a Zend_Db_Table");
        }
    }

    public function getColumnType($col)
    {
        $info = $this->getTable()->info();
        if (isset($info['metadata'][$col])) {
            return $this->_getTypeFromDbType($info['metadata'][$col]['DATA_TYPE']);
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
        else if ($type == 'datetime') $type = self::TYPE_DATE;
        else if ($type == 'date') $type = self::TYPE_DATE;
        else if ($type == 'decimal') $type = self::TYPE_FLOAT;
        else if (substr($type, 0, 6) == 'double') $type = self::TYPE_FLOAT;
        else if ($type == 'time') $type = null;
        else $type = null;
        return $type;
    }

    protected function _getOwnColumns()
    {
        if (!$this->_columns)
            $this->_columns = $this->_table->info(Zend_Db_Table_Abstract::COLS);
        return $this->_columns;
    }

    public function createRow(array $data=array())
    {
        $ret = new $this->_rowClass(array(
            'row' => $this->_table->createRow(),
            'model' => $this
        ));
        $data = array_merge($this->_default, $data);
        foreach ($data as $k=>$i) {
            $ret->$k = $i;
        }
        return $ret;
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
        if (!is_array($keys)) $keys = array($keys);
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
            $proxiedRow->setReadOnly(false);
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

    private function _formatField($field, Zend_Db_Select $select = null)
    {
        if ($field instanceof Zend_Db_Expr) return $field->__toString();

        if (in_array($field, $this->getOwnColumns())) {
            $f = $this->transformColumnName($field);
            return $this->_fieldWithTableName($f);
        }
        $ret = $this->_formatFieldInternal($field, $select);
        if (!$ret) {
            throw new Vps_Exception("Can't find field '$field' in model '".get_class($this)."' (Table '".$this->getTableName()."')");
        }

        return $ret;
    }

    protected function _fieldWithTableName($field)
    {
        return $this->getTableName().'.'.$field;
    }

    private function _formatFieldInternal($field, $dbSelect)
    {
        $siblingOfModels = $this->_proxyContainerModels;
        $siblingOfModels[] = $this;
        foreach ($siblingOfModels as $siblingOf) {
            foreach ($siblingOf->getSiblingModels() as $k=>$m) {
                while ($m instanceof Vps_Model_Proxy) {
                    $m = $m->getProxyModel();
                }
                if ($m instanceof Vps_Model_Db) {
                    if (in_array($field, $m->getOwnColumns())) {
                        $ref = $m->getReferenceByModelClass(get_class($siblingOf), $k);
                        $siblingTableName = $m->getTableName();

                        $joinCondition = $this->getTableName().'.'.$this->getPrimaryKey()
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
                    $ret = $m->_formatFieldInternal($field, $dbSelect);
                    if ($ret) return $ret;
                }
            }
        }

        return $this->_formatFieldExpr($field, $dbSelect);
    }

    private function _formatFieldExpr($field, $dbSelect)
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

        return $this->_createDbSelectExpression($expr, $dbSelect, $depOf);
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

    public function createDbSelect($select)
    {
        if (!$select) return null;
        $tablename = $this->getTableName();
        $dbSelect = $this->_table->select();
        $dbSelect->from($tablename);
        $this->_applySelect($dbSelect, $select);
        return $dbSelect;
    }

    protected function _applySelect(Zend_Db_Select $dbSelect, Vps_Model_Select $select)
    {
        if ($dbSelect instanceof Zend_Db_Table_Select) {
            $dbSelect->setIntegrityCheck(false);
        }

        if ($whereEquals = $select->getPart(Vps_Model_Select::WHERE_EQUALS)) {
            foreach ($whereEquals as $field=>$value) {
                if (is_array($value)) {
                    if ($value) {
                        foreach ($value as &$v) {
                            $v = $this->_fixStupidQuoteBug($v);
                            $v = $this->getAdapter()->quote($v);
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
        if ($whereNotEquals = $select->getPart(Vps_Model_Select::WHERE_NOT_EQUALS)) {
            foreach ($whereNotEquals as $field=>$value) {
                if (is_array($value)) {
                    foreach ($value as &$v) {
                        $v = $this->_fixStupidQuoteBug($v);
                        $v = $this->getAdapter()->quote($v);
                    }
                    $value = implode(', ', $value);
                    $dbSelect->where($this->_formatField($field, $dbSelect)." NOT IN ($value)");
                } else {
                    $value = $this->_fixStupidQuoteBug($value);
                    $dbSelect->where($this->_formatField($field, $dbSelect)." != ?", $value);
                }
            }
        }
        if ($where = $select->getPart(Vps_Model_Select::WHERE)) {
            foreach ($where as $w) {
                $dbSelect->where($w[0], $w[1], $w[2]);
            }
        }

        if ($whereId = $select->getPart(Vps_Model_Select::WHERE_ID)) {
            $whereId = $this->_fixStupidQuoteBug($whereId);
            $dbSelect->where($this->_formatField($this->getPrimaryKey(), $dbSelect)." = ?", $whereId);
        }

        if ($whereNull = $select->getPart(Vps_Model_Select::WHERE_NULL)) {
            foreach ($whereNull as $field) {
                $dbSelect->where("ISNULL(".$this->_formatField($field, $dbSelect).")");
            }
        }

        if ($other = $select->getPart(Vps_Model_Select::OTHER)) {
            foreach ($other as $i) {
                call_user_func_array(array($dbSelect, $i['method']), $i['arguments']);
            }
        }
        if ($whereExpression = $select->getPart(Vps_Model_Select::WHERE_EXPRESSION)) {
            foreach ($whereExpression as $expr) {
                $expr->validate();
                $dbSelect->where($this->_createDbSelectExpression($expr, $dbSelect));
            }
        }

        if ($exprs = $select->getPart(Vps_Model_Select::EXPR)) {
            foreach ($exprs as $field) {
                if ($col = $this->_formatFieldExpr($field, $dbSelect)) {
                    $dbSelect->from(null, array($field=>new Zend_Db_Expr($col)));
                }
            }
        }
    }

    private static function _getInnerDbModel2($model)
    {
        if ($model instanceof Vps_Model_Db) return $model;
        if ($model instanceof Vps_Model_Proxy) {
            $ret = self::_getInnerDbModel2($model->getProxyModel());
            if ($ret) return $ret;
        }
        if ($model instanceof Vps_Model_MirrorCacheSimple || $model instanceof Vps_Model_RowsSubModel_MirrorCacheSimple) {
            $ret = self::_getInnerDbModel2($model->getSourceModel());
            if ($ret) return $ret;
        }
        return null;
    }

    private static function _getInnerDbModel($model)
    {
        if (is_string($model)) $model = Vps_Model_Abstract::getInstance($model);
        $ret = self::_getInnerDbModel2($model);
        if (!$ret) {
            throw new Vps_Exception_NotYetImplemented();
        }
        return $ret;
    }

    private function _createDbSelectExpression($expr, $dbSelect, $depOf = null)
    {
        // wenn die expr von anderen models kommt (bei ProxyModel), dann
        // brauchen wir das model, dass die expr gesetzt hat (siehe Child oder Parent)
        if (is_null($depOf)) {
            $depOf = $this;
        }
        if ($expr instanceof Vps_Model_Select_Expr_CompareField_Abstract) {
            $quotedValue = $expr->getValue();
            if (is_array($quotedValue)) {
                foreach ($quotedValue as &$v) {
                    $v = $this->_fixStupidQuoteBug($v);
                    $v = $this->_table->getAdapter()->quote($v);
                }
            } else {
                if ($quotedValue instanceof Vps_DateTime) {
                    $quotedValue = $quotedValue->format('Y-m-d H:i:s');
                } else if ($quotedValue instanceof Vps_Date) {
                    $quotedValue = $quotedValue->format('Y-m-d');
                }
                $quotedValue = $this->_fixStupidQuoteBug($quotedValue);
                $quotedValue = $this->_table->getAdapter()->quote($quotedValue);
            }
        }
        if ($expr instanceof Vps_Model_Select_Expr_CompareField_Abstract ||
            $expr instanceof Vps_Model_Select_Expr_IsNull
        ) {
            $field = $this->_formatField($expr->getField(), $dbSelect);
        }
        if ($expr instanceof Vps_Model_Select_Expr_Equal) {
            if (is_array($quotedValue)) {
                return $field." IN (".implode(',', $quotedValue).")";
            } else {
                return $field." = ".$quotedValue;
            }
        } else if ($expr instanceof Vps_Model_Select_Expr_NotEquals) {
            if (is_array($quotedValue)) {
                return $field." NOT IN (".implode(',', $quotedValue).")";
            } else {
                return $field." != ".$quotedValue;
            }
        } else if ($expr instanceof Vps_Model_Select_Expr_IsNull) {
            return $field." IS NULL";
        } else if ($expr instanceof Vps_Model_Select_Expr_Lower) {
            return $field." < ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_Higher) {
            return $field." > ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_LowerEqual) {
            return $field." <= ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_HigherEqual) {
            return $field." >= ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_Like) {
            $quotedValue = str_replace("_", "\\_", $quotedValue);
            if ($expr instanceof Vps_Model_Select_Expr_Contains) {
                $v = $expr->getValue();
                $v = $this->_fixStupidQuoteBug($v);
                $quotedValueContains = $this->_table->getAdapter()->quote('%'.$v.'%');

                $quotedValue = str_replace("%", "\\%", $quotedValue);
                $quotedValue = str_replace(
                                substr($quotedValueContains, 2, strlen($quotedValueContains)-4),
                                substr($quotedValue, 1, strlen($quotedValue)-2),
                                $quotedValueContains);
            }
            return $field." LIKE ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_StartsWith) {
            return "LEFT($field, ".strlen($this->_fixStupidQuoteBug($expr->getValue())).") = ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_NOT) {
            return "NOT (".$this->_createDbSelectExpression($expr->getExpression(), $dbSelect).")";
        } else if ($expr instanceof Vps_Model_Select_Expr_Or) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression, $dbSelect).")";
            }
            return implode(" OR ", $sqlExpressions);
        } else if ($expr instanceof Vps_Model_Select_Expr_And) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression, $dbSelect).")";
            }
            return implode(" AND ", $sqlExpressions);
        } else if ($expr instanceof Vps_Model_Select_Expr_Add) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression, $dbSelect).")";
            }
            return implode(" + ", $sqlExpressions);
        } else if ($expr instanceof Vps_Model_Select_Expr_Subtract) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression, $dbSelect).")";
            }
            return implode(" - ", $sqlExpressions);
        } else if ($expr instanceof Vps_Model_Select_Expr_Concat) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                            if ($expression instanceof Vps_Model_Select_Expr_Interface) {
                    $sqlExpressions[] = $this->_createDbSelectExpression($expression, $dbSelect);
                } else {
                    $sqlExpressions[] = $this->_formatField($expression, $dbSelect);
                }
            }
            return 'CONCAT('.implode(", ", $sqlExpressions).')';
        } else if ($expr instanceof Vps_Model_Select_Expr_StrPad) {
            $field = $expr->getField();
            if ($field instanceof Vps_Model_Select_Expr_Interface) {
                $field = $this->_createDbSelectExpression($field, $dbSelect);
            } else {
                $field = $this->_formatField($field, $dbSelect);
            }
            if ($expr->getPadType() == Vps_Model_Select_Expr_StrPad::RIGHT) {
                $pad = 'RPAD';
            } else if ($expr->getPadType() == Vps_Model_Select_Expr_StrPad::LEFT) {
                $pad = 'LPAD';
            } else {
                throw new Vps_Exception_NotYetImplemented();
            }
            return $pad."($field, {$expr->getPadLength()}, {$expr->getPadStr()})";
        } else if ($expr instanceof Vps_Model_Select_Expr_String) {
            $quotedString = $this->_fixStupidQuoteBug($expr->getString());
            $quotedString = $this->_table->getAdapter()->quote($quotedString);
            return $quotedString;
        } else if ($expr instanceof Vps_Model_Select_Expr_Count) {
            $field = $expr->getField();
            if ($field != '*') {
                $field = $this->_formatField($field, $dbSelect);
            }
            if ($expr->getDistinct()) $field = "DISTINCT $field";
            return "COUNT($field)";
        } else if ($expr instanceof Vps_Model_Select_Expr_Sum) {
            $field = $this->_formatField($expr->getField(), $dbSelect);
            return "SUM($field)";
        } else if ($expr instanceof Vps_Model_Select_Expr_Max) {
            $field = $this->_formatField($expr->getField(), $dbSelect);
            return "MAX($field)";
        } else if ($expr instanceof Vps_Model_Select_Expr_Min) {
            $field = $this->_formatField($expr->getField(), $dbSelect);
            return "MIN($field)";
        } else if ($expr instanceof Vps_Model_Select_Expr_Area) {
            $lat1 = $this->_formatField('latitude', $dbSelect);
            $lat2 = $expr->getLatitude();
            $long1 = $this->_formatField('longitude', $dbSelect);
            $long2 = $expr->getLongitude();
            $radius = $expr->getRadius();
            return "
                (ACOS(
                    SIN($lat1) * SIN($lat2) +
                    COS($lat1) * COS($lat2) *
                    COS($long2 - $long1)
                ) / 180 * PI() * 6378.137) <= $radius
            ";
        } else if ($expr instanceof Vps_Model_Select_Expr_Child) {
            $depM = $depOf->getDependentModel($expr->getChild());
            $dbDepM = self::_getInnerDbModel($depM);
            $dbDepOf = self::_getInnerDbModel($depOf);

            $depTableName = $dbDepM->getTableName();
            $ref = $depM->getReferenceByModelClass(get_class($depOf), null/*todo*/);
            $depSelect = $expr->getSelect();
            if (!$depSelect) {
                $depSelect = $dbDepM->select();
            } else {
                //wir führen unten ein where aus, das darf nicht im original select bleiben
                $depSelect = clone $depSelect;
            }
            $col1 = $dbDepM->transformColumnName($ref['column']);
            $col2 = $dbDepOf->transformColumnName($dbDepOf->getPrimaryKey());
            $depSelect->where("$depTableName.$col1={$dbDepOf->getTableName()}.$col2");
            $depDbSelect = $dbDepM->_getDbSelect($depSelect);
            $exprStr = $dbDepM->_createDbSelectExpression($expr->getExpr(), $depDbSelect);
            $depDbSelect->reset(Zend_Db_Select::COLUMNS);
            $depDbSelect->from(null, $exprStr);
            return "($depDbSelect)";
        } else if ($expr instanceof Vps_Model_Select_Expr_Child_Contains) {
            $i = $depOf->getDependentModelWithDependentOf($expr->getChild());
            $depM = $i['model'];
            $depOf = $i['dependentOf'];
            $depM = Vps_Model_Abstract::getInstance($depM);
            $dbDepM = $depM;
            while ($dbDepM instanceof Vps_Model_Proxy) {
                $dbDepM = $dbDepM->getProxyModel();
            }
            if (!$dbDepM instanceof Vps_Model_Db) {
                throw new Vps_Exception_NotYetImplemented();
            }
            $dbDepOf = $depOf;
            while ($dbDepOf instanceof Vps_Model_Proxy) {
                $dbDepOf = $dbDepOf->getProxyModel();
            }
            if (!$dbDepOf instanceof Vps_Model_Db) {
                throw new Vps_Exception_NotYetImplemented();
            }
            $ref = $depM->getReferenceByModelClass(get_class($depOf), $expr->getChild());
            $depSelect = $expr->getSelect();
            if (!$depSelect) $depSelect = $dbDepM->select();
            $col1 = $dbDepM->_formatField($ref['column'], null /* select fehlt - welches ist das korrekte?*/);
            $col2 = $dbDepOf->transformColumnName($dbDepOf->getPrimaryKey());
            $depDbSelect = $dbDepM->_getDbSelect($depSelect);
            $depDbSelect->reset(Zend_Db_Select::COLUMNS);
            $depDbSelect->from(null, $col1);
            return $this->_fieldWithTableName($this->getPrimaryKey())." IN ($depDbSelect)";
        } else if ($expr instanceof Vps_Model_Select_Expr_Parent) {
            $dbRefM = self::_getInnerDbModel($depOf->getReferencedModel($expr->getParent()));
            $dbDepOf = self::_getInnerDbModel($depOf);
            $refTableName = $dbRefM->getTableName();
            $ref = $depOf->getReference($expr->getParent());
            $refSelect = $dbRefM->select();
            if ($ref === Vps_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
                $ref = $dbDepOf->getReferenceByModelClass($depOf->getParentModel(), null);
            }
            $col1 = $dbDepOf->_formatField($ref['column'], null /* select fehlt - welches sollte das sein? */ );
            $col2 = $dbRefM->transformColumnName($dbRefM->getPrimaryKey());

            $refSelect->where("$refTableName.$col2=$col1");
            $refDbSelect = $dbRefM->createDbSelect($refSelect);
            $exprStr = $dbRefM->_formatField($expr->getField(), $refDbSelect);
            $refDbSelect->reset(Zend_Db_Select::COLUMNS);
            $refDbSelect->from(null, $exprStr);
            return "($refDbSelect)";
        } else if ($expr instanceof Vps_Model_Select_Expr_Field) {
            $field = $this->_formatField($expr->getField(), $dbSelect);
            return $field;
        } else if ($expr instanceof Vps_Model_Select_Expr_PrimaryKey) {
            $field = $this->_formatField($this->getPrimaryKey(), $dbSelect);
            return $field;
        } else if ($expr instanceof Vps_Model_Select_Expr_SumFields) {
            $sqlExpressions = array();
            foreach ($expr->getFields() as $expression) {
                if (is_int($expression)) {
                    $sqlExpressions[] = $expression;
                } else if (is_string($expression)) {
                    $sqlExpressions[] = $this->_formatField($expression, $dbSelect);
                } else if ($expression instanceof Vps_Model_Select_Expr_Interface) {
                    $sqlExpressions[] = $this->_createDbSelectExpression($expression, $dbSelect);
                } else {
                    throw new Vps_Exception_NotYetImplemented();
                }
            }
            return '('.implode('+ ', $sqlExpressions).')';
        } else if ($expr instanceof Vps_Model_Select_Expr_Sql) {
            return '('.$expr->getSql().')';
        } else {
            throw new Vps_Exception_NotYetImplemented("Expression not yet implemented: ".get_class($expr));
        }
    }

    //Nur zum Debuggen verwenden!
    public function getSqlForSelect($select)
    {
        $dbSelect = $this->_getDbSelect($select);
        return $dbSelect->__toString();
    }

    public function find($id)
    {
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->find($id),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function getIds($where = array(), $order=null, $limit=null, $start=null)
    {
        $dbSelect = $this->_getDbSelect($where, $order, $limit, $start);
        $id = $this->getPrimaryKey();
        $ret = array();
        foreach ($this->_table->fetchAll($dbSelect) as $row) {
            $ret[] = $row->$id;
        }
        return $ret;
    }

    public function getRows($where = array(), $order=null, $limit=null, $start=null)
    {
        $dbSelect = $this->_getDbSelect($where, $order, $limit, $start);
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->fetchAll($dbSelect),
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
        if ($select->getPart(Vps_Model_Select::OTHER) ||
            $select->getPart(Vps_Model_Select::LIMIT_COUNT) ||
            $select->getPart(Vps_Model_Select::LIMIT_OFFSET))
            throw new Vps_Exception('Select for update must only contain where* parts');
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
        return $this->_table->delete($this->_getTableUpdateWhere($where));
    }

    public function updateRows($data, $where)
    {
        return $this->_table->update($data, $this->_getTableUpdateWhere($where));
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
        if ($order = $select->getPart(Vps_Model_Select::ORDER)) {
            foreach ($order as $o) {
                if ($o['field'] instanceof Zend_Db_Expr) {
                    $dbSelect->order($o['field']);
                } else if ($o['field'] == Vps_Model_Select::ORDER_RAND) {
                    $dbSelect->order('RAND()');
                } else {
                    if (strpos($o['field'], '.') === false &&
                        strpos($o['field'], '(') === false
                    ) {
                        $o['field'] = $this->_formatField($o['field'], $dbSelect);
                    }
                    $dbSelect->order($o['field'].' '.$o['direction']);
                }
            }
        }
        $limitCount = $select->getPart(Vps_Model_Select::LIMIT_COUNT);
        $limitOffset = $select->getPart(Vps_Model_Select::LIMIT_OFFSET);
        if ($limitCount || $limitOffset) {
            $dbSelect->limit($limitCount, $limitOffset);
        }
        return $dbSelect;
    }

    public function countRows($select = array())
    {

        if (!is_object($select)) {
            $select = $this->select($select);
        }
        $dbSelect = $this->createDbSelect($select);
        $dbSelect->reset(Zend_Db_Select::COLUMNS);
        $dbSelect->setIntegrityCheck(false);
        if ($dbSelect->getPart('group')) {
            $group = current($dbSelect->getPart('group'));
            $dbSelect->reset(Zend_Db_Select::GROUP);
            $dbSelect->from(null, "COUNT(DISTINCT $group) c");
        } else {
            $dbSelect->from(null, 'COUNT(*) c');
        }
        return $this->_table->getAdapter()->query($dbSelect)->fetchColumn();
    }

    public function evaluateExpr(Vps_Model_Select_Expr_Interface $expr, Vps_Model_Select $select = null)
    {
        if (is_null($select)) $select = $this->select();
        $dbSelect = $this->createDbSelect($select);
        $dbSelect->reset(Zend_Db_Select::COLUMNS);
        $dbSelect->setIntegrityCheck(false);
        $dbSelect->from(null, $this->_createDbSelectExpression($expr, $dbSelect));
        return $this->_table->getAdapter()->query($dbSelect)->fetchColumn();
    }

    public function getPrimaryKey()
    {
        $ret = $this->_table->info('primary');
        if (sizeof($ret) == 1) {
            $ret = array_values($ret);
            $ret = $ret[0];
        }
        return $ret;
    }

    public function getTable()
    {
        return $this->_table;
    }

    public function getAdapter()
    {
        return $this->getTable()->getAdapter();
    }

    public function getTableName()
    {
        if (!$this->_tableName)
            $this->_tableName = $this->_table->info(Zend_Db_Table_Abstract::NAME);
        return $this->_tableName;
    }

    public function isEqual(Vps_Model_Interface $other) {
        if ($other instanceof Vps_Model_Db &&
            $this->getTableName() == $other->getTableName()) {
            return true;
        }
        return false;
    }

    public function select($where = array(), $order = null, $limit = null, $start = null)
    {
        if (!is_array($where)) {
            $ret = new Vps_Model_Select();
            if ($where) {
                $ret->whereEquals($this->getPrimaryKey(), $where);
            }
        } else {
            $ret = new Vps_Model_Select($where);
        }
        if ($order) $ret->order($order);
        if ($limit || $start) $ret->limit($limit, $start);
        return $ret;
    }

    public function getUniqueIdentifier()
    {
        return $this->getTableName();
    }


    public function export($format, $select = array())
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
            if ($ret != 0) throw new Vps_Exception("SQL export failed");
            $ret = file_get_contents($filename);
            unlink($filename);
            return $ret;
        } else if ($format == self::FORMAT_CSV) {
            if (!is_object($select)) {
                if (is_string($select)) $select = array($select);
                $select = $this->select($select);
            }

            $tmpExportFolder = realpath('application/temp').'/modelcsv'.uniqid();
            mkdir($tmpExportFolder, 0777);
            $filename = $tmpExportFolder.'/csvexport';

            $dbSelect = $this->_getDbSelect($select);
            $sqlString = $dbSelect->assembleIntoOutfile($filename);

            $dbSelect->limit(1);
            $fieldResult = $dbSelect->query()->fetchAll();
            $columnsCsv = '';
            if (count($fieldResult)) {
                $columns = array_keys($fieldResult[0]);
                $columnsCsv = '"'.implode('","', $columns).'"';
                $this->executeSql($sqlString);
                $cmd = "{ echo '$columnsCsv'; cat $filename; } | gzip -c > $filename.gz";
                exec($cmd, $output, $ret);
                if ($ret != 0) throw new Vps_Exception("CSV-SQL export failed");

                if (!file_exists($filename.'.gz')) {
                    throw new Vps_Exception("Error exporting csv from model - target file has not been created");
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
            $dbSelect = $this->_getDbSelect($select);
            if (!$dbSelect) return array();
            return $dbSelect->query()->fetchAll();
        } else {
            return parent::export($format, $select);
        }
    }

    private function _updateModelObserver()
    {
        if (Vps_Component_Data_Root::getComponentClass()) {
            if ($this->_proxyContainerModels) {
                foreach ($this->_proxyContainerModels as $m) {
                    Vps_Component_ModelObserver::getInstance()->update($m);
                }
            } else {
                Vps_Component_ModelObserver::getInstance()->update($this);
            }
        }
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
            $this->_updateModelObserver();
            if ($ret != 0) throw new Vps_Exception("SQL import failed: ".implode("\n", $output));
        } else if ($format == self::FORMAT_CSV) {
            // if no data is recieved, quit
            if (!$data) return;

            $tmpImportFolder = realpath('application/temp').'/modelcsv'.uniqid();
            mkdir($tmpImportFolder, 0777);
            $filename = $tmpImportFolder.'/csvimport';
            file_put_contents($filename.'.gz', $data);

            $cmd = "gunzip -c $filename.gz > $filename"
                ." && head --lines=1 $filename | sed -e 's|\"|`|g'";
            exec($cmd, $output, $ret);
            if ($ret != 0) throw new Vps_Exception("CSV-SQL export failed");

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
            $this->_updateModelObserver();
        } else if ($format == self::FORMAT_ARRAY) {
            if (isset($options['buffer']) && $options['buffer']) {
                if (isset($this->_importBuffer)) {
                    if ($options != $this->_importBufferOptions) {
                        throw new Vps_Exception_NotYetImplemented("You can't buffer imports with different options (not yet implemented)");
                    }
                    $this->_importBuffer = array_merge($this->_importBuffer, $data);
                    // handling for not sending too much data to mysql in one query
                    // is in _importArray() function
                } else {
                    $this->_importBufferOptions = $options;
                    $this->_importBuffer = $data;
                }
            } else {
                $this->_importArray($data, $options);
            }
            $this->_updateModelObserver();
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
        if ($config->server->host == 'vivid-planet.com') {
            $ret['mysqlDir'] = '/usr/local/mysql/bin/';
        }
        $ret['tableName'] = $this->getTableName();

        return $ret;
    }

    public function writeBuffer()
    {
        parent::writeBuffer();
        if (isset($this->_importBuffer)) {
            $this->_importArray($this->_importBuffer, $this->_importBufferOptions);
            unset($this->_importBuffer);
            unset($this->_importBufferOptions);
        }
    }

    private function _importArray($data, $options)
    {
        if (empty($data)) return;
        if ($this->getSiblingModels()) {
            if ($options) {
                throw new Vps_Exception_NotYetImplemented("import options together with siblingModels are not yet implemented");
            }
            foreach ($data as $r) {
                $this->createRow($r)->save();
            }
            return;
        }
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
        $sqlTableAndColumns .= ' INTO '.$this->getTableName().' ('.implode(', ', $fields).') VALUES ';
        $sqlValues = '';
        foreach ($data as $d) {
            if (array_keys($d) != array_keys($data[0])) {
                throw new Vps_Exception_NotYetImplemented("You must have always the same keys when importing");
            }
            $sqlValues .= '(';
            foreach ($d as $i) {
                if (is_null($i)) {
                    $sqlValues .= 'NULL';
                } else {
                    $sqlValues .= $this->_table->getAdapter()->quote($i);
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
        if ($this->_table->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mysql) {
            $q = $this->_table->getAdapter()->getProfiler()->queryStart($sql, Zend_Db_Profiler::INSERT);
            $this->_table->getAdapter()->getConnection()->exec($sql);
            $this->_table->getAdapter()->getProfiler()->queryEnd($q);
        } else {
            $this->_table->getAdapter()->query($sql);
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
                    if (!Vps_Util_Mysql::getFileRight()) {
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
}
