[ GERMAN VERSION BELOW ]

---
# Introduction
This tool was developed as part of a doctoral thesis that aimed to record and examine the interactions between members of the German Bundestag and citizens on Twitter.

# Functions 
The tool has the following functions:
* Import of Twitter account names based on a contact list with detail pages in HTML format (e.g. list of members of the German Bundestag on www.bundestag.de)
* Recording and regular updates of Twitter account information (e.g. description, number of followers and favorites)
* Reading and saving the tweets of the timelines of the Twitter accounts to be captured
* Reading and saving the current conversations on the Twitter accounts to be captured
* Evaluation function based on Twitter account and time period
* Includes emojis and related media

# Special features
* Access to the tweets via the Twitter API
* Consideration of the rate limits specified by Twitter
* Pagination with since_id and max_id through the tweets for high performance
* Logging function
* Automatic creation of the necessary database structure

# Technical requirements
* Apache 2 (or comparable)
* PHP >= 7.2
* MySQL >= 5.7 
* j7mbo/twitter-api-php

# Further requirements
* Parts of the tool have to be created as cronjobs via the CLI, therefore access via shell / bash is necessary.
* Furthermore, a Twitter Developer account as well as corresponding app access data (oauth_access_token, oauth_access_token_secret, consumer_key, consumer_secret) are required to access the Twitter API.

# Description
The tool has been developed object-based and has corresponding models and repositories through which data access is handled.
It consists of the following core elements:

## Importer (importer.php)
The Importer is executed via the CLI and is used to search a given contact list in the form of a website for links to Twitter accounts.
The URL to a contact list in HTML format, which in turn contains links to the detail pages, is therefore passed here. 
In the concrete case of application, the list of members of the German Bundestag was used as available on www.bundestag.de.

The importer then searches the given contact list for links to detail pages based on a definable pattern. On the detail pages the importer then searches for links to Twitter accounts and imports the found Twitter account-names into the database.

In order to keep the load on the target server low and to avoid a blocking of the IP, the links to the detail pages are processed bit by bit.
For this reason it is necessary to set up a cronjob.

The Importer accepts the following parameters:
* **url**: URL to the contact list as HTML page (default: `https://www.bundestag.de/ajax/filterlist/de/abgeordnete/525246-525246/h_e3c112579919ef960d06dbb9d0d44b67?limit=9999&view=BTBiographyList`)
* **baseUrl**: Base URL of the contact list and the detail pages (default: `https://www.bundestag.de`)
* **regExpDetailLinks**: Regular expression for extracting the links to the detail pages (default: `#<a[^>]+href="(/delegates/biographies/[^"]+)"[^>]+>#`),
* **regExpTwitterLinks**: Regular expression for extracting the links to the Twitter accounts on the detail pages (default: `#<a[^>]+href="(https://(www.)?twitter.com/[^"]+)"[^>]+>#`)
* **maxLinksLimit**: Maximum number of detail links to be processed per call (default: `10`)
* **checkInterval**: Interval in which the contact list is checked again, in seconds (default: `604800` = 1 week)

Example using the CLI:
```
php7.2 /var/www/diss/cli/importer.php 'https://www.bundestag.de/ajax/filterlist/de/abgeordnete/525246-525246/h_e3c112579919ef960d06dbb9d0d44b67?limit=9999&view=BTBiographyList' 'https://www.bundestag.de' '#<a[^>]+href="(/members/biographies/[^"]+)"[^>]+>#' '#<a[^>]+href="(https://(www.)?twitter.com/[^"]+)"[^>]+>#'
```

Example for the setup of a cronjob: 
```
# m h dom mon mon dow command
*/5 * * * * * /usr/bin/php7.2 /var/www/diss/cli/importer.php 'https://www.bundestag.de/ajax/filterlist/de/abgeordnete/525246-525246/h_e3c112579919ef960d06dbb9d0d44b67?limit=9999&view=BTBiographyList' 'https://www.bundestag.de' '#<a[^>]+href="(/members/biographies/[^"]+)"[^>]+>#' '#<a[^>]+href="(https://(www.)?twitter.com/[^"]+)"[^>]+>#' > /dev/null
```

