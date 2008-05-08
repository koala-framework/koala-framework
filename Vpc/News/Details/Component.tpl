<h2><?= $this->news['title'] ?></h2>
<?php
echo $this->news['publish_date'];
foreach ($this->paragraphs as $paragraph) {
    $this->component($paragraph);
}
