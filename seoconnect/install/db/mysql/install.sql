CREATE TABLE `b_seoconnect_anchors` (
   `ID` int(11) NOT NULL auto_increment,
   `URL` text,
   `ANCHOR` text,
   `WEIGHT` int(11) DEFAULT '100',
   `ACTIVE` varchar(1) DEFAULT 'N',
   PRIMARY KEY (`ID`)
);

INSERT INTO `b_seoconnect_anchors` (`ID`, `URL`, `ANCHOR`, `WEIGHT`, `ACTIVE`) VALUES
(1, '/Catalog/325/ventilyaciya-kondicionirovanie/648/klapany-protivopozharnye/', 'Клапаны противопожарные', 100, 'N'),
(2, '/Catalog/1366/vyvoz-musora-i-utilizaciya/1367/vyvoz-promyshlennyx-i-bytovyx-otxodov/', 'Вывоз промышленных и  бытовых отходов', 100, 'Y'),
(3, '/Catalog/356/oborudovanie-dlya-proizvodstva-stroitelnyx-materialov/1085/oborudovanie-dlya-proizvodstva-stenovyx-materialov/', 'Оборудование для производства стеновых материалов', 100, 'N'),
(4, '/Catalog/363/pilomaterialy/616/brus-strogannyj-pogonazh/', 'профилированный брус цена', 100, 'Y'),
(5, '/Catalog/363/pilomaterialy/', 'гост пиломатериалы', 100, 'Y'),
(7, '/Catalog/', 'Каталог товаров и услуг', 100, 'Y'),
(8, '/company-catalog/map/', 'Стройматериалы на карте Москвы и России', 100, 'Y'),
(10, '/Catalog/325/ventilyaciya-kondicionirovanie/643/chillery/', 'Чиллеры', 100, 'Y');
                                       

CREATE TABLE `b_seoconnect_titles` (
  `ID` int(11) NOT NULL auto_increment,
  `TITLE` varchar(256),
  `ACTIVE` varchar(1) DEFAULT 'N',
   PRIMARY KEY (`ID`)

);

INSERT INTO `b_seoconnect_titles` (`ID`, `TITLE`, `ACTIVE`) VALUES
(1, 'На нашем портале ищут', 'Y'),
(2, 'Предлагаем посетить страницы', 'Y'),
(3, 'Наши пользователи так же искали', 'Y'),
(4, 'Хотите узнать о строительстве больше, смотрите', 'Y'),
(5, 'Похожие страницы', 'Y'),
(6, 'Вам будет так же интересно', 'Y'),
(7, 'Интересные ссылки', 'Y');



CREATE TABLE `b_seoconnect_pages` (
  `ID` int(11) NOT NULL auto_increment,
  `PAGE` varchar(256),
  `TITLE` int(11),
   PRIMARY KEY (`ID`)
);



CREATE TABLE `b_seoconnect_pages_anchors` (
  `PAGE` int(11),
  `ANCHOR` int(11),
  INDEX ix_page_anchor (PAGE, ANCHOR)
);