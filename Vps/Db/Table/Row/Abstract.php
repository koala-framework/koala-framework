<?p
abstract class Vps_Db_Table_Row_Abstract extends Zend_Db_Table_Row_Abstra

    /
     * Gibt einen von Sonderzeichen befreiten und eindeutigen String zurüc
    
     * Ersetzt alle Zeichen außer a-z0-9_ möglichst sinngemäß, auch im kyrillisch
     * Zeichensatz (falls die transliterate-Erweiterung installiert ist). Option
     * kann der String auf Eindeutigkeit in einer Tabelle geändert werden. Falls d
     * gleiche String schon existiert, wird _1, _2 ... angehäng
    
     * @param string String, der Unique sein soll
     * @param string Spaltenname, dessen Werte unique sein sollt
     * @param string Where-Klausel für Unique-Abfrage (zB. 'parent_id=1
     * @return string Unique Stri
     
    public function getUniqueString($string, $fieldname = '', array $where = array(
   
        // Sonderzeichen rausnehm
        $string = Zend_Filter::get($string, 'Url', array(), 'Vps_Filter')
      
        // Unique mach
        if ($fieldname != '')
            $primaryKey = key($this->_getPrimaryKey()
            $primaryValue = current($this->_getPrimaryKey()
            $where["$primaryKey != ?"] = $primaryValue
           
            $x = 0
            $unique = $string
            $where["$fieldname = ?"] = $unique
            while ($this->getTable()->fetchAll($where)->count() > 0) 
                $unique = $string . '_' . ++$
                $where["$fieldname = ?"] = $unique
           
            $string = $uniqu
       

        return $strin
   

    /
     * Speichert die Nummerierung für einen Datensatz und passt die restlich
     * Datensätze a
    
     * @param string Spaltenname, in der die Nummerierung ste
     * @param int Nummer des zu nummerierenden Datensatz
     * @param string Where-Klausel für Einschränkung der betreffenden Datensätze (zB. 'parent_id=1
     * @return boolean Ob Nummerierung erfolgreich w
     
    public function numberize($fieldname, $value = null, array $where = array(
   
        $originalWhere = $where
        $primaryKey = key($this->_getPrimaryKey()
        $primaryValue = current($this->_getPrimaryKey()
        $where["$primaryKey != ?"] = $primaryValue
      
        // Wenn value null ist, Datensatz am Ende einfüg
        if (is_null($value))
            $value = $this->getTable()->fetchAll($where)->count() + 1
       

        $x = 0
        foreach ($this->getTable()->fetchAll($where, $fieldname) as $row) 
            $x++
            if ($x == $value) { $x++; 
            $row->$fieldname = $x
            $row->save()
        

        $this->$fieldname = $value
        $this->save(

        $this->getTable()->numberizeAll($fieldname, $originalWhere
   

