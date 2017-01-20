/* guestlistadmin "version 2" - empty database creation file */

CREATE TABLE `organizations` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `events` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`organization` INT NOT NULL,
	`name` varchar(255) NOT NULL,
	`venue` INT NOT NULL,
	`start` DATETIME,
	`end` DATETIME,
	PRIMARY KEY (`id`)
);

CREATE TABLE `venues` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` varchar(255),
	`address` varchar(255),
	`organization` INT NOT NULL,
	`timezone` varchar(255),
	`currency` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `users` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`organization` INT,
	`name` varchar(255) NOT NULL,
	`email` varchar(255),
	`password` varchar(64),
	`password_salt` varchar(32) UNIQUE,
	`role` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `groups` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`organization` INT,
	PRIMARY KEY (`id`)
);

CREATE TABLE `lists` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`event` INT NOT NULL,
	`valid_start` DATETIME,
	`valid_end` DATETIME,
	`signup_valid` BOOLEAN NOT NULL DEFAULT 0,
	`signup_limit` INT NOT NULL DEFAULT '-1',
	`status` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `ages` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`organization` INT,
	`age_low` INT,
	`age_high` INT,
	PRIMARY KEY (`id`)
);

CREATE TABLE `guests` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`list` INT NOT NULL,
	`time` TIMESTAMP NOT NULL,
	`name` varchar(255) NOT NULL,
	`email` varchar(255),
	`age` INT,
	`gender` INT,
	`custom` varchar(255),
	`additional_guests` INT NOT NULL DEFAULT '0',
	`notes` varchar(255),
	`user` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `checkins` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`event` INT NOT NULL,
	`list` INT NOT NULL,
	`guest` INT NOT NULL,
	`time` TIMESTAMP NOT NULL,
	`additional_guests` INT DEFAULT '0',
	PRIMARY KEY (`id`)
);

CREATE TABLE `genders` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `settings` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`organization` INT NOT NULL,
	`timezone` varchar(255),
	`date_format` varchar(255),
	`domain` INT,
	`event_start` TIME,
	`event_end` TIME,
	`list_expiration` TIME,
	`currency` INT,
	PRIMARY KEY (`id`)
);

CREATE TABLE `shortlinks` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`link` varchar(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `list_status` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`status` varchar(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `guest_fields` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`organization` INT NOT NULL,
	`name` varchar(255) NOT NULL,
	`description` varchar(255) NOT NULL,
	`type` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `guest_field_type` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`type` varchar(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `shortlink_list` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`link_id` INT NOT NULL,
	`list` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `shortlink_user` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`link` INT NOT NULL,
	`user` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `domains` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`organization` INT NOT NULL,
	`domain` varchar(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `group_members` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user` INT NOT NULL,
	`group` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `list_users` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`list` INT NOT NULL,
	`user` INT NOT NULL,
	`signup_valid` BOOLEAN NOT NULL DEFAULT 0,
	`signup_limit` INT DEFAULT '-1',
	PRIMARY KEY (`id`)
);

CREATE TABLE `list_groups` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`list` INT NOT NULL,
	`group` INT NOT NULL,
	`signup_valid` BOOLEAN NOT NULL DEFAULT 0,
	`signup_limit_group` INT NOT NULL DEFAULT '-1',
	`signup_limit_user` INT NOT NULL DEFAULT '-1',
	PRIMARY KEY (`id`)
);

CREATE TABLE `currencies` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`code` varchar(3) NOT NULL UNIQUE,
	`name` varchar(255) NOT NULL UNIQUE,
	`symbol` varchar(3),
	`prefix` BOOLEAN NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
);

