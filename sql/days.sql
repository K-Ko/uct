-- Need active German
REPLACE INTO `uct` (`set`, `lang`, `code`, `desc`, `order`, `active`) VALUES
("code_lang", "en", "de", "German", 1, 1),
("code_lang", "de", "de", "Deutsch", 0, 0);

REPLACE INTO `uct` (`set`, `lang`, `code`) VALUES

('code_admin', 'en', 'day'),
('code_admin', 'en', 'day1'),
('code_admin', 'en', 'day2'),
('code_admin', 'en', 'day3');

REPLACE INTO `uct` (`set`, `lang`, `code`, `desc`, `order`) VALUES

('code_set', 'en', 'day', 'Day', 101),
('code_set', 'en', 'day1', 'Day (1)', 102),
('code_set', 'en', 'day2', 'Day (2)', 103),
('code_set', 'en', 'day3', 'Day (3)', 104),

('day', 'en', 0, 'Sunday', 0),
('day', 'en', 1, 'Monday', 1),
('day', 'en', 2, 'Tuesday', 2),
('day', 'en', 3, 'Wednesday', 3),
('day', 'en', 4, 'Thursday', 4),
('day', 'en', 5, 'Friday', 5),
('day', 'en', 6, 'Saturday', 6),

('day1', 'en', 0, 'S', 0),
('day1', 'en', 1, 'M', 1),
('day1', 'en', 2, 'T', 2),
('day1', 'en', 3, 'W', 3),
('day1', 'en', 4, 'T', 4),
('day1', 'en', 5, 'F', 5),
('day1', 'en', 6, 'S', 6),

('day2', 'en', 0, 'Su', 0),
('day2', 'en', 1, 'Mo', 1),
('day2', 'en', 2, 'Tu', 2),
('day2', 'en', 3, 'We', 3),
('day2', 'en', 4, 'Th', 4),
('day2', 'en', 5, 'Fr', 5),
('day2', 'en', 6, 'Sa', 6),

('day3', 'en', 0, 'Sun', 0),
('day3', 'en', 1, 'Mon', 1),
('day3', 'en', 2, 'Tue', 2),
('day3', 'en', 3, 'Wed', 3),
('day3', 'en', 4, 'Thu', 4),
('day3', 'en', 5, 'Fri', 5),
('day3', 'en', 6, 'Sat', 6);

REPLACE INTO `uct` (`set`, `lang`, `code`, `desc`) VALUES

('code_set', 'de', 'day', 'Tag'),
('code_set', 'de', 'day1', 'Tag (1)'),
('code_set', 'de', 'day2', 'Tag (2)'),
('code_set', 'de', 'day3', 'Tag (3)'),

('day', 'de', 0, 'Sonntag'),
('day', 'de', 1, 'Montag'),
('day', 'de', 2, 'Dienstag'),
('day', 'de', 3, 'Mittwoch'),
('day', 'de', 4, 'Donnerstag'),
('day', 'de', 5, 'Freitag'),
('day', 'de', 6, 'Samstag'),

('day1', 'de', 0, 'S'),
('day1', 'de', 1, 'M'),
('day1', 'de', 2, 'D'),
('day1', 'de', 3, 'M'),
('day1', 'de', 4, 'D'),
('day1', 'de', 5, 'F'),
('day1', 'de', 6, 'S'),

('day2', 'de', 0, 'So'),
('day2', 'de', 1, 'Mo'),
('day2', 'de', 2, 'Di'),
('day2', 'de', 3, 'Mi'),
('day2', 'de', 4, 'Do'),
('day2', 'de', 5, 'Fr'),
('day2', 'de', 6, 'Sa'),

('day3', 'de', 0, 'Son'),
('day3', 'de', 1, 'Mon'),
('day3', 'de', 2, 'Die'),
('day3', 'de', 3, 'Mit'),
('day3', 'de', 4, 'Don'),
('day3', 'de', 5, 'Fre'),
('day3', 'de', 6, 'Sam');

REPLACE INTO `uct` (`set`, `lang`, `code`, `desc`) VALUES

('code_set', 'fr', 'day', 'Jour'),
('code_set', 'fr', 'day1', 'Jour (1)'),
('code_set', 'fr', 'day2', 'Jour (2)'),
('code_set', 'fr', 'day3', 'Jour (3)'),

('day', 'fr', '0', 'Dimanche'),
('day', 'fr', '1', 'Lundi'),
('day', 'fr', '2', 'Mardi'),
('day', 'fr', '3', 'Mercredi'),
('day', 'fr', '4', 'Jeudi'),
('day', 'fr', '5', 'Vendredi'),
('day', 'fr', '6', 'Samedi'),

('day1', 'fr', '0', 'D'),
('day1', 'fr', '1', 'L'),
('day1', 'fr', '2', 'M'),
('day1', 'fr', '3', 'M'),
('day1', 'fr', '4', 'J'),
('day1', 'fr', '5', 'V'),
('day1', 'fr', '6', 'S'),

('day2', 'fr', '0', 'Di'),
('day2', 'fr', '1', 'Lu'),
('day2', 'fr', '2', 'Ma'),
('day2', 'fr', '3', 'Me'),
('day2', 'fr', '4', 'Je'),
('day2', 'fr', '5', 'Ve'),
('day2', 'fr', '6', 'Sa'),

('day3', 'fr', '0', 'Dim'),
('day3', 'fr', '1', 'Lun'),
('day3', 'fr', '2', 'Mar'),
('day3', 'fr', '3', 'Mer'),
('day3', 'fr', '4', 'Jeu'),
('day3', 'fr', '5', 'Ven'),
('day3', 'fr', '6', 'Sam');
