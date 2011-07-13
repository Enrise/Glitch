INSERT INTO comms_mediums (`name`, description) VALUES
('email', 'E-mail'),
('sms', 'SMS bericht');

INSERT INTO comms_medium_parts (id, `medium`, `name`, description) VALUES
(1, 'email', 'bodyHtml', 'Bericht (HTML)'),
(2, 'email', 'bodyText', 'Bericht (platte tekst)'),
(3, 'email', 'subject', 'Onderwerp'),
(4, 'sms', 'body', 'Bericht');

INSERT INTO comms_templates (id, description) VALUES
(1, 'Bedank voor opgeven LED');

INSERT INTO comms_templates_parts (id, template, mediumPart, contents) VALUES
(1, 1, 3, 'Bedankt voor uw aanmelding'),
(2, 1, 2, 'Beste %%firstname%% %%lastname%%,\r\n\r\nBedankt voor de aanmelding van het defect wat u op of uw woning geconstateerd heeft.\r\n\r\nMocht u verdere aanvullingen hebben kunt u altijd op %%personalUrl%% inloggen met de code: %%personalCode%%.\r\n\r\nUw woningcooperatie\r\nLaatrop Wonen'),
(3, 1, 1, '<p>Beste %%firstname%% %%lastname%%,</p>\r\n\r\n<p>Bedankt voor de aanmelding van het defect wat u op of uw woning geconstateerd heeft.</p>\r\n\r\n<p>Mocht u verdere aanvullingen hebben kunt u altijd op %%personalUrl%% inloggen met de code: %%personalCode%%.</p>\r\n\r\n<p>Uw woningcooperatie<br />\r\nLaatrop Wonen</p>');


