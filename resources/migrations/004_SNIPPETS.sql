CREATE TABLE `snippet` (
	`snippet_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`key` varchar(255) UNIQUE,
	`content` text NOT NULL,
	`is_protected` TINYINT(1) DEFAULT 0,
	`inserted` datetime NOT NULL,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`snippet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `snippet` (`key`, content, is_protected, inserted) VALUES
('main-menu', '.[menu-ul]
- "O co jde":/show/about
- "Průběh":/show/schedule
- "Pravidla":/show/rules
- "Kdo je za tím":/show/people',
1, NOW()),
('footer1', '.[no-margin]
**Science slam** pořádá
 "Masarykova univerzita":http://www.muni.cz/',
1, NOW()),
('footer2', '/---html
<a href="http://www.youtube.com/user/ScienceSlamMUNI" target="_blank" title="Sledujte nás na YouTube">
    <img class="footer-social" src="/images/design/youtube.png" alt="YouTube" />
</a>
<a href="http://www.facebook.com/ScienceSlamMUNI" target="_blank" title="Sledujte nás na Facebooku">
    <img class="footer-social" src="/images/design/facebook.png" alt="Facebook" />
</a>
<a href="mailto:scienceslam@muni.cz" title="Napište nám">
    <img class="footer-social" src="/images/design/email.png" alt="scienceslam@muni.cz" />
</a>

<div class="cleaner">&nbsp;</div>

&copy; 2013 <a href="http://www.muni.cz">Masarykova univerzita</a>
\\---',
1, NOW()),
('header1', '.[no-margin]
**Science slam**
 výzkum v jiném světle',
1, NOW());