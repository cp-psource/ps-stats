=== CP Stats ===
* Contributors:      DerN3rd
* Tags:              analytics, dashboard, pageviews, privacy, statistics, stats, visits, web stats, widget
* Requires at least: 4.7
* Tested up to:      6.1
* Requires PHP:      5.2
* Stable tag:        1.8.4
* License:           GPLv3 or later
* License URI:       https://www.gnu.org/licenses/gpl-3.0.html

Besucherstatistik für ClassicPress mit Fokus auf Datenschutz, Transparenz und Übersichtlichkeit. Perfekt als Widget in Deinem ClassicPress Dashboard.


## Beschreibung ##
CPStats bietet einen einfachen und kompakten Zugriff auf die Anzahl der Seitenaufrufe. Es ist datenschutzfreundlich, da es weder Cookies noch eine dritte Partei verwendet.

Auf ein interaktives Diagramm folgen Listen mit den häufigsten Verweisquellen und Zielseiten. Der Zeitraum der Statistik und die Länge der Listen können direkt im Dashboard-Widget eingestellt werden.

###Datenschutz ###
Im direkten Vergleich zu Statistikdiensten wie *Google Analytics*, *ClassicPress.com Stats* und *Matomo (Piwik)* verarbeitet und speichert *CPStats* keine personenbezogenen Daten wie z.B. IP-Adressen - *CPStats* zählt Seitenaufrufe, nicht Besucher.

Absolute Datenschutzkonformität gepaart mit transparenten Verfahren: Eine lokal in ClassicPress angelegte Datenbanktabelle besteht aus nur vier Feldern (ID, Datum, Quelle, Ziel) und kann vom Administrator jederzeit eingesehen, bereinigt und gelöscht werden.

Durch diesen Tracking-Ansatz ist CPStats 100% konform mit GDPR und dient als leichtgewichtige Alternative zu anderen Tracking-Diensten.

### Anzeige des Widgets ###
Die Konfiguration des Plugins kann direkt im *CPStats* Widget auf dem Dashboard über den Link *Konfigurieren* geändert werden.

Die Anzahl der im *CPStats* Widget angezeigten Links kann ebenso eingestellt werden wie die Option, nur Aufrufe von heute zu zählen. Ältere Einträge werden bei der Änderung dieser Einstellung natürlich nicht gelöscht.

Die Statistiken für das Dashboard-Widget werden für vier Minuten zwischengespeichert.

### Zeitraum der Datenspeicherung ###
*CPStats* speichert die Daten nur für einen begrenzten Zeitraum (Standard: zwei Wochen), längere Intervalle können als Option im Widget ausgewählt werden. Daten, die älter sind als der gewählte Zeitraum, werden durch einen täglichen Cron-Job gelöscht.

Da alle statistischen Werte in der lokalen ClassicPress-Datenbank gesammelt und verwaltet werden, ist mit einer Erhöhung des Datenbankvolumens zu rechnen (insbesondere wenn Sie den Zeitraum der Datenspeicherung erhöhen).

