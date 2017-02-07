/* Guest List Tracker "v2" Sample Data, using "Beta Nightclub" as example */

/* organizations */
INSERT INTO `organizations`(`id`, `name`)
VALUES
	(1,'Beta Nightclub');

/* domains */
INSERT INTO `domains`(`organization`, `domain`)
VALUES
	(1,'betanightclub'),
	(1,'tech.betanightclub.com'),
	(1,'betate.ch');

/* settings */
INSERT INTO `settings`(`organization`, `timezone`, `date_format`, `domain`, `event_start`, `event_end`, `list_expiration`, `currency`)
VALUES
	(1,'America/Denver','M jS Y','2','21:00:00','02:00:00','19:00:00',147);

/* venues */
INSERT INTO `venues`(`id`, `name`, `address`, `organization`, `timezone`, `currency`)
VALUES
	(1,'Beta Nightclub','1909 Blake St. Denver, CO 80202',1,'America/Denver',147);

/* groups */
INSERT INTO `groups`(`id`, `name`, `organization`)
VALUES
	(1,'Administrators',1);

/* users */
INSERT INTO `users`(`id`, `organization`, `role`, `name_first`, `name_last`, `name_alias`, `email`, `password`, `isactive`, `dt`)
VALUES
	(2,1,2,'Justin','Ryan','Jstn Ryan','jstn@jstnryan.com',NULL,0,NULL);

/* group_members */
INSERT INTO `group_members`(`user`, `group`)
VALUES
	(2,1);

/* group_administrators */
INSERT INTO `group_administrators`(`group`, `user`)
VALUES
	(1,2);

/* events */
INSERT INTO `events`(`id`, `organization`, `name`, `venue`, `start`, `end`)
VALUES
	(1,1,'12th Planet + Lumberjvck',1,'2017-01-13 21:00:00','2017-01-14 02:00:00'),
	(2,1,'Chris Liebing',1,'2017-01-22 21:00:00','2017-01-23 02:00:00'),
	(3,1,'NYE 2018',1,'2017-12-31 21:00:00','2018-01-01 02:00:00');

/* lists */
INSERT INTO `lists`(`id`, `event`, `valid_start`, `valid_end`, `signup_valid`, `signup_limit`, `status`)
VALUES
	(1,1,NULL,NULL,1,-1,1);

/* ****************
   * stopped here *
   **************** */