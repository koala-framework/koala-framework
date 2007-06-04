<?php
class Vps_Db_Table_Row extends Zend_Db_Table_Row
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
    public function getUniqueString($string, $fieldname = '', $where = '')
    {
        // Sonderzeichen rausnehmen
        if (function_exists('transliterate')) {
            $filter[] = 'cyrillic_transliterate_bulgarian';
            $string = transliterate($string, $filter, 'utf-8', 'utf-8');
        }
        $string = strtolower(htmlentities($string, ENT_COMPAT, 'utf-8'));
        $string = preg_replace('/&szlig;/', 'ss', $string);
        $string = preg_replace('/&(.)(uml);/', '$1e', $string);
        $string = preg_replace('/&(.)(acute|breve|caron|cedil|circ|dblac|die|dot|grave|macr|ogon|ring|tilde|uml);/', '$1', $string);
        $string = preg_replace('/([^a-z0-9]+)/', '_', html_entity_decode($string));
        $string = trim($string, '_');
        
        // Unique machen
        if ($fieldname != '') {
            $table = $this->getTable();
            $info = $table->info();
            $tablename = $info['name'];
            $x = 0;
            if ($where != '') { $where .= ' AND '; }
            $primaryKey = key($this->_getPrimaryKey());
            $primaryValue = current($this->_getPrimaryKey());
            $where .= " $primaryKey!='$primaryValue'";
            $unique = $string;
            while ((int)$table->getAdapter()->fetchOne("SELECT COUNT(*) FROM $tablename WHERE $fieldname='$unique' AND $where") > 0) {
                $unique = $string . '_' . ++$x;
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
    public function numberize($fieldname, $value, $where = '')
    {
        return true;
    }
}
?>
