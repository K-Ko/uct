REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`) VALUES
('code_admin', '{{NATIVE}}', 'day'),
('code_admin', '{{NATIVE}}', 'day1'),
('code_admin', '{{NATIVE}}', 'day2'),
('code_admin', '{{NATIVE}}', 'day3');

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'de', 'day', 'Tag', 100),
('code_set', 'de', 'day1', 'Tag (1)', 101),
('code_set', 'de', 'day2', 'Tag (2)', 102),
('code_set', 'de', 'day3', 'Tag (3)', 103);

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
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
