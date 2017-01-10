/* guestlistadmin "version 2" - empty database creation file */

CREATE TABLE `organizations` (
	`id` INT NOT NULL PRIMARY KEY,
	`name` VARCHAR(255) NOT NULL
);

CREATE TABLE `events` (
	`id` INT NOT NULL PRIMARY KEY,
	`organization` INT NOT NULL,
	`name` varchar(255) NOT NULL,
	`venue` INT NOT NULL,
	`start` DATETIME,
	`end` DATETIME
);

CREATE TABLE `venues` (
	`id` INT NOT NULL,
	`name` varchar(255),
	`address` varchar(255),
	`organization` INT NOT NULL,
	`timezone` varchar(255),
	PRIMARY KEY (`id`)
);

CREATE TABLE `users` (
	`id` INT NOT NULL,
	`organization` varchar(255),
	`name` varchar(255) NOT NULL,
	`email` varchar(255) UNIQUE,
	`password` varchar(64),
	`password_salt` varchar(32) UNIQUE,
	`groups` varchar(255),
	PRIMARY KEY (`id`)
);

CREATE TABLE `groups` (
	`id` INT NOT NULL,
	`name` varchar(255) NOT NULL,
	`organization` INT,
	`administrators` varchar(255),
	PRIMARY KEY (`id`)
);

CREATE TABLE `lists` (
	`id` INT NOT NULL,
	`event` INT NOT NULL,
	`valid_start` DATETIME,
	`valid_end` DATETIME,
	`age` INT,
	`price` INT,
	`users` varchar(255),
	`groups` varchar(255),
	`signup_valid` BOOLEAN NOT NULL DEFAULT 'false',
	`signup_limit` INT NOT NULL DEFAULT '-1',
	`status` INT NOT NULL,
	`gender` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `prices` (
	`id` INT NOT NULL,
	`organization` INT NOT NULL,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `ages` (
	`id` INT NOT NULL,
	`name` varchar(255) NOT NULL,
	`organization` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `guests` (
	`id` INT NOT NULL,
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
	`id` INT NOT NULL,
	`event` INT NOT NULL,
	`list` INT NOT NULL,
	`guest` INT NOT NULL,
	`time` TIMESTAMP NOT NULL,
	`additional_guests` INT DEFAULT '0',
	PRIMARY KEY (`id`)
);

CREATE TABLE `genders` (
	`id` INT NOT NULL,
	`name` varchar(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `settings` (
	`id` INT NOT NULL,
	`organization` INT NOT NULL,
	`timezone` varchar(255),
	`date_format` varchar(255),
	`short_url` varchar(255),
	`event_start` TIME,
	`event_end` TIME,
	`list_expiration` TIME,
	PRIMARY KEY (`id`)
);

CREATE TABLE `shortlink` (
	`id` INT NOT NULL,
	`link` varchar(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `list_status` (
	`id` INT NOT NULL,
	`status` varchar(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `guest_fields` (
	`id` INT NOT NULL,
	`organization` INT NOT NULL,
	`name` varchar(255) NOT NULL,
	`description` varchar(255) NOT NULL,
	`type` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `guest_field_type` (
	`id` INT NOT NULL,
	`type` varchar(255) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `shortlink_list` (
	`id` INT NOT NULL,
	`link_id` INT NOT NULL,
	`list` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `shortlink_user` (
	`id` INT NOT NULL,
	`link` INT NOT NULL,
	`user` INT NOT NULL,
	PRIMARY KEY (`id`)
);

ALTER TABLE `events` ADD CONSTRAINT `events_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `events` ADD CONSTRAINT `events_fk1` FOREIGN KEY (`venue`) REFERENCES `venues`(`id`);

ALTER TABLE `venues` ADD CONSTRAINT `venues_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `users` ADD CONSTRAINT `users_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `users` ADD CONSTRAINT `users_fk1` FOREIGN KEY (`groups`) REFERENCES `groups`(`id`);

ALTER TABLE `groups` ADD CONSTRAINT `groups_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `groups` ADD CONSTRAINT `groups_fk1` FOREIGN KEY (`administrators`) REFERENCES `users`(`id`);

ALTER TABLE `lists` ADD CONSTRAINT `lists_fk0` FOREIGN KEY (`event`) REFERENCES `events`(`id`);

ALTER TABLE `lists` ADD CONSTRAINT `lists_fk1` FOREIGN KEY (`age`) REFERENCES `ages`(`id`);

ALTER TABLE `lists` ADD CONSTRAINT `lists_fk2` FOREIGN KEY (`price`) REFERENCES `prices`(`id`);

ALTER TABLE `lists` ADD CONSTRAINT `lists_fk3` FOREIGN KEY (`users`) REFERENCES `users`(`id`);

ALTER TABLE `lists` ADD CONSTRAINT `lists_fk4` FOREIGN KEY (`groups`) REFERENCES `groups`(`id`);

ALTER TABLE `lists` ADD CONSTRAINT `lists_fk5` FOREIGN KEY (`status`) REFERENCES `list_status`(`id`);

ALTER TABLE `lists` ADD CONSTRAINT `lists_fk6` FOREIGN KEY (`gender`) REFERENCES `genders`(`id`);

ALTER TABLE `prices` ADD CONSTRAINT `prices_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `ages` ADD CONSTRAINT `ages_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `guests` ADD CONSTRAINT `guests_fk0` FOREIGN KEY (`list`) REFERENCES `lists`(`id`);

ALTER TABLE `guests` ADD CONSTRAINT `guests_fk1` FOREIGN KEY (`age`) REFERENCES `ages`(`id`);

ALTER TABLE `guests` ADD CONSTRAINT `guests_fk2` FOREIGN KEY (`gender`) REFERENCES `genders`(`id`);

ALTER TABLE `guests` ADD CONSTRAINT `guests_fk3` FOREIGN KEY (`user`) REFERENCES `users`(`id`);

ALTER TABLE `checkins` ADD CONSTRAINT `checkins_fk0` FOREIGN KEY (`event`) REFERENCES `events`(`id`);

ALTER TABLE `checkins` ADD CONSTRAINT `checkins_fk1` FOREIGN KEY (`list`) REFERENCES `lists`(`id`);

ALTER TABLE `checkins` ADD CONSTRAINT `checkins_fk2` FOREIGN KEY (`guest`) REFERENCES `guests`(`id`);

ALTER TABLE `settings` ADD CONSTRAINT `settings_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `guest_fields` ADD CONSTRAINT `guest_fields_fk0` FOREIGN KEY (`organization`) REFERENCES `organizations`(`id`);

ALTER TABLE `guest_fields` ADD CONSTRAINT `guest_fields_fk1` FOREIGN KEY (`type`) REFERENCES `guest_field_type`(`id`);

ALTER TABLE `shortlink_list` ADD CONSTRAINT `shortlink_list_fk0` FOREIGN KEY (`link_id`) REFERENCES `shortlink`(`id`);

ALTER TABLE `shortlink_list` ADD CONSTRAINT `shortlink_list_fk1` FOREIGN KEY (`list`) REFERENCES `lists`(`id`);

ALTER TABLE `shortlink_user` ADD CONSTRAINT `shortlink_user_fk0` FOREIGN KEY (`link`) REFERENCES `shortlink`(`id`);

ALTER TABLE `shortlink_user` ADD CONSTRAINT `shortlink_user_fk1` FOREIGN KEY (`user`) REFERENCES `users`(`id`);
