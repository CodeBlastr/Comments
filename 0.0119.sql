
ALTER TABLE `projects` CHANGE `id` `id` VARCHAR( 36 ) NOT NULL ;

ALTER TABLE `messages` CHANGE `id` `id` VARCHAR( 36 ) NULL DEFAULT NULL 

ALTER TABLE `messages` CHANGE `parent_id` `parent_id` VARCHAR( 36 ) NULL DEFAULT NULL ;

--