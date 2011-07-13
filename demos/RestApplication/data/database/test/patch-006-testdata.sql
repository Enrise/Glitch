SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

TRUNCATE TABLE rep_codes;
TRUNCATE TABLE rep_observers;
TRUNCATE TABLE rep_photos;
TRUNCATE TABLE rep_repairs;
TRUNCATE TABLE rep_tasks;

INSERT INTO `rep_codes` (`code`) VALUES
('code1'),
('code2'),
('code3'),
('code4');

INSERT INTO `rep_observers` (`id`, `repair_id`, `firstname`, `lastname`, `gender`, `email`, `phone`, `email_update`, `text_update`) VALUES
(1, 1, 'Joshua', 'Thijssen', 'm', 'jthijssen@example.org', '0612345678', 1, 0),
(3, 1, 'Andy', 'Warhol', 'm', 'awarhol@example.org', '0612356783', 1, 1),
(4, 1, 'Andy', 'Andy', 'f', 'awarhol@example.org', '0612356783', 1, 1),
(5, 1, 'Andy', 'Warhol', 'm', 'awarhol@example.org', '0612356783', 1, 0),
(6, 1, 'Andy', 'Warhol', 'f', 'awarhol@example.org', '0612356783', 0, 1);


INSERT INTO `rep_repairs` (`id`, `title`, `object_uri`, `appointment_uri`, `code`, `owner_id`) VALUES
(1, 'Kleine keuken reparaties', '/objects/postalcode/1234AA/1234', NULL, 'qwerusta', 1);

INSERT INTO `rep_tasks` (`id`, `repair_id`, `appointment_uri`, `led_uri`, `title`, `remarks`, `reason`) VALUES
(1, 1, NULL, '/decision/location/keuken/element/kraan/defect/lekt', 'Keuken, Kraan, Lekt ', '', ''),
(2, 1, NULL, '/decision/location/keuken/element/keukenblok/defect/deurtje+klemt', 'Keukenkastje zit los (scharnier is kapot)', '', ''),
(5, 2, NULL, '/decision/location/keuken/element/keukenblok/defect/deurtje+klemt', 'Tweede keukenkastje zit los', '', ''),
(6, 1, NULL, '/decision/location/keuken/element/keukenblok/defect/deurtje+klemt', 'Derde keukenkastje zit los', '', ''),
(7, 1, NULL, '/decision/location/keuken/element/keukenblok/defect/deurtje+klemt', 'Vierde keukenkastje zit los', '', ''),
(8, 1, NULL, '/decision/location/keuken/element/keukenblok/defect/deurtje+klemt', 'Vijfde keukenkastje zit los', '', '');
