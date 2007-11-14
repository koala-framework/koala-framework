<?php
abstract class Vps_Db_Table_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
    /**
     * Gibt einen von Sonderzeichen befreiten und eindeutigen String zurück.
     *
     * Ersetzt alle Zeichen außer a-z0-9_ möglichst sinngemäß, auch im kyrillischen
     * Zeichensatz (falls die transliterate-Erweiterung installiert ist). Optional
     * kann der String auf Eindeutigkeit in einer Tabelle geändert werden. Falls der
     * gleiche String schon existiert, wird _1, _2 ... angehängt.
     *
     * @param string String, der Unique sein sollte
     * @param string Spaltenname, dessen Werte unique sein sollten
     * @param string Where-Klausel für Unique-Abfrage (zB. 'parent_id=1')
     * @return string Unique String
     */
    public function getUniqueString($string, $fieldname = '', array $where = array())
    {
        // Sonderzeichen rausnehmen
        $string = Zend_Filter::get($string, 'Url', array(), 'Vps_Filter');
        
        // Unique machen
        if ($fieldname != '') {
            $primaryKey = key($this->_getPrimaryKey());
            $primaryValue = current($this->_getPrimaryKey());
            $where["$primaryKey != ?"] = $primaryValue;
            
            $x = 0;
            $unique = $string;
            $where["$fieldname = ?"] = $unique;
            while ($this->getTable()->fetchAll($where)->count() > 0) {
                $unique = $string . '_' . ++$x;
                $where["$fieldname = ?"] = $unique;
            }
            $string = $unique;
        }

        return $string;
    }

    /**
     * Speichert die Nummerierung für einen Datensatz und passt die restlichen
     * Datensätze an.
     *
     * @param string Spaltenname, in der die Nummerierung steht
     * @param int Nummer des zu nummerierenden Datensatzes
     * @param string Where-Klausel für Einschränkung der betreffenden Datensätze (zB. 'parent_id=1')
     * @return boolean Ob Nummerierung erfolgreich war
     */
    public function numberize($fieldname, $value = null, array $where = array())
    {
        $originalWhere = $where;
        $primaryKey = key($this->_getPrimaryKey());
        $primaryValue = current($this->_getPrimaryKey());
        $where["$primaryKey != ?"] = $primaryValue;
        
        // Wenn value null ist, Datensatz am Ende einfügen
        if (!$value) {
            $value = $this->getTable()->fetchAll($where)->count() + 1;
        }

        $x = 0;
        foreach ($this->getTable()->fetchAll($where, $fieldname) as $row) {
            $x++;
            if ($x == $value) { $x++; }
            $row->$fieldname = $x;
            $row->save();
        }

        $this->$fieldname = $value;
        $this->save();

        $this->getTable()->numberizeAll($fieldname, $originalWhere);
    }
}
