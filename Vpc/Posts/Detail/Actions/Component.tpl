<?
$echos = array();
if (isset($this->edit) && $this->edit) {
    $echos[] = $this->componentLink($this->edit);
}
if (isset($this->delete) && $this->delete) {
    $echos[] = $this->componentLink($this->delete);
}
if (isset($this->report) && $this->report) {
    $echos[] = $this->componentLink($this->report);
}
if (isset($this->quote) && $this->quote) {
    $echos[] = $this->componentLink($this->quote);
}
echo implode(' | ', $echos);