### JavaScript-Tracking für Caching-Kompatibilität ###
Für die Kompatibilität mit Caching-Plugins wie [Cachify](http://cachify.de) bietet *CPStats* ein optional zuschaltbares Tracking via JavaScript. Diese Funktion ermöglicht eine zuverlässige Zählung der gecachten Blogseiten.

Damit dies korrekt funktioniert, muss das aktive Theme `wp_footer()` aufrufen, typischerweise in einer Datei namens `footer.php`.

### Tracking für Spam-Referrer überspringen ###
Die Kommentar-Blacklist kann aktiviert werden, um das Tracking für Aufrufe zu überspringen, deren Referrer-URL in der Kommentar-Blacklist aufgeführt ist, d. h. die als Spam angesehen werden.

### Unterstützung ###
Wenn Du Probleme hast oder glaubst, einen Fehler gefunden zu haben (z.B. ein unerwartetes Verhalten), poste es bitte in den [Support-Foren] (https://wordpress.org/support/plugin/cpstats).

### Contribute ###
* Die aktive Entwicklung dieses Plugins wird [auf GitHub](https://github.com/cp-psource/cp-stats) betrieben.
* Pull Requests für dokumentierte Bugs sind sehr willkommen.
* Wenn Du uns bei der Übersetzung dieses Plugins helfen willst, kannst Du dies [auf ClassicPress Translate](https://github.com/cp-psource/cp-stats/) tun.



## Changelog ##
You can find the full changelog in [our GitHub repository](https://github.com/cp-psource/cp-stats/blob/master/CHANGELOG.md).

### 1.8.4
* Use same date retrieval for tracking and analysis (#227) (#232)
* Replace input filtering for PHP 8.1 compatibility (#237)
* Minor markup corrections in dashboard widget (#235)
* Tested up to ClassicPress 6.1

### 1.8.3
* Update documentation links (#204)
* Minor markup fix on settings page (#206)
* Dashboard widget is closeable again (#208) (#209)
* Fix static initialization on multisite with PHP 8 (#210, props @walterebert)
* Tested up to ClassicPress 5.8

### 1.8.2
* Minor adjustments for the dashboard widget (#197) (#199)
* Tested up to ClassicPress 5.7

### 1.8.1
* Fix AMP compatibility for Standard and Transitional mode (#181) (#182)
* JavaScript is no longer embedded if request is served by AMP (#181) (#182)
* Always register the action for the cleanup (#184)
* Exclude sitemap calls (WP 5.5+) from tracking (#185) (#186)
* Tested up to ClassicPress 5.6

### 1.8.0
* Fix date offset in dashboard widget in WP 5.3+ environments with mixed timezones (#167)
* Allow to deactivate the nonce check during JavaScript tracking (#168)
* Add support for "disallowed_keys" option instead of "blacklist_keys" in ClassicPress 5.5 (#174)
* Add refresh button in the dashboard, increase caching time (#157)

### 1.7.2
* Prevent JavaScript tracking from raising 400 for logged-in users, if tracking is disabled (#159)
* Use `wp_die()` instead of header and exit for AJAX requests (#160)
* Fix 1 day offset between display range and number of days evaluated in top lists (#162)

### 1.7.1
* Fix refresh of the dashboard widget when settings have been changed through the settings page (#147)
* Fix _Cachify_ cache not being flushed after changing JavaScript settings (#152)
* Fix date inconsistency for number of total visits (#150)
* Extend user agent filter for bot detection (#149) (#151)
* Update tooltip library (containing a bugfix in IE 11) (#156)

### 1.7.0
* Fix JavaScript embedding when bots visit before caching (#84) (#86)
* Fix offset in visitor reporting due to different timezones between PHP and database (#117, props @sophiehuiberts)
* Fix untranslatable support link (#122) (#126, props @arkonisus)
* Add separate settings page and reduced widget backview to widget settings only (#111)
* Add options to track logged in users (#103) (#111)
* Add option to show total visits (#134, props @yurihs)
* Refactored JavaScript tracking to use WP AJAX (#109) (#142)
* Introduced new option to separate display from storage range (#72)
* Automatically add AMP analytics trigger if official AMP PlugIn is installed (#110) (#116, props @tthemann)
* Dashboard widget is now scrollable with dynamic point radius to keep long-term statistics readable (#71) (#101, props @manumeter)
* Improved bot detection (#112) (#125, props @mahype)
* Updated Chartist JS library for dashboard widget (#132)
* Skip tracking for favicon.ico redirects (since WP 5.4) (#144)
* Tested up to ClassicPress 5.4

For the complete changelog, check out our [GitHub repository](https://github.com/cp-psource/cp-stats).


## Upgrade Notice ##

### 1.8.4 ###
This is a maintenance release targeting ClassicPress 6.1 and PHP 8.1 compatibility. It is recommended for all users.

### 1.8.3 ###
This is a bugfix with corrections for the dashboard widget and PHP 8 issues on multisite. It is recommended for all users.

### 1.8.2 ###
This is a maintenance release with minor changes in the dashboard widget. Compatible with ClassicPress 5.7.

### 1.8.1 ###
This is a bugfix release improving AMP compatibility and excluding native sitemaps as of ClassicPress 5.5. It is recommended for all users.

### 1.8.0 ###
Some minor improvements. The most important one: This version offers to deactivate the nonce check for JavaScript tracking (recommend if a caching plugin with a long caching time is used).


## Screenshots ##
1. CPStats dashboard widget
2. CPStats dashboard widget settings
3. CPStats settings page
