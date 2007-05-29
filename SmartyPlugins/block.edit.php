<?
function smarty_block_edit($params, $content, &$smarty, &$repeat)
{
    if (!isset($content)) return;
    if (isset($_GET["mode"]) && $_GET["mode"] == 'edit') {
    	return $content;
    }
}

?>