-- Need active German
REPLACE INTO `uct` (`set`, `lang`, `code`, `desc`, `order`, `active`) VALUES
("code_lang", "en", "de", "German", 1, 1),
("code_lang", "de", "de", "Deutsch", 0, 0);

REPLACE INTO `uct` (`set`, `lang`, `code`) VALUES
('code_admin', 'en', 'month'),
('code_admin', 'en', 'month3');

REPLACE INTO `uct` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'en', 'month', 'Month', 111),

('month', 'en', 1, 'January', 1),
('month', 'en', 2, 'February', 2),
('month', 'en', 3, 'March', 3),
('month', 'en', 4, 'April', 4),
('month', 'en', 5, 'May', 5),
('month', 'en', 6, 'June', 6),
('month', 'en', 7, 'July', 7),
('month', 'en', 8, 'August', 8),
('month', 'en', 9, 'September', 9),
('month', 'en', 10, 'October', 10),
('month', 'en', 11, 'November', 11),
('month', 'en', 12, 'December', 12),

('code_set', 'en', 'month3', 'Month (3)', 112),

('month3', 'en', 1, 'Jan', 1),
('month3', 'en', 2, 'Feb', 2),
('month3', 'en', 3, 'Mar', 3),
('month3', 'en', 4, 'Apr', 4),
('month3', 'en', 5, 'May', 5),
('month3', 'en', 6, 'Jun', 6),
('month3', 'en', 7, 'Jul', 7),
('month3', 'en', 8, 'Aug', 8),
('month3', 'en', 9, 'Sep', 9),
('month3', 'en', 10, 'Oct', 10),
('month3', 'en', 11, 'Nov', 11),
('month3', 'en', 12, 'Dec', 12);

REPLACE INTO `uct` (`set`, `lang`, `code`, `desc`) VALUES

('code_set', 'de', 'month', 'Monat'),

('month', 'de', 1, 'Januar'),
('month', 'de', 2, 'Februar'),
('month', 'de', 3, 'März'),
('month', 'de', 4, 'April'),
('month', 'de', 5, 'Mai'),
('month', 'de', 6, 'Juni'),
('month', 'de', 7, 'Juli'),
('month', 'de', 8, 'August'),
('month', 'de', 9, 'September'),
('month', 'de', 10, 'Oktober'),
('month', 'de', 11, 'November'),
('month', 'de', 12, 'Dezember'),

('code_set', 'de', 'month3', 'Monat (3)'),

('month3', 'de', 1, 'Jan'),
('month3', 'de', 2, 'Feb'),
('month3', 'de', 3, 'Mär'),
('month3', 'de', 4, 'Apr'),
('month3', 'de', 5, 'Mai'),
('month3', 'de', 6, 'Jun'),
('month3', 'de', 7, 'Jul'),
('month3', 'de', 8, 'Aug'),
('month3', 'de', 9, 'Sep'),
('month3', 'de', 10, 'Okt'),
('month3', 'de', 11, 'Nov'),
('month3', 'de', 12, 'Dez'),

('code_set', 'fr', 'month', 'Mois'),

('month', 'fr', '1', 'Janvier'),
('month', 'fr', '10', 'Octobre'),
('month', 'fr', '11', 'Novembre'),
('month', 'fr', '12', 'Décembre'),
('month', 'fr', '2', 'Février'),
('month', 'fr', '3', 'Mars'),
('month', 'fr', '4', 'Avril'),
('month', 'fr', '5', 'Mai'),
('month', 'fr', '6', 'Juin'),
('month', 'fr', '7', 'Juillet'),
('month', 'fr', '8', 'Août'),
('month', 'fr', '9', 'Septembre'),

('code_set', 'fr', 'month3', 'Mois (3)'),

('month3', 'fr', '1', 'Jan'),
('month3', 'fr', '10', 'Oct'),
('month3', 'fr', '11', 'Nov'),
('month3', 'fr', '12', 'Dec'),
('month3', 'fr', '2', 'Fev'),
('month3', 'fr', '3', 'Mar'),
('month3', 'fr', '4', 'Avr'),
('month3', 'fr', '5', 'Mai'),
('month3', 'fr', '6', 'Jui'),
('month3', 'fr', '7', 'Jul'),
('month3', 'fr', '8', 'Aou'),
('month3', 'fr', '9', 'Sep');