CREATE TABLE `list_constraint` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`time_begin` TIME,
	`time_end` TIME,
	`gender` INT,
	`age` INT,
	`price` DECIMAL NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `list_constraint_group` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`organization` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `list_constraints` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`constraint_group` INT NOT NULL,
	`constraint` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `list_constraint_groups` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`list` INT NOT NULL,
	`constraint_group` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `themes` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`organization` INT NOT NULL,
	`name` varchar(255) NOT NULL,
	`filename` varchar(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `group_administrators` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`group` INT NOT NULL,
	`user` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `user_permissions` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`bit` BINARY NOT NULL UNIQUE,
	`name` varchar(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `user_roles` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL UNIQUE,
	`permissions` BINARY NOT NULL,
	PRIMARY KEY (`id`)
);

ALTER TABLE `events` ADD CONSTRAINT `events_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `events` ADD CONSTRAINT `events_fk1` FOREIGN KEY (`venue`) REFERENCES `venues`(`id`);

ALTER TABLE `venues` ADD CONSTRAINT `venues_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `venues` ADD CONSTRAINT `venues_fk1` FOREIGN KEY (`currency`) REFERENCES `currencies`(`id`);

ALTER TABLE `users` ADD CONSTRAINT `users_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `users` ADD CONSTRAINT `users_fk1` FOREIGN KEY (`role`) REFERENCES `user_roles`(`id`);

ALTER TABLE `groups` ADD CONSTRAINT `groups_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `lists` ADD CONSTRAINT `lists_fk0` FOREIGN KEY (`event`) REFERENCES `events`(`id`);

ALTER TABLE `lists` ADD CONSTRAINT `lists_fk1` FOREIGN KEY (`status`) REFERENCES `list_status`(`id`);

ALTER TABLE `ages` ADD CONSTRAINT `ages_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `guests` ADD CONSTRAINT `guests_fk0` FOREIGN KEY (`list`) REFERENCES `lists`(`id`);

ALTER TABLE `guests` ADD CONSTRAINT `guests_fk1` FOREIGN KEY (`age`) REFERENCES `ages`(`id`);

ALTER TABLE `guests` ADD CONSTRAINT `guests_fk2` FOREIGN KEY (`gender`) REFERENCES `genders`(`id`);

ALTER TABLE `guests` ADD CONSTRAINT `guests_fk3` FOREIGN KEY (`user`) REFERENCES `users`(`id`);

ALTER TABLE `checkins` ADD CONSTRAINT `checkins_fk0` FOREIGN KEY (`event`) REFERENCES `events`(`id`);

ALTER TABLE `checkins` ADD CONSTRAINT `checkins_fk1` FOREIGN KEY (`list`) REFERENCES `lists`(`id`);

ALTER TABLE `checkins` ADD CONSTRAINT `checkins_fk2` FOREIGN KEY (`guest`) REFERENCES `guests`(`id`);

ALTER TABLE `settings` ADD CONSTRAINT `settings_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `settings` ADD CONSTRAINT `settings_fk1` FOREIGN KEY (`domain`) REFERENCES `domains`(`id`);

ALTER TABLE `settings` ADD CONSTRAINT `settings_fk2` FOREIGN KEY (`currency`) REFERENCES `currencies`(`id`);

ALTER TABLE `guest_fields` ADD CONSTRAINT `guest_fields_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `guest_fields` ADD CONSTRAINT `guest_fields_fk1` FOREIGN KEY (`type`) REFERENCES `guest_field_type`(`id`);

ALTER TABLE `shortlink_list` ADD CONSTRAINT `shortlink_list_fk0` FOREIGN KEY (`link_id`) REFERENCES `shortlinks`(`id`);

ALTER TABLE `shortlink_list` ADD CONSTRAINT `shortlink_list_fk1` FOREIGN KEY (`list`) REFERENCES `lists`(`id`);

ALTER TABLE `shortlink_user` ADD CONSTRAINT `shortlink_user_fk0` FOREIGN KEY (`link`) REFERENCES `shortlinks`(`id`);

ALTER TABLE `shortlink_user` ADD CONSTRAINT `shortlink_user_fk1` FOREIGN KEY (`user`) REFERENCES `users`(`id`);

ALTER TABLE `domains` ADD CONSTRAINT `domains_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `group_members` ADD CONSTRAINT `group_members_fk0` FOREIGN KEY (`user`) REFERENCES `users`(`id`);

ALTER TABLE `group_members` ADD CONSTRAINT `group_members_fk1` FOREIGN KEY (`group`) REFERENCES `groups`(`id`);

ALTER TABLE `list_users` ADD CONSTRAINT `list_users_fk0` FOREIGN KEY (`list`) REFERENCES `lists`(`id`);

ALTER TABLE `list_users` ADD CONSTRAINT `list_users_fk1` FOREIGN KEY (`user`) REFERENCES `users`(`id`);

ALTER TABLE `list_groups` ADD CONSTRAINT `list_groups_fk0` FOREIGN KEY (`list`) REFERENCES `lists`(`id`);

ALTER TABLE `list_groups` ADD CONSTRAINT `list_groups_fk1` FOREIGN KEY (`group`) REFERENCES `groups`(`id`);

ALTER TABLE `list_constraint` ADD CONSTRAINT `list_constraint_fk0` FOREIGN KEY (`gender`) REFERENCES `genders`(`id`);

ALTER TABLE `list_constraint` ADD CONSTRAINT `list_constraint_fk1` FOREIGN KEY (`age`) REFERENCES `ages`(`id`);

ALTER TABLE `list_constraint_group` ADD CONSTRAINT `list_constraint_group_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `list_constraints` ADD CONSTRAINT `list_constraints_fk0` FOREIGN KEY (`constraint_group`) REFERENCES `list_constraint_group`(`id`);

ALTER TABLE `list_constraints` ADD CONSTRAINT `list_constraints_fk1` FOREIGN KEY (`constraint`) REFERENCES `list_constraint`(`id`);

ALTER TABLE `list_constraint_groups` ADD CONSTRAINT `list_constraint_groups_fk0` FOREIGN KEY (`list`) REFERENCES `lists`(`id`);

ALTER TABLE `list_constraint_groups` ADD CONSTRAINT `list_constraint_groups_fk1` FOREIGN KEY (`constraint_group`) REFERENCES `list_constraint_group`(`id`);

ALTER TABLE `themes` ADD CONSTRAINT `themes_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `group_administrators` ADD CONSTRAINT `group_administrators_fk0` FOREIGN KEY (`group`) REFERENCES `groups`(`id`);

ALTER TABLE `group_administrators` ADD CONSTRAINT `group_administrators_fk1` FOREIGN KEY (`user`) REFERENCES `users`(`id`);
