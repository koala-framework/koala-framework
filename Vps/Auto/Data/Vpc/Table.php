<?ph
class Vps_Auto_Data_Vpc_Table extends Vps_Auto_Data_Table_Paren

    public function load($row
    
        $table = new $this->_parentTable()
        $key = array
            'page_id = ?' => $row->page_id
            'component_key = ?' => $row->component_key . '-' . $row->i
        )

        $row = $table->fetchAll($key)->current()
        if ($row) 
            $name = $this->_dataIndex
            return $row->$name
        } else 
            return ''
        
    
