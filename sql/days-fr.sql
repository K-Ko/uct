REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`) VALUES
('code_admin', '{{NATIVE}}', 'day'),
('code_admin', '{{NATIVE}}', 'day1'),
('code_admin', '{{NATIVE}}', 'day2'),
('code_admin', '{{NATIVE}}', 'day3');

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'fr', 'day', 'Jour', 100),
('code_set', 'fr', 'day1', 'Jour (1)', 101),
('code_set', 'fr', 'day2', 'Jour (2)', 102),
('code_set', 'fr', 'day3', 'Jour (3)', 103);

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('day', 'fr', 0, 'Dimanche'),
('day', 'fr', 1, 'Lundi'),
('day', 'fr', 2, 'Mardi'),
('day', 'fr', 3, 'Mercredi'),
('day', 'fr', 4, 'Jeudi'),
('day', 'fr', 5, 'Vendredi'),
('day', 'fr', 6, 'Samedi'),

('day1', 'fr', 0, 'D'),
('day1', 'fr', 1, 'L'),
('day1', 'fr', 2, 'M'),
('day1', 'fr', 3, 'M'),
('day1', 'fr', 4, 'J'),
('day1', 'fr', 5, 'V'),
('day1', 'fr', 6, 'S'),

('day2', 'fr', 0, 'Di'),
('day2', 'fr', 1, 'Lu'),
('day2', 'fr', 2, 'Ma'),
('day2', 'fr', 3, 'Me'),
('day2', 'fr', 4, 'Je'),
('day2', 'fr', 5, 'Ve'),
('day2', 'fr', 6, 'Sa'),

('day3', 'fr', 0, 'Dim'),
('day3', 'fr', 1, 'Lun'),
('day3', 'fr', 2, 'Mar'),
('day3', 'fr', 3, 'Mer'),
('day3', 'fr', 4, 'Jeu'),
('day3', 'fr', 5, 'Ven'),
('day3', 'fr', 6, 'Sam');
