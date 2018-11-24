REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`) VALUES
('code_admin', '{{NATIVE}}', 'month'),
('code_admin', '{{NATIVE}}', 'month3');

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'fr', 'month', 'Mois', 110),
('code_set', 'fr', 'month3', 'Mois (3)', 113);

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('month', 'fr', 1, 'Janvier'),
('month', 'fr', 2, 'Février'),
('month', 'fr', 3, 'Mars'),
('month', 'fr', 4, 'Avril'),
('month', 'fr', 5, 'Mai'),
('month', 'fr', 6, 'Juin'),
('month', 'fr', 7, 'Juillet'),
('month', 'fr', 8, 'Août'),
('month', 'fr', 9, 'Septembre'),
('month', 'fr', 10, 'Octobre'),
('month', 'fr', 11, 'Novembre'),
('month', 'fr', 12, 'Décembre'),

('month3', 'fr', 1, 'Jan'),
('month3', 'fr', 2, 'Fev'),
('month3', 'fr', 3, 'Mar'),
('month3', 'fr', 4, 'Avr'),
('month3', 'fr', 5, 'Mai'),
('month3', 'fr', 6, 'Jui'),
('month3', 'fr', 7, 'Jul'),
('month3', 'fr', 8, 'Aou'),
('month3', 'fr', 9, 'Sep'),
('month3', 'fr', 10, 'Oct'),
('month3', 'fr', 11, 'Nov'),
('month3', 'fr', 12, 'Dec');