## Fetcher (fetcher.php)
The fetcher is executed via the CLI and performs three core tasks:
* Reading and saving of the tweets from the timelines of the Twitter accounts into the database
* Updating the information on the Twitter accounts  (e.g. description, number of followers and favorites)
* Reading  and saving conversations with other users on the Twitter accounts into the database

In order to be able to take the rate limits of Twitter into account, access via the Twitter API is staggered.
Therefore, it is necessary to set up a cronjob in order to always get the latest tweets and data.


Example using the CLI:
```
php7.2 /var/www/diss/cli/fetcher.php
```

Example for the setup of a cronjob: 
```
# m h dom mon mon dow command
*/5 * * * * * /usr/bin/php7.2 /var/www/diss/cli/fetcher.php > /dev/null
```

# Evaluation (index.php)
The evaluation can be done by Twitter account and period.

# Reporter (reporter.php)
The reporter is executed via the CLI and sends a regular report on the status of the tool.
You can configure the log level, the frequency of the reports, and the recipient e-mail.


Example using the CLI:
```
php7.2 /var/www/diss/cli/reporter.php
```

Example for the setup of a cronjob: 
```
# m h dom mon mon dow command
0 0 * * * * /usr/bin/php7.2 /var/www/diss/cli/reporter.php > /dev/null
```

---

# Einleitung
Dieses Tool wurde im Rahmen einer Doktorarbeit entwickelt, die zum Ziel hatte, die Interaktionen zwischen Abgeordneten des Deutschen Bundestages und den Bürgern auf Twitter zu erfassen und näher zu untersuchen.

# Funktionen 
Das Tool hat die folgenden Funktionen:
* Import von Twitter-Account-Namen basierend auf einer Kontaktliste mit Detailseiten im HTML-Format (z.B. Liste der Mitglieder des Deutschen Bundestages auf www.bundestag.de)
* Erfassung und regelmäßige Updates der Informationen zu Twitter-Accounts (z.B. Beschreibung, Anzahl der Follower und Favorites)
* Auslesen und Speichern der Tweets der Timelines der zu erfassenden Twitter-Accounts
* Auslesen und Speichern der laufenden Konversationen auf den zu erfassenden Twitter-Accounts
* Auswertungsfunktion basierend auf Twitter-Account und Zeitraum
* Berücksichtigt Emojis und verknüpfte Medien

# Besonderheiten
* Zugriff auf die Tweets über die Twitter-API
* Berücksichtigung der von Twitter vorgegebenen Rate Limits
* Pagination mit since_id und max_id durch die Tweets für hohe Performanz
* Logging- Funktion
* Automatische Erstellung der nötigen Datenbankstruktur

# Technische Voraussetzungen
* Apache 2 (oder vergleichbar)
* PHP >= 7.2
* MySQL >= 5.7 
* j7mbo/twitter-api-php

# Weitere Voraussetzungen
* Teile des Tools müssen als Cronjobs über das CLI angelegt werden, daher ist ein Zugriff via Shell / Bash notwendig.
* Ferner sind ein Twitter Developer-Account, sowie entsprechende App-Zugangsdaten (oauth_access_token, oauth_access_token_secret, consumer_key, consumer_secret) notwendig, um auf die Twitter-API zugreifen zu können.

# Beschreibung
Das Tool ist objektbasiert entwickelt worden und verfügt über entsprechende Models und Repositories über die die Datenzugriffe abgewickelt werden.
Es besteht aus den folgenden Kern-Elementen:

## Importer (importer.php)
Der Importer wird über das CLI ausgeführt und dient dazu, eine gegebenene Kontaktliste in Form einer Website nach Links zu Twitter-Accounts zu durchsuchen.
Übergeben wird hier daher die URL zu einer Kontaktliste im HTML-Format, die ihrerseits Links zu den Detailseiten enthält. 
Im konkreten Anwendungsfall wurde hier die Liste der Abgeordneten des Deutschen Bundestages auf www.bundestag.de übergeben.

Der Importer sucht in der übergebenen Kontaktliste dann basierend auf einem definierbaren Muster nach Links zu Detailseiten. Auf den Detailseiten wiederum sucht der Importer dann nach Links zu Twitter-Accounts und import die gefundenen Twitter-Accountnamen in die Datenbank.

Um die Last auf dem Zielserver gering zu halten und eine Sperrung der IP durch den Betreiber zu vermeiden, werden die Links zu den Detailseiten nach und nach abgearbeitet.
Hierfür ist daher die Einrichtung eines Cronjobs notwendig.

