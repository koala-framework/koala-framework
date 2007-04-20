<?php
/**
*  Registry Werte aus der Datenbank einlesen, durchsuchen und setzen
*  
* @author Dipl.-Ing. (FH) Martin Mayrhofer
* @copyright 2003-2007, DAX Softwareentwicklung, Vivid Planet Software GmbH
* @since 03.04.2007 
* @since 11.01.2007 Singleton / PHP5 
* @since 02.12.2003
*/

/**
* Bietet Methoden zum Abfragen und Manipulieren der Registry Werte an
**/
class Vps_Registry
{
    private static $_instances = array();
       
	/**
	* Array in which the Config Values are read
	* @access private
	**/
	private $_registryVals;
	/**
	* Represents the Database Connection
	* @access private
	* */
	private $_database;
	/**
	* The Tablename for the Config Values
	* @access private
	* */ 
	private $_table;
	
	private $_nestSeparator = '.';
	/**
	* Kontruktor: Config
	* 
	* @param DB $db A _database Connection
	* @param string $_table The Table Name for the Config
	**/
	protected function __construct($db, $table)
	{
		$this->_setDb($db);
		$this->_table = $table;
		$this->read();
	}
    
    private function __clone(){}
    
    public static function getInstance($db, $table = 'registry')
    {
        for($i = 0; $i < count(self::$_instances); $i++) {
        	if(self::$_instances[$i]['db'] === $db) {
        		return self::$_instances[$i]['registry'];
        	}
        }
        
        $instance = array('db' => $db,
        				  'registry' => new VPS_Registry($db, $table));
		self::$_instances[] = $instance;        
		return $instance['registry'];
    }
	
	/**
	 *	Reads the Config Table and writes the values in an array
	 * 
	 * @return void 
	 **/
	public function read()
	{	
		$sql = "SELECT * FROM $this->_table";
		$this->_registryVals = $this->_database->fetchAll($sql);
	}
	
	/**
	 * Sets a key/value pair only in the array
	 * 
	 * @return void
	 * @param string $key
	 * @param string $value
	 **/
	public function set($key, $value)
	{
		$newindex = count($this->_registryVals);
		
		//Überprüfen ob der Schlüssel schon vorhanden ist
		for ($i = 0; $i <= $newindex; $i++) {
			if(isset($this->_registryVals[$i]['name']) && $this->_registryVals[$i]['name'] == $key) {
				$this->_registryVals[$i]['value']= $value;
				return;
			}
		}
		//Neuen Schlüssel/Wert setzen		
		$this->_registryVals[$newindex]['name']=$key;
		$this->_registryVals[$newindex]['value']=$value;
	}
	
	
	/**
	 * Sets the database handle
	 * 
	 * @return void 
	 * @param DB $db The Database Connection
	 **/
	private function _setDb($db)
	{
		$this->_database=$db;	
	}
	
	/**
	* Returns a value for a requested key
	* 
	* When the requested key is not available the function trys to find a value for the
	* key which is a level higher.
	* A key consists of levels (all levels are optional) and a property with the following syntax:
	* leveln.level2.level1.property
	* 
	* @return $value
	* @param string $key
	**/
	public function get($key, $default = '')
	{
		//echo '| '. $key;
		$root ='';

		if (!$this->exists($key)) {
			//**Falls gesuchter Wert nicht vorhanden ist den Root Wert holen
			//Falls gesuchter Wert nicht vorhanden ist den nächst höheren Wert holen
			$tokens = explode($this->_nestSeparator,$key);
			$amount = count($tokens);
				
			if ($amount > 2) {	
				$key=$tokens[0];		
				for($i = 1; $i <= $amount - 1; $i++) {
					if ($i != $amount - 2) {
						$key .= $this->_nestSeparator. $tokens[$i];
					}
				}
				$root = $this->get($key);
			}
			else if ($amount == 2) {
			 	$key = $tokens[1];
   			    $root = $this->get($key);
			}
			return $root;
		} else {
			for ($i = 0; $i <= count($this->_registryVals); $i++) {
				if ($this->_registryVals[$i]['name']==$key) {
					return $this->_registryVals[$i]['value'];					    
				}
			}
		}
        
        if($default) {       
        	return $default;
        }
		return 0;
	}
	
