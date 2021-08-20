CREATE TABLE IF NOT EXISTS `kwf_user_actionslog` (
    `id` bigint(20) NOT NULL,
    `date` datetime NOT NULL,
    `user_name` varchar(255) NOT NULL,
    `user_email` varchar(255) NOT NULL,
    `domain` varchar(255) NOT NULL,
    `url` varchar(255) NOT NULL,
    `details` varchar(255) NULL,
    `changes` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `kwf_user_actionslog`
    ADD PRIMARY KEY (`id`), ADD KEY `user_name` (`user_name`(191));

ALTER TABLE `kwf_user_actionslog`
    MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `kwf_user_actionslog` ADD INDEX(`user_name`);

ALTER TABLE `kwf_user_actionslog` ADD INDEX(`domain`);
