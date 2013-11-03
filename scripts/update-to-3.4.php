<?
$file = is_file('vkwf_branch') ? 'vkwf_branch' : 'kwf_branch';
file_put_contents($file, "3.4\n");
echo "Changed $file to 3.4\n";

function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        if (dirname($dir) == './kwf-lib') continue;
        if (dirname($dir) == './library') continue;
        $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

function checkGallery($files) {
    foreach ($files as $f) {
        $content = file_get_contents($f);
        if (strpos($content, 'Kwc_List_Gallery_Component')) {
            echo ("$f: Gallery changed the image enlarge tag from LinkTag to EnlargeTag. Please make sure that EnlargeTag is ok or change the component to Kwc_List_ImagesLinked_Component\n");
        }
    }
}

function checkBaseProperties($files) {
    foreach ($files as $f) {
        $content = file_get_contents($f);
        if (strpos($content, 'hasDomain')) {
            echo "\033[45mPlease change setting hasDomain to BaseProperties\033[00m\n";
        }
        if (strpos($content, 'hasLanguage')) {
            echo "\033[45mPlease change setting hasLanguage to BaseProperties\033[00m\n";
        }
        if (strpos($content, 'hasMoneyFormat')) {
            echo "\033[45mPlease change setting hasMoneyFormat to BaseProperties\033[00m\n";
        }
    }
}

function updateStatisticsConfig()
{
    $content = file_get_contents('config.ini');
    $original = $content;
    $content = str_replace('statistic.', 'statistics.', $content);
    $content = str_replace('moneyFormat', 'money.format', $content);
    $content = str_replace('moneyDecimals', 'money.decimals', $content);
    $content = str_replace('moneyDecimalSeparator', 'money.decimalSeparator', $content);
    $content = str_replace('moneyThousandSeparator', 'money.thousandSeparator', $content);
    $content = preg_replace('/kwc\.domains\.([a-z]*)\.piwikId/', 'kwc.domains.$1.statistics.piwikId', $content);
    $content = preg_replace('/kwc\.domains\.([a-z]*)\.piwikDomain/', 'kwc.domains.$1.statistics.piwikDomain', $content);
    $content = preg_replace('/kwc\.domains\.([a-z]*)\.twynCustomerId/', 'kwc.domains.$1.statistics.twynCustomerId', $content);
    $content = preg_replace('/kwc\.domains\.([a-z]*)\.analyticsCode/', 'kwc.domains.$1.statistics.analyticsCode', $content);
    $content = preg_replace('/kwc\.domains\.([a-z]*)\.ignoreAnalyticsCode/', 'kwc.domains.$1.statistics.ignoreAnalyticsCode', $content);
    $content = preg_replace('/kwc\.domains\.([a-z]*)\.ignorePiwikCode/', 'kwc.domains.$1.statistics.ignorePiwikCode', $content);
    $content = str_replace('piwikId', 'piwik.id', $content);
    $content = str_replace('piwikDomain', 'piwik.domain', $content);
    $content = str_replace('ignorePiwikCode', 'piwik.ignore', $content);
    $content = str_replace('twynCustomerId', 'twin.customerId', $content);
    $content = str_replace('analyticsCode', 'analytics.code', $content);
    $content = str_replace('ignoreAnalyticsCode', 'analytics.ignore', $content);
    if ($original != $content) {
        file_put_contents('config.ini', $content);
        echo "Updated statistics config\n";
    }
}

function replaceFiles($files, $from, $to) {
    foreach ($files as $f) {
        $content = file_get_contents($f);
        if (strpos($content, $from)) {
            file_put_contents($f, str_replace($from, $to, $content));
            echo "Change $f: $from -> $to\n";
        }
    }
}

function updateIncludeCode()
{
    $files = glob_recursive('Master.tpl');
    $removeBoxes = array('title', 'metatags', 'opengraph', 'piwik', 'analytics', 'assets', 'rssFeeds');
    foreach ($files as $f) {
        $c = file_get_contents($f);
        if (!strpos($c, 'this->includeCode')) {
            $c = str_replace("<head>\n", "<head>\n        <?=\$this->includeCode('header')?>\n", $c);
            $c = str_replace("</body>", "    <?=\$this->includeCode('footer')?>\n    </body>", $c);
            foreach ($removeBoxes as $b) {
                $c = preg_replace("#^\s*<\?=\\\$this->component\(\\\$this->boxes\['$b'\]\);\?> *\n#im", "", $c);
            }
            $c = preg_replace("#^\s*<\?=\\\$this->debugData\(\);\?> *\n#im", "", $c);
            $c = preg_replace("#^\s*<link rel=\"shortcut icon\" href=\"/assets/web/images/favicon\.ico\" /> *\n#im", "", $c);
            $c = preg_replace("#^\s*<\?=\\\$this->statisticCode\(\);\?> *\n#im", "", $c);
            $c = preg_replace("#^\s*<\?=\\\$this->assets\(\'Frontend\'\);\?> *\n#im", "", $c);
            file_put_contents($f, $c);
            echo "Updated $f to use new includeCode helper\n";
        }
    }
}

