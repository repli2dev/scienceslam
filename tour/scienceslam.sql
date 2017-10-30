-- phpMyAdmin SQL Dump
-- version 4.4.15.8
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 29, 2017 at 09:59 PM
-- Server version: 10.0.23-MariaDB
-- PHP Version: 5.5.34-pl0-gentoo

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scienceslamtour`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(10) unsigned NOT NULL,
  `datetime` datetime NOT NULL,
  `email` varchar(256) COLLATE utf8_czech_ci NOT NULL,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  `newsletter` tinyint(1) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `datetime`, `email`, `text`, `newsletter`) VALUES
(1, '2017-10-29 21:33:13', 'test@test.com', 'Dobrý den,\r\nmám zájem o vaši prezentaci na naší škole, prosím kontaktujte mne pro více informací.\r\nDěkuji. XY', 1);

-- --------------------------------------------------------

--
-- Table structure for table `map_locations`
--

CREATE TABLE IF NOT EXISTS `map_locations` (
  `id` int(10) unsigned NOT NULL,
  `location_name` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `x_coor` int(11) NOT NULL,
  `y_coor` int(11) NOT NULL,
  `label_pos` varchar(16) COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `map_locations`
--

INSERT INTO `map_locations` (`id`, `location_name`, `x_coor`, `y_coor`, `label_pos`) VALUES
(1, 'Brno', 510, 243, 'left'),
(2, 'Praha', 275, 95, 'right'),
(3, 'Rájec-Jestřebí', 520, 185, 'left'),
(4, 'Kyjov', 575, 248, 'right'),
(5, 'Opava', 640, 100, 'left'),
(6, 'Kroměříž', 600, 198, 'top'),
(7, 'Valašské Meziříčí', 670, 170, 'right'),
(8, 'Svitavy', 500, 132, 'left');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE IF NOT EXISTS `sections` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `tooltip` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  `priority` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `tooltip`, `priority`) VALUES
(1, 'Hlavička', 'Hlavička stránky s menu, velkým logem a claimem.', 9),
(2, 'Sekce Science slam tour', 'První sekce na webu, nachází se zde mapa vystoupení.', 8),
(3, 'Co je Science slam?', 'Sekce aktualit.', 7),
(4, 'Startujeme!', 'Sekce s videem.', 6),
(5, 'Chci Science slam ve škole', 'Kontaktní formulář.', 5),
(6, 'Jak to proběhne', '', 4),
(7, 'Více o Science slamu', '', 3),
(8, 'Patička', '', 2),
(9, 'Popup', 'Popup, který se zobrazí po vyplnění kontaktního formuláře', 1);

-- --------------------------------------------------------

--
-- Table structure for table `texts`
--

CREATE TABLE IF NOT EXISTS `texts` (
  `id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `name` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `tooltip` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  `is_wysiwyg` tinyint(1) NOT NULL,
  `priority` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `texts`
--