Der Importer nimmt folgende Parameter entgegen:
* **url**: URL zur Kontaktliste als HTML-Seite (default: `https://www.bundestag.de/ajax/filterlist/de/abgeordnete/525246-525246/h_e3c112579919ef960d06dbb9d0d44b67?limit=9999&view=BTBiographyList`)
* **baseUrl**: Base-URL der Kontaktliste und der Detailseiten (default: `https://www.bundestag.de`)
* **regExpDetailLinks**: Regulärer Ausdruck für das Extrahieren der Links zu den Detailseiten (default: `#<a[^>]+href="(/abgeordnete/biografien/[^"]+)"[^>]+>#`),
* **regExpTwitterLinks**: Regulärer Ausdruck für das Extrahieren der Links zu den Twitter-Accounts auf den Detailseiten (default: `#<a[^>]+href="(https://(www.)?twitter.com/[^"]+)"[^>]+>#`)
* **maxLinksLimit**: Maximale Anzahl der abzuarbeitenden Detail-Links je Aufruf (default: `10`)
* **checkInterval**: Interval, in der die Kontaktliste erneut geprüft wird, in Sekunden (default: `604800` = 1 Woche)

Beispiel-Aufruf über das CLI:
```
php7.2 /var/www/diss/cli/importer.php 'https://www.bundestag.de/ajax/filterlist/de/abgeordnete/525246-525246/h_e3c112579919ef960d06dbb9d0d44b67?limit=9999&view=BTBiographyList' 'https://www.bundestag.de' '#<a[^>]+href="(/abgeordnete/biografien/[^"]+)"[^>]+>#' '#<a[^>]+href="(https://(www.)?twitter.com/[^"]+)"[^>]+>#'
```

Beispiel für die Einrichtung eines Cronjobs: 
```
# m h  dom mon dow   command
*/5 * * * * /usr/bin/php7.2 /var/www/diss/cli/importer.php 'https://www.bundestag.de/ajax/filterlist/de/abgeordnete/525246-525246/h_e3c112579919ef960d06dbb9d0d44b67?limit=9999&view=BTBiographyList' 'https://www.bundestag.de' '#<a[^>]+href="(/abgeordnete/biografien/[^"]+)"[^>]+>#' '#<a[^>]+href="(https://(www.)?twitter.com/[^"]+)"[^>]+>#' > /dev/null
```

## Fetcher (fetcher.php)
Der Fetcher wird über das CLI ausgeführt und erledigt im Kern drei Aufgaben:
* Auslesen und Übertragung der Tweets aus den Timelines der zu erfassenden Twitter-Accounts in die Datenbank
* Aktualisierung der Informationen zu den zu erfassenden Twitter-Accounts (z.B. Beschreibung, Anzahl der Follower und Favorites)
* Auslesen und Übertragen der Konversationen mit anderen Nutzern auf den zu erfassenden Twitter-Accounts in die Datenbank

Um die Rate Limits von Twitter entsprechend berücksichtigen zu können, erfolgt der Zugriff über die Twitter API gestaffelt.
Daher ist die Einrichtung eines Cronjobs notwendig, um stets die aktuellsten Tweets und Daten erfassen zu können.


Beispiel-Aufruf über das CLI:
```
php7.2 /var/www/diss/cli/fetcher.php
```

Beispiel für die Einrichtung eines Cronjobs: 
```
# m h  dom mon dow   command
*/5 * * * * /usr/bin/php7.2 /var/www/diss/cli/fetcher.php > /dev/null
```

# Auswertung (index.php)
Die Auswertung kann nach Twitter-Account und Zeitraum erfolgen.

# Reporter (reporter.php)
Der Reporter wird über das CLI ausgeführt und sendet einen regelmäßigen Report über den Status des Tools.
Dabei kann das Log-Level, die Häufigkeit der Reports und die Empfänger- E-Mail konfiguriert werden.


Beispiel-Aufruf über das CLI:
```
php7.2 /var/www/diss/cli/reporter.php
```

Beispiel für die Einrichtung eines Cronjobs: 
```
# m h  dom mon dow   command
0 0 * * * /usr/bin/php7.2 /var/www/diss/cli/reporter.php > /dev/null
```
