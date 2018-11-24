REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`) VALUES
('code_admin', '{{NATIVE}}', 'month'),
('code_admin', '{{NATIVE}}', 'month3');

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`, `order`) VALUES
('code_set', 'en', 'month', 'Month', 110),
('code_set', 'en', 'month3', 'Month (3)', 113);

REPLACE INTO `{{TABLE}}` (`set`, `lang`, `code`, `desc`) VALUES
('month', 'en', 1, 'January'),
('month', 'en', 2, 'February'),
('month', 'en', 3, 'March'),
('month', 'en', 4, 'April'),
('month', 'en', 5, 'May'),
('month', 'en', 6, 'June'),
('month', 'en', 7, 'July'),
('month', 'en', 8, 'August'),
('month', 'en', 9, 'September'),
('month', 'en', 10, 'October'),
('month', 'en', 11, 'November'),
('month', 'en', 12, 'December'),

('month3', 'en', 1, 'Jan'),
('month3', 'en', 2, 'Feb'),
('month3', 'en', 3, 'Mar'),
('month3', 'en', 4, 'Apr'),
('month3', 'en', 5, 'May'),
('month3', 'en', 6, 'Jun'),
('month3', 'en', 7, 'Jul'),
('month3', 'en', 8, 'Aug'),
('month3', 'en', 9, 'Sep'),
('month3', 'en', 10, 'Oct'),
('month3', 'en', 11, 'Nov'),
('month3', 'en', 12, 'Dec');
