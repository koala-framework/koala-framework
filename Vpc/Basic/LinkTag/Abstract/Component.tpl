<?php
echo '<a href="' . $this->href . '"';
if($this->rel) { echo ' rel="' . $this->rel . '"'; }
echo '>';