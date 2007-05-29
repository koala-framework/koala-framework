<?
function smarty_block_show($params, $content, &$smarty, &$repeat)
{
    if (!isset($content)) return;
    if (!(isset($_GET["mode"]) && $_GET["mode"] == 'edit')) {
    	return $content;
    }
}

?>