	/**
	 * Tests if a key/value pair exists
	 * 
	 * @return true/false
	 * @param string $key
	 **/
	public function exists($key)
	{
		for ($i = 0; $i <= count($this->_registryVals); $i++) {
			//Array nach dem Schl�ssel durchsuchen
			if (isset($this->_registryVals[$i]['name']) && $this->_registryVals[$i]['name'] == $key) {
				if ($this->_registryVals[$i]['value']) {
					//echo $key.'<br>'.$this->_registryVals[$i]['value'];
				    return true;
				}				
			}
		}
		return false;
	}
	
	
	/**
	* Writes a new key/value pair into the database
	* If the key already exists only the value will be updated
	* 
	* @return void
	* @param string $key
	* @param string $value
	**/
	public function write($key, $value)
	{
		$this->set($key,$value);
		$sql ="SELECT name FROM $this->_table WHERE name ='$key'";
		
		$rs =$this->_database->fetchAll($sql);
		
		//Falls Schlüssel in der Datenbank noch nicht vorhanden ist neu anlegen
		if (!$rs) {	
			$temp['name'] = $key;
			$temp['value'] = $value;
			$this->_database->insert($this->_table,$temp);		    
		} else { //Falls der Schlüssel in der Datenbank vorhanden ist nur den Wert updaten
			$data['value']=$value;
			$this->_database->update($this->_table,$data,$this->_database->quoteInto('name = ?', $key));
		}
	}
	
	/**
	 * Write the whole array into the database
	 * 
	 * @return void 
	 **/
	public function flush()
	{
		for($i = 0;$i < count($this->_registryVals); $i++) {
			$this->write($this->_registryVals[$i]['name'],$this->_registryVals[$i]['value']);
		}
	}
	
	/**
	 * Deletes a key/value pair from the database
	 * 
	 * @return void
	 * @param string $key 
	 **/
	public function delete($key)
	{
		$sql = "DELETE FROM {$this->_table} WHERE name = '$key'";
		//echo $sql;
		$this->_database->query($sql);
		$this->read();
	}
	
	/**
	* Replace a variable in a property value with a value
	* Variable must have this style: {variable}
	* 
	* @return replaced value
	* @param string $name
	* @param string $value
	* @param string $key
	**/
	public function replaceValue($name, $value, $key)
	{
		return preg_replace("/\{$name\}/i",$value, $this->get($key));
	}
	
	/**
	 * Funktion: check
	 * 
	 * Erwartet den zu überprüfenden Schlüssel sowie einen optionalen Standardwert falls er nicht existiert
	 * @return proof result
	 * @param string $key
	 * @param string $default
	 **/
	public function check($key,$default='')
	{
		if ($this->exists($key)) {
			// 	Schlüssel/Wert existiert => Returnwert 1
			return 1;  
		} else if (($this->get($key)) && (!$default)) {
			// Nächst höherer Schlüssel existiert und Defaultwert ist NICHT angegeben 
			// => Returnwert 2
			return 2;
		} else if (!($this->exists($key)) && $default) {
			// Schlüssel existiert NICHT und Defaultwert ist angegeben => Schlüssel/Wert Paar
			// wird geschrieben, Returnwert 3
			$this->write($key,$default);
			return 3;
		} else {
			// Kein Schlüssel existiert und kein Defaultwert ist angegeben => Returnwert 0
			// (Error)
			return 0;
		}    
	}
	
	public function getSection($name)
	{
		$ret = array();
		for($i = 0; $i < count($this->_registryVals); $i++)
		{
			if (preg_match("/^($name)\..*/", $this->_registryVals[$i]['name'])) {
				$entry = array();
				$entry = $this->_processKey($entry,$this->_registryVals[$i]['name'],$this->_registryVals[$i]['value']);
				$ret = array_merge_recursive($ret, $entry);
			}
		}
		return $ret;
	}

	
	protected function _processKey($config, $key, $value)
	{
		if (strpos($key, $this->_nestSeparator) !== false) {
			$pieces = explode($this->_nestSeparator, $key, 2);
			if (strlen($pieces[0]) && strlen($pieces[1])) {
				if (!isset($config[$pieces[0]])) {
					$config[$pieces[0]] = array();
				} elseif (!is_array($config[$pieces[0]])) {
					throw new Exception("Cannot create sub-key for '{$pieces[0]}' as key already exists");
				}
				$config[$pieces[0]] = $this->_processKey($config[$pieces[0]], $pieces[1], $value);
			} else {
				throw new Exception("Invalid key '$key'");
			}
		} else {
			$config[$key] = $value;
		}
		return $config;
	}
}