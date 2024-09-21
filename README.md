# lgsl-wordpress-plugin
 Wordpress Plugin für LGSL
Das LGSL (Live Game Server List) WordPress Plugin ermöglicht es Website-Betreibern, eine detaillierte Liste von Live-Spieleservern auf ihrer WordPress-Seite anzuzeigen. Es unterstützt eine Vielzahl von Spielen und bietet Echtzeit-Informationen wie den Serverstatus, die Anzahl der Spieler und die Karte, auf der gespielt wird. Das Plugin ist einfach zu bedienen und vollständig anpassbar, um nahtlos in jedes Website-Design zu passen.

Funktionen:

Serverüberwachung in Echtzeit: Zeigt aktuelle Informationen zu Spiele-Servern direkt auf Ihrer WordPress-Seite an.
Unterstützung für verschiedene Spiele: Kompatibel mit einer Vielzahl von Spielen und Servertypen.
Anpassbare Darstellung: Ermöglicht es Ihnen, die Anzeige der Serverliste an das Design Ihrer Website anzupassen.
Einfache Integration: Fügen Sie Serverinformationen einfach über verschiedene Shortcodes auf jeder Seite oder jedem Beitrag hinzu.
Mehrsprachigkeit: Unterstützt mehrere Sprachen für die Benutzeroberfläche.
Verfügbare Shortcodes:

Das LGSL WordPress Plugin bietet mehrere Shortcodes, um die Darstellung der Serverinformationen flexibel und anpassbar zu gestalten:

Grundlegender Shortcode: [lgsl_server_list]

Zeigt die vollständige Serverliste an, basierend auf den Anzeigeneinstellungen, die im Admin-Bereich des Plugins konfiguriert wurden.
Beispiel: [lgsl_server_list] zeigt alle Server an, die im Admin-Bereich eingerichtet sind, mit den voreingestellten Anzeigeoptionen.
Shortcode mit spezifischen Server-IDs: [lgsl_server_list id="1,2,3"]

Zeigt eine Serverliste für die angegebenen Server-IDs an und ignoriert dabei die allgemeinen Anzeigeneinstellungen im Admin-Bereich.
Beispiel: [lgsl_server_list id="1,2,3"] zeigt nur die Server mit den IDs 1, 2 und 3 an.
Shortcode mit individuellen Anzeigeoptionen:

[lgsl_server_list id="1,2,3" show_servername="false" show_ip="false" show_port="true" show_game="true" show_map="true" show_players="true" show_status="false"]
Zeigt die Serverliste für die angegebenen Server-IDs an und verwendet benutzerdefinierte Anzeigeoptionen, um bestimmte Informationen anzuzeigen oder auszublenden.
Erklärung der Optionen:
show_servername: Zeigt den Servernamen an (true/false).
show_ip: Zeigt die IP-Adresse des Servers an (true/false).
show_port: Zeigt den Serverport an (true/false).
show_game: Zeigt das Spiel an, das auf dem Server läuft (true/false).
show_map: Zeigt die aktuelle Karte an, die auf dem Server gespielt wird (true/false).
show_players: Zeigt die Anzahl der aktuellen Spieler an (true/false).
show_status: Zeigt den Status des Servers (online/offline) an (true/false).
Beispiel: [lgsl_server_list id="1,2,3" show_servername="false" show_ip="false" show_port="true" show_game="true" show_map="true" show_players="true" show_status="false"] zeigt die Serverliste für die IDs 1, 2 und 3 an, ohne den Servernamen, aber mit Port, Spiel, Karte und Spieleranzahl.
Installation:

Laden Sie das Plugin herunter und entpacken Sie es.
Laden Sie das Plugin-Verzeichnis lgsl-wordpress-plugin in das Verzeichnis /wp-content/plugins/ Ihrer WordPress-Installation hoch.
Aktivieren Sie das Plugin über das Menü 'Plugins' in WordPress.
Konfigurieren Sie das Plugin unter dem neuen Menüpunkt 'LGSL' und fügen Sie Serverdetails hinzu.
Verwendung: Nach der Konfiguration können Sie die verschiedenen Shortcodes nutzen, um die Serverliste individuell angepasst auf jeder Seite oder jedem Beitrag Ihrer WordPress-Seite anzeigen zu lassen.

Voraussetzungen:

WordPress Version 4.6 oder höher
PHP Version 5.6 oder höher
Support & Entwicklung: Dieses Plugin wird von der Community auf GitHub entwickelt und gepflegt. Für Unterstützung, Vorschläge oder zur Fehlerberichterstattung besuchen Sie bitte das GitHub-Repository.

