<?p
/* 7 December 2006. version 1
 
 * This is the php version of the Dean Edwards JavaScript 's Packe
 * Based on
 
 * ParseMaster, version 1.0.2 (2005-08-19) Copyright 2005, Dean Edwar
 * a multi-pattern parse
 * KNOWN BUG: erroneous behavior when using escapeChar with a replaceme
 * value that is a functi
 
 * packer, version 2.0.2 (2005-08-19) Copyright 2004-2005, Dean Edwar
 
 * License: http://creativecommons.org/licenses/LGPL/2.
 
 * Ported to PHP by Nicolas Marti
 
 * --------------------------------------------------------------------
 
 * examples of usage
 * $myPacker = new JavaScriptPacker($script, 62, true, false
 * $packed = $myPacker->pack(
 
 * 
 
 * $myPacker = new JavaScriptPacker($script, 'Normal', true, false
 * $packed = $myPacker->pack(
 
 * or (default value
 
 * $myPacker = new JavaScriptPacker($script
 * $packed = $myPacker->pack(
 
 
 * params of the constructor
 * $script:       the JavaScript to pack, strin
 * $encoding:     level of encoding, int or string
 *                0,10,62,95 or 'None', 'Numeric', 'Normal', 'High ASCII
 *                default: 6
 * $fastDecode:   include the fast decoder in the packed result, boolea
 *                default : tru
 * $specialChars: if you are flagged your private and local variabl
 *                in the script, boolea
 *                default: fals
 
 * The pack() method return the compressed JavasScript, as a strin
 
 * see http://dean.edwards.name/packer/usage/ for more informatio
 
 * Notes
 * # need PHP 5 . Tested with PHP 5.1
 
 * # The packed result may be different than with the Dean Edwar
 *   version, but with the same length. The reason is that the P
 *   function usort to sort array don't necessarily preserve t
 *   original order of two equal member. The Javascript sort functi
 *   in fact preserve this order (but that's not require by t
 *   ECMAScript standard). So the encoded keywords order can 
 *   different in the two result
 
 * # Be careful with the 'High ASCII' Level encoding if you u
 *   UTF-8 in your files..
 


class Vps_Assets_JavaScriptPacker
	// constan
	const IGNORE = '$1

	// validate paramete
	private $_script = '
	private $_encoding = 6
	private $_fastDecode = tru
	private $_specialChars = fals

	private $LITERAL_ENCODING = arra
		'None' => 
		'Numeric' => 1
		'Normal' => 6
		'High ASCII' => 
	

	public function __construct($_script, $_encoding = 62, $_fastDecode = true, $_specialChars = fals

		$this->_script = $_script . "\n
		if (array_key_exists($_encoding, $this->LITERAL_ENCODING
			$_encoding = $this->LITERAL_ENCODING[$_encoding
		$this->_encoding = min((int)$_encoding, 95
		$this->_fastDecode = $_fastDecode
		$this->_specialChars = $_specialChar


	public function pack()
		$this->_addParser('_basicCompression'
		if ($this->_specialChar
			$this->_addParser('_encodeSpecialChars'
		if ($this->_encodin
			$this->_addParser('_encodeKeywords'

		// g
		return $this->_pack($this->_script


	// apply all parsing routin
	private function _pack($script)
		for ($i = 0; isset($this->_parsers[$i]); $i++)
			$script = call_user_func(array(&$this,$this->_parsers[$i]), $script
	
		return $scrip


	// keep a list of parsing functions, they'll be executed all at on
	private $_parsers = array(
	private function _addParser($parser)
		$this->_parsers[] = $parse


	// zero encoding - just removal of white space and commen
	private function _basicCompression($script)
		$parser = new Vps_Assets_ParseMaster(
		// make sa
		$parser->escapeChar = '\\
		// protect strin
		$parser->add('/\'[^\'\\n\\r]*\'/', self::IGNORE
		$parser->add('/"[^"\\n\\r]*"/', self::IGNORE
		// remove commen
		$parser->add('/\\/\\/[^\\n\\r]*[\\n\\r]/', ' '
		$parser->add('/\\/\\*[^*]*\\*+([^\\/][^*]*\\*+)*\\//', ' '
		// protect regular expressio
		$parser->add('/\\s+(\\/[^\\/\\n\\r\\*][^\\/\\n\\r]*\\/g?i?)/', '$2'); // IGNO
		$parser->add('/[^\\w\\x24\\/\'"*)\\?:]\\/[^\\/\\n\\r\\*][^\\/\\n\\r]*\\/g?i?/', self::IGNORE
		// remove: ;;; doSomething(
		if ($this->_specialChars) $parser->add('/;;;[^\\n\\r]+[\\n\\r]/'
		// remove redundant semi-colo
		$parser->add('/\\(;;\\)/', self::IGNORE); // protect for (;;) loo
		$parser->add('/;+\\s*([};])/', '$2'
		// apply the abo
		$script = $parser->exec($script

		// remove white-spa
		$parser->add('/(\\b|\\x24)\\s+(\\b|\\x24)/', '$2 $3'
		$parser->add('/([+\\-])\\s+([+\\-])/', '$2 $3'
		$parser->add('/\\s+/', ''
		// do
		return $parser->exec($script


	private function _encodeSpecialChars($script)
		$parser = new Vps_Assets_ParseMaster(
		// replace: $name -> n, $$name -> 
		$parser->add('/((\\x24+)([a-zA-Z$_]+))(\\d*)/
					 array('fn' => '_replace_name
		
		// replace: _name -> _0, double-underscore (__name) is ignor
		$regexp = '/\\b_[A-Za-z\\d]\\w*/
		// build the word li
		$keywords = $this->_analyze($script, $regexp, '_encodePrivate'
		// quick r
		$encoded = $keywords['encoded'

		$parser->add($regex
			arra
				'fn' => '_replace_encoded
				'data' => $encod
		
		
		return $parser->exec($script


	private function _encodeKeywords($script)
		// escape high-ascii values already in the script (i.e. in string
		if ($this->_encoding > 6
			$script = $this->_escape95($script
		// create the pars
		$parser = new Vps_Assets_ParseMaster(
		$encode = $this->_getEncoder($this->_encoding
		// for high-ascii, don't encode single character low-asc
		$regexp = ($this->_encoding > 62) ? '/\\w\\w+/' : '/\\w+/
		// build the word li
		$keywords = $this->_analyze($script, $regexp, $encode
		$encoded = $keywords['encoded'

		// enco
		$parser->add($regex
			arra
				'fn' => '_replace_encoded
				'data' => $encod
		
		
		if (empty($script)) return $scrip
		else
			//$res = $parser->exec($script
			//$res = $this->_bootStrap($res, $keywords
			//return $re
			return $this->_bootStrap($parser->exec($script), $keywords
	


	private function _analyze($script, $regexp, $encode)
		// analy
		// retreive all words in the scri
		$all = array(
		preg_match_all($regexp, $script, $all
		$_sorted = array(); // list of words sorted by frequen
		$_encoded = array(); // dictionary of word->encodi
		$_protected = array(); // instances of "protected" wor
		$all = $all[0]; // simulate the javascript comportement of global mat
		if (!empty($all))
			$unsorted = array(); // same list, not sort
			$protected = array(); // "protected" words (dictionary of word->"word
			$value = array(); // dictionary of charCode->encoding (eg. 256->f
			$this->_count = array(); // word->cou
			$i = count($all); $j = 0; //$word = nul
			// count the occurrences - used for sorting lat
			do
				--$
				$word = '$' . $all[$i
				if (!isset($this->_count[$word]))
					$this->_count[$word] = 
					$unsorted[$j] = $wor
					// make a dictionary of all of the protected words in this scri
					//  these are words that might be mistaken for encodi
					//if (is_string($encode) && method_exists($this, $encode
					$values[$j] = call_user_func(array(&$this, $encode), $j
					$protected['$' . $values[$j]] = $j+
			
				// increment the word count
				$this->_count[$word]+
			} while ($i > 0
			// prepare to sort the word list, first we must prote
			//  words that are also used as codes. we assign them a co
			//  equivalent to the word itsel
			// e.g. if "do" falls within our encoding ran
			//      then we store keywords["do"] = "do
			// this avoids problems when decodi
			$i = count($unsorted
			do
				$word = $unsorted[--$i
				if (isset($protected[$word]) /*!= null*/)
					$_sorted[$protected[$word]] = substr($word, 1
					$_protected[$protected[$word]] = tru
					$this->_count[$word] = 
			
			} while ($i
	
			// sort the words by frequen
			// Note: the javascript and php version of sort can be different
			// in php manual, usort
			// " If two members compare as equa
			// their order in the sorted array is undefined
			// so the final packed script is different of the Dean's javascript versi
			// but equivalen
			// the ECMAscript standard does not guarantee this behaviou
			// and thus not all browsers (e.g. Mozilla versions dating back to 
			// least 2003) respect this
			usort($unsorted, array(&$this, '_sortWords')
			$j = 
			// because there are "protected" words in the li
			//  we must add the sorted words around th
			do
				if (!isset($_sorted[$i]
					$_sorted[$i] = substr($unsorted[$j++], 1
				$_encoded[$_sorted[$i]] = $values[$i
			} while (++$i < count($unsorted)
	
		return arra
			'sorted'  => $_sorte
			'encoded' => $_encode
			'protected' => $_protected


	private $_count = array(
	private function _sortWords($match1, $match2)
		return $this->_count[$match2] - $this->_count[$match1


	// build the boot function used for loading and decodi
	private function _bootStrap($packed, $keywords)
		$ENCODE = $this->_safeRegExp('$encode\\($count\\)'

		// $packed: the packed scri
		$packed = "'" . $this->_escape($packed) . "'

		// $ascii: base for encodi
		$ascii = min(count($keywords['sorted']), $this->_encoding
		if ($ascii == 0) $ascii = 

		// $count: number of words contained in the scri
		$count = count($keywords['sorted']

		// $keywords: list of words contained in the scri
		foreach ($keywords['protected'] as $i=>$value)
			$keywords['sorted'][$i] = '
	
		// convert from a string to an arr
		ksort($keywords['sorted']
		$keywords = "'" . implode('|',$keywords['sorted']) . "'.split('|')

		$encode = ($this->_encoding > 62) ? '_encode95' : $this->_getEncoder($ascii
		$encode = $this->_getJSFunction($encode
		$encode = preg_replace('/_encoding/','$ascii', $encode
		$encode = preg_replace('/arguments\\.callee/','$encode', $encode
		$inline = '\\$count' . ($ascii > 10 ? '.toString(\\$ascii)' : ''

		// $decode: code snippet to speed up decodi
		if ($this->_fastDecode)
			// create the decod
			$decode = $this->_getJSFunction('_decodeBody'
			if ($this->_encoding > 6
				$decode = preg_replace('/\\\\w/', '[\\xa1-\\xff]', $decode
			// perform the encoding inline for lower ascii valu
			elseif ($ascii < 3
				$decode = preg_replace($ENCODE, $inline, $decode
			// special case: when $count==0 there are no keywords. I want to ke
			//  the basic shape of the unpacking funcion so i'll frig the code.
			if ($count == 
				$decode = preg_replace($this->_safeRegExp('($count)\\s*=\\s*1'), '$1=0', $decode, 1
	

		// boot functi
		$unpack = $this->_getJSFunction('_unpack'
		if ($this->_fastDecode)
			// insert the decod
			$this->buffer = $decod
			$unpack = preg_replace_callback('/\\{/', array(&$this, '_insertFastDecode'), $unpack, 1
	
		$unpack = preg_replace('/"/', "'", $unpack
		if ($this->_encoding > 62) { // high-asc
			// get rid of the word-boundaries for regexp match
			$unpack = preg_replace('/\'\\\\\\\\b\'\s*\\+|\\+\s*\'\\\\\\\\b\'/', '', $unpack
	
		if ($ascii > 36 || $this->_encoding > 62 || $this->_fastDecode)
			// insert the encode functi
			$this->buffer = $encod
			$unpack = preg_replace_callback('/\\{/', array(&$this, '_insertFastEncode'), $unpack, 1
		} else
			// perform the encoding inli
			$unpack = preg_replace($ENCODE, $inline, $unpack
	
		// pack the boot function t
		$unpackPacker = new Vps_Assets_JavaScriptPacker($unpack, 0, false, true
		$unpack = $unpackPacker->pack(

		// argumen
		$params = array($packed, $ascii, $count, $keywords
		if ($this->_fastDecode)
			$params[] = 
			$params[] = '{}
	
		$params = implode(',', $params

		// the whole thi
		return 'eval(' . $unpack . '(' . $params . "))\n


	private $buffe
	private function _insertFastDecode($match)
		return '{' . $this->buffer . ';

	private function _insertFastEncode($match)
		return '{$encode=' . $this->buffer . ';


	// mmm.. ..which one do i need 
	private function _getEncoder($ascii)
		return $ascii > 10 ? $ascii > 36 ? $ascii > 62
		       '_encode95' : '_encode62' : '_encode36' : '_encode10


	// zero encodi
	// characters: 01234567
	private function _encode10($charCode)
		return $charCod


	// inherent base36 suppo
	// characters: 0123456789abcdefghijklmnopqrstuvwx
	private function _encode36($charCode)
		return base_convert($charCode, 10, 36


	// hitch a ride on base36 and add the upper case alpha characte
	// characters: 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWX
	private function _encode62($charCode)
		$res = '
		if ($charCode >= $this->_encoding)
			$res = $this->_encode62((int)($charCode / $this->_encoding)
	
		$charCode = $charCode % $this->_encodin

		if ($charCode > 3
			return $res . chr($charCode + 29
		el
			return $res . base_convert($charCode, 10, 36


	// use high-ascii valu
	// characters: ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüý
	private function _encode95($charCode)
		$res = '
		if ($charCode >= $this->_encodin
			$res = $this->_encode95($charCode / $this->_encoding

		return $res . chr(($charCode % $this->_encoding) + 161


	private function _safeRegExp($string)
		return '/'.preg_replace('/\$/', '\\\$', $string).'/


	private function _encodePrivate($charCode)
		return "_" . $charCod


	// protect characters used by the pars
	private function _escape($script)
		return preg_replace('/([\\\\\'])/', '\\\$1', $script


	// protect high-ascii characters already in the scri
	private function _escape95($script)
		return preg_replace_callbac
			'/[\\xa1-\\xff]/
			array(&$this, '_escape95Bis'
			$scri
		

	private function _escape95Bis($match)
		return '\x'.((string)dechex(ord($match))



	private function _getJSFunction($aName)
		if (defined('self::JSFUNCTION'.$aName
			return constant('self::JSFUNCTION'.$aName
		els
			return '


	// JavaScript Functions use
	// Note : In Dean's version, these functions are convert
	// with 'String(aFunctionName);
	// This internal conversion complete the original code, ex
	// 'while (aBool) anAction();' is converted 
	// 'while (aBool) { anAction(); }
	// The JavaScript functions below are correcte

	// unpacking function - this is the boot strap functi
	//  data extracted from this packing routine is passed 
	//  this function when decoded in the targ
	// NOTE ! : without the ';' fina
	const JSFUNCTION_unpack

'function($packed, $ascii, $count, $keywords, $encode, $decode)
    while ($count--)
        if ($keywords[$count])
            $packed = $packed.replace(new RegExp(\'\\\\b\' + $encode($count) + \'\\\\b\', \'g\'), $keywords[$count]
       
   
    return $packe
}

'function($packed, $ascii, $count, $keywords, $encode, $decode)
    while ($count-
        if ($keywords[$count
            $packed = $packed.replace(new RegExp(\'\\\\b\' + $encode($count) + \'\\\\b\', \'g\'), $keywords[$count]
    return $packe
}


	// code-snippet inserted into the unpacker to speed up decodi
	const JSFUNCTION_decodeBody
//_decode = function()
// does the browser support String.replace where t
//  replacement value is a functio

'    if (!\'\'.replace(/^/, String))
        // decode all the values we ne
        while ($count--)
            $decode[$encode($count)] = $keywords[$count] || $encode($count
       
        // global replacement functi
        $keywords = [function ($encoded) {return $decode[$encoded]}
        // generic mat
        $encode = function () {return \'\\\\w+\'
        // reset the loop counter -  we are now doing a global repla
        $count = 
   

//

'	if (!\'\'.replace(/^/, String))
        // decode all the values we ne
        while ($count--) $decode[$encode($count)] = $keywords[$count] || $encode($count
        // global replacement functi
        $keywords = [function ($encoded) {return $decode[$encoded]}
        // generic mat
        $encode = function () {return\'\\\\w+\'
        // reset the loop counter -  we are now doing a global repla
        $count = 
    }


	 // zero encodi
	 // characters: 01234567
	 const JSFUNCTION_encode10
'function($charCode)
    return $charCod
}';//;

	 // inherent base36 suppo
	 // characters: 0123456789abcdefghijklmnopqrstuvwx
	 const JSFUNCTION_encode36
'function($charCode)
    return $charCode.toString(36
}';//;

	// hitch a ride on base36 and add the upper case alpha characte
	// characters: 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWX
	const JSFUNCTION_encode62
'function($charCode)
    return ($charCode < _encoding ? \'\' : arguments.callee(parseInt($charCode / _encoding)))
    (($charCode = $charCode % _encoding) > 35 ? String.fromCharCode($charCode + 29) : $charCode.toString(36)
}

	// use high-ascii valu
	// characters: ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüý
	const JSFUNCTION_encode95
'function($charCode)
    return ($charCode < _encoding ? \'\' : arguments.callee($charCode / _encoding))
        String.fromCharCode($charCode % _encoding + 161
}'




class Vps_Assets_ParseMaster
	public $ignoreCase = fals
	public $escapeChar = '

	// constan
	const EXPRESSION = 
	const REPLACEMENT = 
	const LENGTH = 

	// used to determine nesting leve
	private $GROUPS = '/\\(/';/
	private $SUB_REPLACE = '/\\$\\d/
	private $INDEXED = '/^\\$\\d+$/
	private $TRIM = '/([\'"])\\1\\.(.*)\\.\\1\\1$/
	private $ESCAPE = '/\\\./';/
	private $QUOTE = '/\'/
	private $DELETED = '/\\x01[^\\x01]*\\x01/';/

	public function add($expression, $replacement = '')
		// count the number of sub-expressio
		//  - add one because each pattern is itself a sub-expressi
		$length = 1 + preg_match_all($this->GROUPS, $this->_internalEscape((string)$expression), $out

		// treat only strings $replaceme
		if (is_string($replacement))
			// does the pattern deal with sub-expression
			if (preg_match($this->SUB_REPLACE, $replacement))
				// a simple lookup? (e.g. "$2
				if (preg_match($this->INDEXED, $replacement))
					// store the index (used for fast retrieval of matched string
					$replacement = (int)(substr($replacement, 1)) - 
				} else { // a complicated lookup (e.g. "Hello $2 $1
					// build a function to do the look
					$quote = preg_match($this->QUOTE, $this->_internalEscape($replacement
					         ? '"' : "'
					$replacement = arra
						'fn' => '_backReferences
						'data' => arra
							'replacement' => $replacemen
							'length' => $lengt
							'quote' => $quo
					
					
			
		
	
		// pass the modified argumen
		if (!empty($expression)) $this->_add($expression, $replacement, $length
		else $this->_add('/^$/', $replacement, $length


	public function exec($string)
		// execute the global replaceme
		$this->_escaped = array(

		// simulate the _patterns.toSTring of De
		$regexp = '/
		foreach ($this->_patterns as $reg)
			$regexp .= '(' . substr($reg[self::EXPRESSION], 1, -1) . ')|
	
		$regexp = substr($regexp, 0, -1) . '/
		$regexp .= ($this->ignoreCase) ? 'i' : '

		$string = $this->_escape($string, $this->escapeChar
		$string = preg_replace_callbac
			$regex
			arra
				&$thi
				'_replacemen
			
			$stri
		
		$string = $this->_unescape($string, $this->escapeChar

		return preg_replace($this->DELETED, '', $string


	public function reset()
		// clear the patterns collection so that this object may be re-us
		$this->_patterns = array(


	// priva
	private $_escaped = array();  // escaped characte
	private $_patterns = array(); // patterns stored by ind

	// create and add a new pattern to the patterns collecti
	private function _add()
		$arguments = func_get_args(
		$this->_patterns[] = $argument


	// this is the global replace function (it's quite complicate
	private function _replacement($arguments)
		if (empty($arguments)) return '

		$i = 1; $j = 
		// loop through the patter
		while (isset($this->_patterns[$j]))
			$pattern = $this->_patterns[$j++
			// do we have a resul
			if (isset($arguments[$i]) && ($arguments[$i] != ''))
				$replacement = $pattern[self::REPLACEMENT
		
				if (is_array($replacement) && isset($replacement['fn']))
			
					if (isset($replacement['data'])) $this->buffer = $replacement['data'
					return call_user_func(array(&$this, $replacement['fn']), $arguments, $i
			
				} elseif (is_int($replacement))
					return $arguments[$replacement + $i
		
			
				$delete = ($this->escapeChar == '' 
				           strpos($arguments[$i], $this->escapeChar) === fals
				        ? '' : "\x01" . $arguments[$i] . "\x01
				return $delete . $replacemen
	
			// skip over references to sub-expressio
			} else
				$i += $pattern[self::LENGTH
		
	


	private function _backReferences($match, $offset)
		$replacement = $this->buffer['replacement'
		$quote = $this->buffer['quote'
		$i = $this->buffer['length'
		while ($i)
			$replacement = str_replace('$'.$i--, $match[$offset + $i], $replacement
	
		return $replacemen


	private function _replace_name($match, $offset
		$length = strlen($match[$offset + 2]
		$start = $length - max($length - strlen($match[$offset + 3]), 0
		return substr($match[$offset + 1], $start, $length) . $match[$offset + 4


	private function _replace_encoded($match, $offset)
		return $this->buffer[$match[$offset]



	// php : we cannot pass additional data to preg_replace_callbac
	// and we cannot use &$this in create_function, so let's go to lower lev
	private $buffe

	// encode escaped characte
	private function _escape($string, $escapeChar)
		if ($escapeChar)
			$this->buffer = $escapeCha
			return preg_replace_callbac
				'/\\' . $escapeChar . '(.)' .'/
				array(&$this, '_escapeBis'
				$stri
			
	
		} else
			return $strin
	

	private function _escapeBis($match)
		$this->_escaped[] = $match[1
		return $this->buffe


	// decode escaped characte
	private function _unescape($string, $escapeChar)
		if ($escapeChar)
			$regexp = '/'.'\\'.$escapeChar.'/
			$this->buffer = array('escapeChar'=> $escapeChar, 'i' => 0
			return preg_replace_callba
		
				$regex
				array(&$this, '_unescapeBis'
				$stri
			
	
		} else
			return $strin
	

	private function _unescapeBis()
		if (!empty($this->_escaped[$this->buffer['i']]))
			 $temp = $this->_escaped[$this->buffer['i']
		} else
			$temp = '
	
		$this->buffer['i']+
		return $this->buffer['escapeChar'] . $tem


	private function _internalEscape($string)
		return preg_replace($this->ESCAPE, '', $string