INSERT INTO `texts` (`id`, `section_id`, `name`, `tooltip`, `text`, `is_wysiwyg`, `priority`) VALUES
(1, 1, 'Claim', 'Claim hlavičky stránky, původní "Velká věda začíná malými citoslovci"', '<p><strong>Velká věda</strong> začíná malými citoslovci</p>', 1, 1),
(2, 2, 'Nadpis', '', '<p>Science slam tour</p>', 1, 6),
(3, 2, 'Úvodní odstavec', '', '<p><strong>Věda a poznání je to, co každou společnost rozvíjí. A byla by sakra škoda tohle neříct svým žákům, nemyslíte? Science slam je vědecko-popularizační projekt, jehož cílem je ukázat široké veřejnosti, že věda je zajímavá a zábavná a že stát se vědcem a bádat na plný úvazek stojí za to.</strong></p>', 1, 5),
(4, 2, 'Text', '', '<p>Před časem jsme se rozhodli, že pokud chceme mladé přivést k vědě, musíme za nimi. Musíme zpátky na střední. Proto vzniklo celorepublikové turné po středních školách, které nás postupně zavedlo do Opavy, do Kroměříže, do Kyjova, do Brna nebo třeba do Karlových Varů.</p><p>A možná nás zavede i k vám. Pokud chcete svým studentům předat kus nadšení pro vědu, rádi vám pomůžeme. Přijedeme k vám s řečníky a veškerou aparaturou a uspořádáme Science slam přímo u vás. Vše je zdarma, nic neplatíte.</p>', 1, 4),
(5, 2, 'Claim', '', '<p><strong>SCIENCE SLAM </strong>TOUR</p>', 1, 3),
(6, 2, 'Odkaz URL', '', 'https://scienceslam.muni.cz/', 0, 2),
(7, 3, 'Nadpis', '', '<p>Co je Science slam?</p>', 1, 8),
(8, 3, 'Text žlutý', '', '<p><strong>Science slam je vědecko-popularizační show. Každého slamu se účastní několik řečníků (zpravidla 6), kteří mají za úkol v relativně krátkém čase (6 minut) představit svůj výzkum. Jejich řeč musí být zajímavá, poutavá, zábavná. Nesmí používat žádnou formu elektronické prezentace, jen rekvizity nebo ruce a nohy. Na konci slamu publikum hlasuje, kdo zaujal nejvíc.</strong></p><p>Cílem projektu je zábavnou formou ukázat, na čem dnes dělají vědci. Dalším cílem je vzbudit ve studentech zájem o vědu a případně univerzitní studium. A konečně - Science slam je skvělý způsob, jak se dozvědět víc o univerzitním prostředí i studiu samotném.</p>', 1, 6),
(9, 3, 'Odkaz žlutý URL', '', 'https://scienceslam.muni.cz/show/about', 0, 5),
(10, 3, 'Text modrý', '', '<p>Možná vás zajímá, kdo za Science slam vlastně stojí. Jsou to studenti z Masarykovy univerzity a z Univerzity Karlovy. Jsou to mladí lidé, vesměs studenti. &nbsp;Máme mezi sebou přírodovědce, religionistku i informatika. Všichni uvnitř cítíme, že je potřeba předat kus nadšení pro vědu dál.</p>', 1, 3),
(11, 3, 'Odkaz modrý URL', '', 'https://scienceslam.muni.cz/show/people', 0, 2),
(12, 4, 'Nadpis', '', '<p>Startujeme!</p>', 1, 7),
(13, 4, 'Úvodní odstavec', '', '<p><strong>Slamovou tour jsme začali na Klasickém a Španělském gymnáziu v Brně-Bystrci. Čekalo nás přibližně 40 žáků z posledních ročníků. Byli jsme příjemně překvapeni, že se akce zúčastnili v tak velkém počtu, i když se konala nad rámec vyučování.</strong></p>', 1, 6),
(14, 4, 'Video Youtube ID', '', 'iTwEW-DqQHI', 0, 5),
(15, 4, 'Text', '', '<p>Za slam vystoupili čtyři vědci z oborů teoretická fyzika, molekulární ekologie, mezinárodní vztahy a anglická výslovnost. Studenti si poslechli krátké projevy a zkusili si odpovědět na záludné otázky, kdy zjišťovali, že nemusí jen jedna odpověď být ta správná.&nbsp; Aktivně se zapojovali během celého programu a debatovali jak s hosty, tak mezi sebou. Na konci se setkali s řečníky v menších skupinkách a doptávali se na výzkum, nebo obecně na život vysokoškoláka. Největší úspěch měl v tomto případě řečník z oboru mezinárodní vztahy. Sami žáci nám řekli, že je slam bavil a ocenili osobní kontakt s lidmi, kteří se vědou zabývají a mluví o ní jednoduše a zábavně.</p>', 1, 4),
(16, 4, 'Claim', '', '<p><strong>STAR-TUJEME</strong></p>', 1, 3),
(17, 4, 'Odkaz URL', '', 'https://scienceslam.muni.cz/', 0, 2),
(18, 5, 'Nadpis', '', '<p><strong>Chci Science slam</strong> ve škole</p>', 1, 2),
(19, 5, 'Text placeholder', '', '<p>Dobrý den,</p><p>mám zájem o vaši prezentaci na naší škole, prosím kontaktujte mne pro více informací.</p><p>Děkuji. XY</p>', 1, 1),
(20, 6, 'Nadpis', '', '<p>Jak to proběhne</p>', 1, 6),
(21, 6, 'Úvodní odstavec', '', '<p><strong>Ráno nebo ve stanovený čas přijedeme na vaši školu. S sebou přivezeme nejen účinkující a řečníky, ale i merchandise a drobné dárky pro vaše studenty. Celá show má zhruba dvě hodiny a rádi ji za den sjedeme několikrát.</strong></p>', 1, 5),
(22, 6, 'Text', '', '<p>Celé vystoupení je rozdělené na dvě části. V první části proběhne několik 7 minutových minishow, &nbsp;v rámci kterých každý řečník zábavnou formou představí, na čem pracuje. Používat PowerPoint se nesmí, všichni si musí vystačit s tím, co mají po ruce. Na konci této části vaši žáci hodnotí, komu se to povedlo nejvíc.</p><p>V druhé části se studenti řečníků ptají na to, co je zajímá. O vědecké práci, o studiu, o škole. Nebojte se, i tahle část je částečně moderovaná, takže řeč nestojí. Naopak. Často je dotazů tolik, že na některé se nedostane.</p><p>Science slam se nejdříve konal v malém sále brněnského Kabinetu Múz, později v kině Scala. Jsme tak schopní ho odehrát ve třídě i na velkém pódiu, a to i několikrát denně. Záleží na domluvě s vedením školy. Jak jsme psali výše, jako škola nic neplatíte.</p>', 1, 4),
(23, 6, 'Claim', '', '<p>JAK TO <strong>PRO-BĚHNE</strong></p>', 1, 3),
(24, 6, 'Odkaz URL', '', '#chci-science-slam', 0, 2),
(25, 7, 'Nadpis', '', '<p>VÍCE O <strong>SCIENCE SLAMU ZDE</strong></p>', 1, 4),
(26, 7, 'Úvodní odstavec', '', '<p><strong>Minulé ročníky, rozhovory, fotografie a další důležité informace.</strong></p>', 1, 3),
(27, 7, 'Odkaz URL', '', 'https://scienceslam.muni.cz/', 0, 2),
(28, 8, 'Text nalevo', '', '<p>Science slam pořádá</p><p>Masarykova univerzita</p>', 1, 6),
(29, 8, 'Text uprostřed', '', '<p>2017 Masarykova univerzita</p>', 1, 5),
(30, 8, 'Email', '', 'mailto:scienceslam@muni.cz', 0, 4),
(31, 8, 'Facebook URL', '', 'http://www.facebook.com/ScienceSlamMUNI', 0, 3),
(32, 8, 'Youtube URL', '', 'http://www.youtube.com/user/ScienceSlamMUNI', 0, 2),
(33, 9, 'Text OK', 'Text popupu po úspěšném odeslání kontaktního formuláře', '<p>Děkujeme za vyplnění formuláře, v nejbližší době budeme vás a vaši školu kontaktovat.</p>', 1, 3),
(34, 2, 'Odkaz text', '', 'Více o Science slam', 0, 1),
(35, 4, 'Odkaz text', '', 'Více', 0, 1),
(36, 6, 'Odkaz text', '', 'Napište nám', 0, 1),
(37, 7, 'Odkaz text', '', 'Zjistit více', 0, 1),
(38, 3, 'Odkaz žlutý text', '', 'Více', 0, 4),
(39, 3, 'Odkaz modrý text', '', 'Více o nás', 0, 1),
(40, 8, 'Instagram URL', '', 'https://www.instagram.com/scienceslam.muni/', 0, 1),
(41, 9, 'Text chyba', 'Text popupu při chybě během odeslání kontaktního formuláře', '<p>Při odesílání formuláře došlo k chybě.</p>', 1, 2),
(42, 9, 'Captcha je povinná', '', '<p>Před odesláním musíte potvrdit captchu.</p>', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `username` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(128) COLLATE utf8_czech_ci NOT NULL,
  `description` varchar(512) COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `description`) VALUES
(1, 'admin', '$2y$10$MR2qkXQx5XILRM.XBC7cbOtREf5gSIIKBrvjgNObsX/IP9d.3Bju2', 'The one and only...');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `map_locations`
--
ALTER TABLE `map_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `texts`
--
ALTER TABLE `texts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `map_locations`
--
ALTER TABLE `map_locations`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `texts`
--
ALTER TABLE `texts`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
