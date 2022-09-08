# Alarmzone

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
5. [Alarmzonenstatus](#5-alarmzonenstatus)
6. [Ablaufplan](#6-ablaufplan)
   1. [Prüfung der Alarmsensoren](#61-prüfung-der-alarmsensoren)
   2. [Alarmzone scharf schalten](#62-alarmzone-scharf-schalten)
   3. [Alarmzone unscharf schalten](#63-alarmzone-unscharf-schalten)
  
7. [Externe Aktion](#7-externe-aktion)
8. [PHP-Befehlsreferenz](#8-php-befehlsreferenz)
   1. [Alarmzone schalten](#81-alarmzone-schalten)
   
### 1. Modulbeschreibung

Dieses Modul steuert und überwacht eine Alarmzone.

### 2. Voraussetzungen

- IP-Symcon ab Version 6.1

### 3. Schaubild

```
                                   +--------------------------+
                                   | Alarmzone (Modul)        |<------------- Externe Aktion
                                   |                          |
                                   | Vollschutz               |
                                   |                          |
                                   | Hüllschutz               |
                                   |                          |
                                   | Teilschutz               |
                                   |                          |
                                   | Alarmzonenstatus         |
                                   |                          |
                                   | Alarmstatus              |
                        Auslöser   |                          |
    Alarmsensor (HW) <-------------| Tür- und Fenstersensoren |
                        Auslöser   |                          |
Bewegungsmelder (HW) <-------------| Bewegungsmelder          |
                                   |                          |
                                   | Alarmsirene              |
                                   |                          |
                                   | Alarmbeleuchtung         |
                                   |                          |
                                   | Alarmanruf               |
                                   +--------------------------+             
                                                ^                                      
                                                |                            
                                                |            Auslöser         +----------------+
                                                +-----------------------------+ Externe Module |
                                                                              +----------------+
                                                                      
```

### 4. Betriebsarten

In den drei nachfolgenden Betriebsarten können die Alarmsensoren (Tür- und Fenstersensoren, Bewegungsmelder) individuell zugewiesen werden.

* Vollschutz 
* Hüllschutz
* Teilschutz

##### 4.1 Vollschutz

Der Vollschutz umfasst in der Regel alle Alarmsensoren, d.h. es werden sowohl Tür- und Fenstersensoren, als auch Bewegungsmelder überwacht.  
Diese Betriebsart wird aktiviert, wenn sich keine Person mehr im Objekt befindet.

##### 4.2 Hüllschutz

Der Hüllschutz umfasst in der Regel nur die Tür- und Fenstersensoren. Bewegungsmelder werden in der Regel nicht überwacht.  
Diese Betriebsart wird aktiviert, wenn sich noch Person im Objekt befinden, aber die Außenhülle überwacht werden soll.

##### 4.3 Teilschutz

Der Teilschutz enthält eine individuelle Zuweisung von Tür- und Fenstersensoren, sowie Bewegungsmeldern, welche überwacht werden sollen.  
Diese Betriebsart wird aktiviert, wenn z.B. in der Nacht gewisse Alarmsensoren (Schlafzimmer Fenster) von der Überwachung ausgeschlossen werden sollen.

### 5. Alarmzonenstatus

Der Alarmzonenstatus zeigt den Zustand der Alarmzone an.

Alarmzonenstatus:

| Status | Bezeichnung          | Beschreibung                                   |
|--------|----------------------|------------------------------------------------|
| 0      | Unscharf             | Die Alarmzone ist unscharf                     |
| 1      | Scharf               | Die Alarmzone ist scharf                       |
| 2      | Verzögert Scharf     | Die Alarmzone wird verzögert scharf geschaltet |

Detaillierter Alarmzonenstatus:

| Status | Bezeichnung          | Beschreibung                                                                             |
|--------|----------------------|------------------------------------------------------------------------------------------|
| 0      | Unscharf             | Die Alarmzone ist unscharf                                                               |
| 1      | Scharf               | Die Alarmzone ist scharf                                                                 |
| 2      | Verzögert Scharf     | Die Alarmzone wird verzögert scharf geschaltet                                           |
| 3      | Teilscharf           | Die Alarmzone ist teilscharf, es sind noch Türen oder Fenster geöffnet                   |
| 4      | Verzögert Teilscharf | Die Alarmzone wird verzögert scharf geschaltet, es sind noch Türen oder Fenster geöffnet |


### 6. Ablaufplan

Die nachfolgenden Punkte beschreiben die Prüfung der Alarmsensoren, sowie den Ablauf bei Scharf- und Unscharf-Schaltung der Alarmzone.

#### 6.1 Prüfung der Alarmsensoren

Prüfung der Alarmsensoren für den Voll-, Hüll- und Teilschutz:

| Alarmsensor aktiviert | Modus zugewiesen   | Aktivierungsprüfung  | Alarmsensorstatus | Aktivierung                | Sperrliste         |
|-----------------------|--------------------|----------------------|-------------------|----------------------------|--------------------|
| :white_check_mark:    | :white_check_mark: | :x:                  | Geschlossen       | :green_circle: Aktivierung | :x:                |
| :white_check_mark:    | :white_check_mark: | :x:                  | Geöffnet          | :green_circle: Aktivierung | :white_check_mark: |
| :white_check_mark:    | :white_check_mark: | :white_check_mark:   | Geschlossen       | :green_circle: Aktivierung | :x:                |
| :white_check_mark:    | :white_check_mark: | :white_check_mark:   | Geöffnet          | :exclamation:️ Abbruch     | :x:                |

#### 6.2 Alarmzone scharf schalten

- [x] :clock8: ***Mit*** Einschaltverzögerung

  - [x] Tür- und Fensterstatus entspricht der Konfiguration
    - [x] :yellow_circle: Alarmzonestatus: Verzögert Scharf
    - [x] :yellow_circle: Detaillierter Alarmzonestatus: Verzögert Scharf

  - [x] Tür- und Fensterstatus entspricht ***nicht*** der Konfiguration
    - [x] :yellow_circle: Alarmzonestatus: Verzögert Scharf 
    - [x] :yellow_circle: Detaillierter Alarmzonestatus: Verzögert Teilscharf

  - [x]  :warning: Scharfschaltung erfolgt nach definierter Verzögerung

   
- [x] :warning: Scharfschaltung

   - [x] :cop: ***Mit*** Aktivierungsprüfung

      - [x] Tür- und Fensterstatus ***entspricht*** der Konfiguration
        
        - [x] Offene Türen und Fenster ***ohne*** Aktivierungsprüfung
          - [x] :no_entry_sign: Offene Türen und Fenster werden für die Auslösung gesperrt
            - [x] :red_circle: Alarmzonenstatus: Scharf
            - [x] :yellow_circle: Detaillierter Alarmzonenstatus: Teilscharf

        - [x] Offene Türen und Fenster ***mit*** Aktivierungsprüfung
          - [x] :red_circle: Alarmzonenstatus: Scharf

      - [x] Tür- und Fensterstatus ***entspricht nicht*** der Konfiguration
         - [x] :heavy_exclamation_mark: Abbruch
         - [x] :green_circle: Alarmzonenstatus: Unscharf
         - [x] :green_circle: Detaillierter Alarmzonenstatus: Unscharf

   - [x] :cop: ***Ohne*** Aktivierungsprüfung
     
      - [x] Tür- und Fensterstatus ***entspricht*** der Konfiguration
         - [x] :red_circle: Alarmzonenstatus: Scharf
         - [x] :red_circle: Detaillierter Alarmzonenstatus: Scharf

      - [x] Tür- und Fensterstatus ***entspricht nicht*** der Konfiguration
         - [x] :no_entry_sign: Offene Türen und Fenster werden für die Auslösung gesperrt
         - [x] :red_circle: Alarmzonenstatus: Scharf
         - [x] :yellow_circle: Detaillierter Alarmzonenstatus: Teilscharf

#### 6.3 Alarmzone unscharf schalten

- [x] :green_circle: Alarmzonenstatus: Unscharf
- [x] :green_circle: Detaillierter Alarmzonenstatus: Unscharf

### 7. Externe Aktion

Das Modul kann über eine externe Aktion gesteuert werden.  
Nachfolgendes Beispiel aktiviert die Alarmzone mit Vollschutz.
> AZ_SelectProtectionMode(12345, 1, 'Sender ID');

### 8. PHP-Befehlsreferenz

#### 8.1 Alarmzone schalten

```
boolean AZ_SelectProtectionMode(integer INSTANCE_ID, integer MODE, string SENDER_ID);
```

Konnte der jeweilige Befehl erfolgreich ausgeführt werden, liefert er als Ergebnis **TRUE**, andernfalls **FALSE**.

| Parameter     | Beschreibung                                   | Wert           |
|---------------|------------------------------------------------|----------------|
| `INSTANCE_ID` | ID der Instanz                                 |                |
| `MODE`        | Modus, der geschaltet werden soll              | 0 = Unscharf   |
|               |                                                | 1 = Vollschutz |
|               |                                                | 2 = Hüllschutz |
|               |                                                | 3 = Teilschutz |
| `SENDER_ID`   | Eine Kennung, die den Sender identifiziert und |                |
|               | im Alarmprotokoll anzeigt                      |                |

Beispiel:
> AZ_SelectProtectionMode(12345, 0, 'Sender');

---