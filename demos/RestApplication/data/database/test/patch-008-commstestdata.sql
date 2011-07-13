
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE comms_mediums;
TRUNCATE TABLE comms_medium_parts;
TRUNCATE TABLE comms_templates;
TRUNCATE TABLE comms_templates_parts;




INSERT INTO comms_mediums (name, description) VALUES
('email', 'E-mail'),
('sms', 'SMS bericht');

--
-- Dumping data for table comms_medium_parts
--

INSERT INTO comms_medium_parts (id, medium, name, description) VALUES
(1, 'email', 'bodyHtml', 'Bericht (HTML)'),
(2, 'email', 'bodyText', 'Bericht (platte tekst)'),
(3, 'email', 'subject', 'Onderwerp'),
(4, 'sms', 'body', 'Bericht');

--
-- Dumping data for table comms_templates
--

INSERT INTO comms_templates (id, name, description) VALUES
(1, 'ThanksReportingDefect', 'Bedank voor opgeven LED');

--
-- Dumping data for table comms_templates_parts
--

INSERT INTO comms_templates_parts (id, template, mediumPart, contents) VALUES
(1, 1, 4, 'Bedankt voor uw aanmelding'),
(2, 1, 2, 'Beste %%firstname%% %%lastname%%,\r\n\r\nBedankt voor de aanmelding van het defect wat u op of uw woning geconstateerd heeft.\r\n\r\nMocht u verdere aanvullingen hebben kunt u altijd op %%personalUrl%% inloggen met de code: %%personalCode%%.\r\n\r\nUw woningcooperatie\r\nLaatrop Wonen'),
(3, 1, 1, '<p>Beste %%firstname%% %%lastname%%,</p>\r\n\r\n<p>Bedankt voor de aanmelding van het defect wat u op of uw woning geconstateerd heeft.</p>\r\n\r\n<p>Mocht u verdere aanvullingen hebben kunt u altijd op %%personalUrl%% inloggen met de code: %%personalCode%%.</p>\r\n\r\n<p>Uw woningcooperatie<br />\r\nLaatrop Wonen</p>'),
(4, 1, 3, 'Bedankt voor uw aanmelding');

SET FOREIGN_KEY_CHECKS = 1;