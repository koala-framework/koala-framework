<?php
$this->component($this->paging);
foreach ($this->news as $n) {
    echo '<a href="' . $n['href'] . '">' . $n['title'] . '</a>';
    echo '<p>' . $n['teaser'] . '</p>';
    echo $n['publish_date'];
    echo '<br /><br />';
}
