CREATE TABLE `b_seoconnect_anchors` (
   `ID` int(11) NOT NULL auto_increment,
   `URL` text,
   `ANCHOR` text,
   `WEIGHT` int(11) DEFAULT '100',
   `ACTIVE` varchar(1) DEFAULT 'N',
   PRIMARY KEY (`ID`)
);

INSERT INTO `b_seoconnect_anchors` (`ID`, `URL`, `ANCHOR`, `WEIGHT`, `ACTIVE`) VALUES
(1, '/Catalog/325/ventilyaciya-kondicionirovanie/648/klapany-protivopozharnye/', '������� ���������������', 100, 'N'),
(2, '/Catalog/1366/vyvoz-musora-i-utilizaciya/1367/vyvoz-promyshlennyx-i-bytovyx-otxodov/', '����� ������������ �  ������� �������', 100, 'Y'),
(3, '/Catalog/356/oborudovanie-dlya-proizvodstva-stroitelnyx-materialov/1085/oborudovanie-dlya-proizvodstva-stenovyx-materialov/', '������������ ��� ������������ �������� ����������', 100, 'N'),
(4, '/Catalog/363/pilomaterialy/616/brus-strogannyj-pogonazh/', '��������������� ���� ����', 100, 'Y'),
(5, '/Catalog/363/pilomaterialy/', '���� �������������', 100, 'Y'),
(7, '/Catalog/', '������� ������� � �����', 100, 'Y'),
(8, '/company-catalog/map/', '�������������� �� ����� ������ � ������', 100, 'Y'),
(10, '/Catalog/325/ventilyaciya-kondicionirovanie/643/chillery/', '�������', 100, 'Y');
                                       

CREATE TABLE `b_seoconnect_titles` (
  `ID` int(11) NOT NULL auto_increment,
  `TITLE` varchar(256),
  `ACTIVE` varchar(1) DEFAULT 'N',
   PRIMARY KEY (`ID`)

);

INSERT INTO `b_seoconnect_titles` (`ID`, `TITLE`, `ACTIVE`) VALUES
(1, '�� ����� ������� ����', 'Y'),
(2, '���������� �������� ��������', 'Y'),
(3, '���� ������������ ��� �� ������', 'Y'),
(4, '������ ������ � ������������� ������, ��������', 'Y'),
(5, '������� ��������', 'Y'),
(6, '��� ����� ��� �� ���������', 'Y'),
(7, '���������� ������', 'Y');



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