<?php foreach ($this->news as $n) { ?>
    <div>
        <a href="<?= $$n['href'] ?>"><?= $n['title'] ?></a>
    </div>
<?php } ?>
