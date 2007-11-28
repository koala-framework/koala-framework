<?p
class Vps_Assets_Dependenci

    private $_file
    private $_confi
    private $_asset
    private $_dependenciesConfi
    private $_processedDependencies = array(
    private $_processedComponents = array(

    public function __construct($assets, $config = nul
   
        if (!isset($config))
            $config = Zend_Registry::get('config'
       
        $this->_config = $confi

        $this->_assets = $asset
   

    private function _getFilePath($fil
   
        return Vps_Assets_Loader::getAssetPath($file, $this->_config->path
   

    public function getAssetFiles($fileType = nul
   
        $files = $this->getFiles($fileType
        $ret = array(
        foreach ($files as $file)
            $ret[] = '/assets/'.$fil
       
        return $re
   
    public function getFiles($fileType = nul
   
        if (!isset($this->_files))
            $this->_files = array(
            foreach($this->_assets as $d=>$v)
                if ($v)
                    $this->_processDependency($d
               
           
       

        if (is_null($fileType)) return $this->_file

        $files = array(
        foreach ($this->_files as $file)
            if (substr($file, -strlen($fileType)) == $fileType)
                $files[] = $fil
           
       
        return $file
   
  
    public function getFilePaths($fileType = nul
   
        $paths = array(
        foreach ($this->getFiles($fileType) as $file)
            $paths[] = $this->_getFilePath($file
       
        return $path
   

    private function _pack($contents, $fileTyp
   
        if ($fileType == 'js')
            $contents = str_replace("\r", "\n", $contents

            // remove commen
            $contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents
            $contents = preg_replace('!//[^\n]*!', '', $contents

            // remove tabs, spaces, newlines, etc. - funktioniert nicht - da fehlen hinundwider
            //$contents = str_replace(array("\r", "\n", "\t"), "", $contents

            // multiple whitespac
            $contents = str_replace("\t", " ", $contents
            $contents = preg_replace('/(\n)\n+/', '$1', $contents
            $contents = preg_replace('/(\n)\ +/', '$1', $contents
            $contents = preg_replace('/(\ )\ +/', '$1', $contents

        } else if ($fileType == 'css')

            $contents = str_replace("\r", "\n", $contents

            // remove commen
            $contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents

            // multiple whitespac
            $contents = str_replace("\t", " ", $contents
            $contents = preg_replace('/(\n)\n+/', '$1', $contents
            $contents = preg_replace('/(\n)\ +/', '$1', $contents
            $contents = preg_replace('/(\ )\ +/', '$1', $contents
       
        return $content
   

    public function getPackedAll($fileTyp
   
        return $this->_pack($this->getContentsAll($fileType), $fileType
   

    public function getContentsAll($fileTyp
   
        $contents = '
        foreach($this->getFiles($fileType) as $file)
            $contents .= Vps_Assets_Loader::getFileContents($file, $this->_config->path) . "\n
       
        return $content
   

    private function _getDependenciesConfig()
        if(!isset($this->_dependenciesConfig))
            $this->_dependenciesConfig = new Zend_Config_Ini(VPS_PATH.'/config.ini', 'dependencies
                                                array('allowModifications'=>true)
            $this->_dependenciesConfig->merge(new Zend_Config_Ini('application/config.ini', 'dependencies')
       
        return $this->_dependenciesConfi
   

    private function _processDependency($dependenc
   
        if (in_array($dependency, $this->_processedDependencies)) retur
        $this->_processedDependencies[] = $dependenc
        if ($dependency == 'Components')
            foreach ($this->_config->pageClasses as $c)
                if ($c->class && $c->text)
                    $this->_processComponentDependency($c->class
               
           
            retur
       
        if (!isset($this->_getDependenciesConfig()->$dependency))
            throw new Vps_Exception("Can't resolve dependency '$dependency'."
       
        $deps = $this->_getDependenciesConfig()->$dependenc

        if (isset($deps->dep))
            foreach ($deps->dep as $d)
                $this->_processDependency($d
           
       

        if (isset($deps->files))
            foreach ($deps->files as $file)
                $this->_processDependencyFile($file
           
       
        retur
   
    private function _processComponentDependency($
   
        if (in_array($c, $this->_processedComponents)) retur
        $this->_processedComponents[] = $
        $classes = Vpc_Abstract::getSetting($c, 'childComponentClasses'
        if (is_array($classes))
            foreach ($classes as $class)
                $assets = Vpc_Abstract::getSetting($class, 'assets'
                if (isset($assets['dep']))
                    foreach ($assets['dep'] as $dep)
                        $this->_processDependency($dep
                   
               
                if (isset($assets['files']))
                    foreach ($assets['files'] as $file)
                        $this->_processDependencyFile($file
                   
               
                $file = Vpc_Admin::getComponentFile($class, '', 'css'
                if ($file)
                    foreach ($this->_config->path as $type=>$path)
                        if ($path == '.') $path = getcwd(
                        if (substr($file, 0, strlen($path)) == $path)
                            $file = $type.substr($file, strlen($path)
                            if (!in_array($file, $this->_files))
                                $this->_files[] = $fil
                                brea
                           
                       
                   
               
                $this->_processComponentDependency($class
           
       
   

    private function _processDependencyFile($fil
   
        if (substr($file, -2)=="/*")
            $pathType = substr($file, 0, strpos($file, '/')
            if (!isset($this->_config->path->$pathType))
                throw new Vps_Exception("Assets-Path-Type '$pathType' not found in config."
           
            $file = substr($file, strpos($file, '/')); //pathtype abschneid
            $file = substr($file, 0, -1); //* abschneid
            $path = $this->_config->path->$pathType.$fil
            if (!file_exists($path))
                throw new Vps_Exception("Path '$path' does not exist."
           
            $DirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)
            foreach ($DirIterator as $file)
                if (!preg_match('#/\\.svn/#', $file->getPathname(
                    && (substr($file->getPathname(), -3) == '.j
                        || substr($file->getPathname(), -4) == '.css'))
                    $f = $file->getPathname(
                    $f = substr($f, strlen($this->_config->path->$pathType)
                    $f = $pathType . $
                    if (!in_array($f, $this->_files))
                        $this->_files[] = $
                   
               
           
        } else
            if (!in_array($file, $this->_files))
                $this->_files[] = $fil
           
       
   
