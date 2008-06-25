<?= trlVps('Hello {0}!', $this->fullname); ?>

<?= trlVps('A new Post has been written in thread:'); ?> <?= $this->threadName; ?>
<?= trlVps('in the forum of:'); ?> <?= $this->webUrl; ?>

<?= trlVps('Click the following link to go directly to the mentioned thread:'); ?>
<?= $this->webUrl.$this->threadUrl; ?>

<?= $this->applicationName; ?>

--
<?= trlVps('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
