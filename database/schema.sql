-- Criminal Minds Blog - Database Schema
-- Posts table

CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    author VARCHAR(100) NOT NULL DEFAULT 'Onbekend',
    date DATETIME NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    featured BOOLEAN NOT NULL DEFAULT FALSE,
    INDEX idx_status (status),
    INDEX idx_date (date),
    INDEX idx_featured (featured)
);

-- Insert sample data
INSERT INTO posts (id, title, content, status, author, date, created_at, updated_at, featured) VALUES
(1, 'L. van der W. (17) gepakt in Vogelwaarde tijdens het kiefen', 'Jo maat, er is zoeen cooked ass guy opgepakt in Vogelwaarde tijdens een grote deal.', 'published', 'Reklezz', '2025-09-08 12:48:02', '2025-09-08 12:48:02', NULL, TRUE),
(2, 'BREAKING: 100 kilo 6mmc gesmokkeld uit Deventer', 'goedemiddag beste mensen, blogger sjoak hier. 

er is afgelopen zondag 100 kilo 6mmc gesmokkeld uut Deevntr, ik vermoed dat het mien neef was maar ik kan het beter niet doorvertellen


wat een kearl', 'published', 'blogger_sjoak1993', '2025-09-08 09:45:50', '2025-09-08 09:45:50', NULL, FALSE),
(3, 'Grote Drugsbust in Amsterdam: 500kg Cocaïne Onderschept', '<p>In een grootschalige operatie heeft de politie vandaag 500 kilogram cocaïne onderschept in de haven van Amsterdam. De drugs waren verstopt in een container met bananen uit Zuid-Amerika.</p>

<p>De operatie, codenaam "Witte Sneeuw", was het resultaat van maanden onderzoek door de Nationale Politie in samenwerking met internationale partners. Drie verdachten zijn aangehouden.</p>

<p><strong>Details van de operatie:</strong></p>
<ul>
<li>Straatwaarde: €37,5 miljoen</li>
<li>Container herkomst: Colombia</li>
<li>Arrestaties: 3 personen</li>
<li>Onderzoeksduur: 8 maanden</li>
</ul>

<p>Dit is een van de grootste drugsvangsten van dit jaar in Nederland. De politie verwacht dat deze inbeslagname een significante impact zal hebben op de lokale drugshandel.</p>', 'published', 'Ronald Hogerlanden', '2025-09-08 07:05:29', '2025-09-08 07:05:29', NULL, FALSE);