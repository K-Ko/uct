REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`) VALUES
('code_admin', '{{NATIVE}}', 'day'),
('code_admin', '{{NATIVE}}', 'day1'),
('code_admin', '{{NATIVE}}', 'day2'),
('code_admin', '{{NATIVE}}', 'day3');

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'en', 'day', 'Day', 100),
('code_set', 'en', 'day1', 'Day (1)', 101),
('code_set', 'en', 'day2', 'Day (2)', 102),
('code_set', 'en', 'day3', 'Day (3)', 103);

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('day', 'en', 0, 'Sunday'),
('day', 'en', 1, 'Monday'),
('day', 'en', 2, 'Tuesday'),
('day', 'en', 3, 'Wednesday'),
('day', 'en', 4, 'Thursday'),
('day', 'en', 5, 'Friday'),
('day', 'en', 6, 'Saturday'),

('day1', 'en', 0, 'S'),
('day1', 'en', 1, 'M'),
('day1', 'en', 2, 'T'),
('day1', 'en', 3, 'W'),
('day1', 'en', 4, 'T'),
('day1', 'en', 5, 'F'),
('day1', 'en', 6, 'S'),

('day2', 'en', 0, 'Su'),
('day2', 'en', 1, 'Mo'),
('day2', 'en', 2, 'Tu'),
('day2', 'en', 3, 'We'),
('day2', 'en', 4, 'Th'),
('day2', 'en', 5, 'Fr'),
('day2', 'en', 6, 'Sa'),

('day3', 'en', 0, 'Sun'),
('day3', 'en', 1, 'Mon'),
('day3', 'en', 2, 'Tue'),
('day3', 'en', 3, 'Wed'),
('day3', 'en', 4, 'Thu'),
('day3', 'en', 5, 'Fri'),
('day3', 'en', 6, 'Sat');