function updateMasterCssClass()
{
    $files = glob_recursive('Master.tpl');
    foreach ($files as $f) {
        $c = file_get_contents($f);
        $c = str_replace('<body class="frontend', '<body class="', $c);
        $c = str_replace('<body class="', '<body class="<?=$this->cssClass?>', $c);
        $c = str_replace('<body>', '<body class="<?=$this->cssClass?>">', $c);
        file_put_contents($f, $c);
        echo "Updated $f to use new cssClass\n";
    }
}

function moveCssFiles()
{
    if (file_exists('css/master.css') && file_exists('css/web.css')) {
        $c = file_get_contents('css/master.css')."\n\n".file_get_contents('css/web.css');
        file_put_contents("css/web.css", $c);
        unlink('css/master.css');
        echo "moved css/master.css contents into css/web.css\n";
    }
    if (file_exists('css/web.css') && file_exists('components/Root/Component.php')) {
        rename('css/web.css', 'components/Root/Web.css');
        echo "moved css/web.css to components/Root/Web.css\n";
        file_put_contents('components/Root/Master.scss', '/* move styling relevant for Master.tpl from Web.css in here*/');
        echo "created components/Root/Master.scss\n";
    }
    if (file_exists('css/web.scss') && file_exists('components/Root/Component.php')) {
        rename('css/web.scss', 'components/Root/Web.scss');
        echo "moved css/web.scss to components/Root/Web.scss\n";
    }
    if (file_exists('css/web.printcss') && file_exists('components/Root/Component.php')) {
        rename('css/web.printcss', 'components/Root/Web.printcss');
        echo "moved css/web.printcss to components/Root/Web.printcss\n";
    }
    if (file_exists('dependencies.ini')) {
        $c = file_get_contents('dependencies.ini');
        $c = str_replace("Frontend.files[] = web/css/master.css\n", '', $c);
        $c = str_replace("Frontend.files[] = web/css/web.css\n", '', $c);
        $c = str_replace("Frontend.files[] = web/css/web.scss\n", '', $c);
        $c = str_replace("Frontend.files[] = web/css/web.printcss\n", '', $c);
        file_put_contents('dependencies.ini', $c);
        echo "updated dependencies.ini\n";
    }
}

function updateAclTrl()
{
    $c = file_get_contents('app/Acl.php');
    $c = preg_replace('#(trl[cp]?(Kwf)?)\\(#', '\1Static(', $c);
    file_put_contents('app/Acl.php', $c);
    echo "updated app/Acl.php to use trlStatic\n";
}
$files = glob_recursive('Component.php');
$files[] = 'config.ini';
replaceFiles($files, 'Kwc_Composite_Images_Component', 'Kwc_List_Images_Component');
replaceFiles($files, 'Kwc_Composite_LinksImages_Component', 'Kwc_List_ImagesLinked_Component');
replaceFiles($files, 'Kwc_Composite_Downloads_Component', 'Kwc_List_Downloads_Component');
replaceFiles($files, 'Kwc_Composite_ImagesEnlarge_Component', 'Kwc_List_Gallery_Component');
replaceFiles($files, 'Kwc_Composite_Links_Component', 'Kwc_List_Links_Component');
replaceFiles($files, 'Kwc_Box_Analytics_Component', 'Kwc_Statistics_Analytics_Component');
replaceFiles($files, 'Kwc_Root_DomainRoot_Domain_Analytics_Component', 'Kwc_Statistics_Analytics_Component');
replaceFiles($files, 'Kwc_Root_DomainRoot_Domain_AdsenseAnalytics_Component', 'Kwc_Statistics_Adsense_Component');
checkGallery($files);
checkBaseProperties($files);
updateStatisticsConfig();
updateIncludeCode();
updateMasterCssClass();
moveCssFiles();
updateAclTrl();
