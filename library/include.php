<?
$filename = $_GET['filename'];
if (!preg_match('/[a-zA-Z0-9]+\.js/', $filename)) {
	echo "invalid file: $filename";
} else if (is_file($filename)) {
	include $filename;
}
?>
