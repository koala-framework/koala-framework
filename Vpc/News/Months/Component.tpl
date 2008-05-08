<ul class="newsCatagory">
    <?php foreach ($this->months as $month) { ?>
    <li>
        <a href="<?= $month['href'] ?>"><?= $month['monthName'] ?> <?= $month['<year'] ?></a>
    </li>
    <?php } ?>
</ul>