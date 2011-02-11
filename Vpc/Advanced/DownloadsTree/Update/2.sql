 ALTER TABLE `vpc_downloadstree_projects_to_users` DROP FOREIGN KEY `vpc_downloadstree_projects_to_users_ibfk_1` ;

ALTER TABLE `vpc_downloadstree_projects_to_users` ADD FOREIGN KEY ( `project_id` ) REFERENCES `unicope_franz`.`vpc_downloadstree_projects` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

 ALTER TABLE `vpc_downloadstree_projects` DROP FOREIGN KEY `vpc_downloadstree_projects_ibfk_1` ;

ALTER TABLE `vpc_downloadstree_projects` ADD FOREIGN KEY ( `parent_id` ) REFERENCES `unicope_franz`.`vpc_downloadstree_projects` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `vpc_downloadstree_downloads` DROP FOREIGN KEY `vpc_downloadstree_downloads_ibfk_3` ;

ALTER TABLE `vpc_downloadstree_downloads` ADD FOREIGN KEY ( `project_id` ) REFERENCES `unicope_franz`.`vpc_downloadstree_projects` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `vpc_downloadstree_downloads` DROP FOREIGN KEY `vpc_downloadstree_downloads_ibfk_4` ;

ALTER TABLE `vpc_downloadstree_downloads` ADD FOREIGN KEY ( `vps_upload_id` ) REFERENCES `unicope_franz`.`vps_uploads` (
`id`
);
