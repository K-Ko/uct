REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`) VALUES
('code_admin', '{{NATIVE}}', 'month'),
('code_admin', '{{NATIVE}}', 'month3');

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'de', 'month', 'Monat', 110),
('code_set', 'de', 'month3', 'Monat (3)', 113);

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
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
('month3', 'de', 12, 'Dez');
