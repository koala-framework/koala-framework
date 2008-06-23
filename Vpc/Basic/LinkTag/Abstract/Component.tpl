<?php
echo '<a href="' . $this->data->url . '"';
if($this->data->rel) { echo ' rel="' . $this->data->rel . '"'; }
echo '>';