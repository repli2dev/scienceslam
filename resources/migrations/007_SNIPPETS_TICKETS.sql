ALTER TABLE snippet ADD COLUMN `is_plaintext` TINYINT(1) DEFAULT 0 NOT NULL;

INSERT INTO `snippet` (`key`, content, is_protected, is_plaintext, inserted) VALUES
('registration', 'Chtěl bys prezentovat svůj vlastní výzkum a vyhrát?
===Registrace===',
1, 0, NOW()),
('tickets', 'Zajímá tě, jaké to bude? Přijď!
===Vstupné dobrovolné===',
1, 0, NOW()),
('tickets-link', '',
1, 1, NOW());