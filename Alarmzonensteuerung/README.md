# Alarmzonensteuerung

Zur Verwendung dieses Moduls als Privatperson, Einrichter oder Integrator wenden Sie sich bitte zunächst an den Autor.

Für dieses Modul besteht kein Anspruch auf Fehlerfreiheit, Weiterentwicklung, sonstige Unterstützung oder Support.  
Bevor das Modul installiert wird, sollte unbedingt ein Backup von IP-Symcon durchgeführt werden.  
Der Entwickler haftet nicht für eventuell auftretende Datenverluste oder sonstige Schäden.  
Der Nutzer stimmt den o.a. Bedingungen, sowie den Lizenzbedingungen ausdrücklich zu.

### Inhaltsverzeichnis

1. [Modulbeschreibung](#1-modulbeschreibung)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Schaubild](#3-schaubild)
4. [Betriebsarten](#4-betriebsarten)
   1. [Vollschutz](#41-vollschutz)
   2. [Hüllschutz](#42-hüllschutz)
   3. [Teilschutz](#43-teilschutz)
   4. [Individualschutz](#44-teilschutz)
5. [Systemstatus](#5-systemstatus)
6. [Ablaufplan](#6-ablaufplan)
7. [Externe Aktion](#7-externe-aktion)
8. [PHP-Befehlsreferenz](#8-php-befehlsreferenz)
   1. [Alarmzonen schalten](#81-alarmzonen-schalten)
   
### 1. Modulbeschreibung

Dieses Modul steuert und überwacht mehrere Alarmzonen.

### 2. Voraussetzungen

- IP-Symcon ab Version 6.1

### 3. Schaubild

```
                          +-----------------------------+
                          | Alarmzonensteuerung (Modul) |<-------------- Externe Aktion
                          |                             |
               Auslöser   |                             |   Aktionen
Alarmzonen <--------------| Vollschutz                  |--------------> Alarmzone 1, Alarmzone 2, Alarmzone n
                          |                             |
Alarmzonen <--------------| Hüllschutz                  |--------------> Alarmzone 1, Alarmzone 2, Alarmzone n
                          |                             |
Alarmzonen <--------------| Teilschutz                  |--------------> Alarmzone 1, Alarmzone 2, Alarmzone n
                          |                             |
Alarmzonen <--------------| Tür- und Fenstersensoren    |
                          |                             |
Alarmzonen <--------------| Bewegungsmelder             |
                          +-----------------------------+    
                                         ^                                      
                                         |                            
                                         |            Auslöser         +----------------+
                                         +-----------------------------+ Externe Module |
                                                                       +----------------+         
```

### 4. Betriebsarten

In den drei nachfolgenden Betriebsarten können die Alarmzonen geschaltet werden.

* Vollschutz
* Hüllschutz 
* Teilschutz
* Individualschutz

##### 4.1 Vollschutz

Der Vollschutz umfasst in der Regel alle Alarmsensoren, d.h. es werden sowohl Tür- und Fenstersensoren, als auch Bewegungsmelder überwacht.  
Diese Betriebsart wird für alle Alarmzonen aktiviert, wenn sich keine Person mehr im Objekt befindet.

##### 4.2 Hüllschutz

Der Hüllschutz umfasst in der Regel nur die Tür- und Fenstersensoren. Bewegungsmelder werden in der Regel nicht überwacht.  
Diese Betriebsart wird für alle Alarmzonen aktiviert, wenn sich noch Person im Objekt befinden, aber die Außenhülle überwacht werden soll.

##### 4.3 Teilschutz

Der Teilschutz enthält eine individuelle Zuweisung von Tür- und Fenstersensoren, sowie Bewegungsmeldern, welche überwacht werden sollen.  
Diese Betriebsart wird für alle Alarmzonen aktiviert, wenn z.B. in der Nacht gewisse Alarmsensoren (Schlafzimmer Fenster) von der Überwachung ausgeschlossen werden sollen.

##### 4.4 Individualschutz

Der Individualschutz kann die jeweilige Alarmzone mit einem unterschiedlichen Schutz individuell schalten.

### 5. Systemstatus

Der Systemstatus zeigt den Zustand der Alarmzonen an.

Systemstatus:

| Status | Bezeichnung          | Beschreibung                                       |
|--------|----------------------|----------------------------------------------------|
| 0      | Unscharf             | Die Alarmzonen sind unscharf                       |
| 1      | Scharf               | Die Alarmzonen sind scharf                         |
| 2      | Verzögert Scharf     | Die Alarmzonen werdenverzögert scharf geschaltet   |

Detaillierter Systemstatus:

| Status | Bezeichnung          | Beschreibung                                                                                |
|--------|----------------------|---------------------------------------------------------------------------------------------|
| 0      | Unscharf             | Die Alarmzonen sind unscharf                                                                |
| 1      | Scharf               | Die Alarmzonen sind scharf                                                                  |
| 2      | Verzögert Scharf     | Die Alarmzonen werden verzögert scharf geschaltet                                           |
| 3      | Teilscharf           | Die Alarmzone(n) ist/sind teilscharf, es sind noch Türen oder Fenster geöffnet              |
| 4      | Verzögert Teilscharf | Die Alarmzonen werden verzögert scharf geschaltet, es sind noch Türen oder Fenster geöffnet |


### 6. Ablaufplan

Der Ablaufplan ist in der jeweiligen Alarmzone definiert.

### 7. Externe Aktion

Das Modul kann über eine externe Aktion gesteuert werden.  
Nachfolgendes Beispiel aktiviert die Alarmzone mit Vollschutz.
> AZST_SelectProtectionMode(12345, 1, 'Sender ID');

### 8. PHP-Befehlsreferenz

#### 8.1 Alarmzonen schalten

```
boolean AZST_SelectProtectionMode(integer INSTANCE_ID, integer MODE, string SENDER_ID);
```

Konnte der jeweilige Befehl erfolgreich ausgeführt werden, liefert er als Ergebnis **TRUE**, andernfalls **FALSE**.

| Parameter     | Beschreibung                                   | Wert                 |
|---------------|------------------------------------------------|----------------------|
| `INSTANCE_ID` | ID der Instanz                                 |                      |
| `MODE`        | Modus, der geschaltet werden soll              | 0 = Unscharf         |
|               |                                                | 1 = Vollschutz       |
|               |                                                | 2 = Hüllschutz       |
|               |                                                | 3 = Teilschutz       |
|               |                                                | 3 = Individualschutz |
| `SENDER_ID`   | Eine Kennung, die den Sender identifiziert und |                      |
|               | im Alarmprotokoll anzeigt                      |                      |

Beispiel:
> AZST_SelectProtectionMode(12345, 0, 'Sender');

---