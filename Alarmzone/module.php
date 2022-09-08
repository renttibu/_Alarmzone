<?php

/**
 * @project       _Alarmzone/Alarmzone
 * @file          module.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

declare(strict_types=1);

include_once __DIR__ . '/helper/AZ_autoload.php';

class Alarmzone extends IPSModule
{
    //Helper
    use AZ_AlarmProtocol;
    use AZ_Blacklist;
    use AZ_Config;
    use AZ_Control;
    use AZ_DoorWindowSensors;
    use AZ_MotionDetectors;
    use AZ_Notification;

    //Constants
    private const MODULE_NAME = 'Alarmzone';
    private const MODULE_PREFIX = 'AZ';
    private const MODULE_VERSION = '7.0-1, 08.09.2022';
    private const ALARMPROTOCOL_MODULE_GUID = '{66BDB59B-E80F-E837-6640-005C32D5FC24}';
    private const NOTIFICATION_MODULE_GUID = '{BDAB70AA-B45D-4CB4-3D65-509CFF0969F9}';
    private const HOMEMATIC_DEVICE_GUID = '{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}';
    private const SLEEP_DELAY = 100;

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        ########## Properties

        //Info
        $this->RegisterPropertyString('Note', '');

        //Designations
        $this->RegisterPropertyString('SystemName', 'Alarmzone');
        $this->RegisterPropertyString('Location', '');
        $this->RegisterPropertyString('AlarmZoneName', '');

        //Functions
        $this->RegisterPropertyBoolean('EnableActive', false);
        $this->RegisterPropertyBoolean('EnableLocation', true);
        $this->RegisterPropertyBoolean('EnableAlarmZoneName', true);
        $this->RegisterPropertyBoolean('EnableMode', true);
        $this->RegisterPropertyString('DisarmedName', 'Unscharf');
        $this->RegisterPropertyString('DisarmedIcon', 'Warning');
        $this->RegisterPropertyInteger('DisarmedColor', 65280);
        $this->RegisterPropertyBoolean('UseFullProtectionMode', true);
        $this->RegisterPropertyString('FullProtectionName', 'Vollschutz');
        $this->RegisterPropertyString('FullProtectionIcon', 'Basement');
        $this->RegisterPropertyInteger('FullProtectionColor', 16711680);
        $this->RegisterPropertyBoolean('UseHullProtectionMode', false);
        $this->RegisterPropertyString('HullProtectionName', 'Hüllschutz');
        $this->RegisterPropertyString('HullProtectionIcon', 'Presence');
        $this->RegisterPropertyInteger('HullProtectionColor', 16776960);
        $this->RegisterPropertyBoolean('UsePartialProtectionMode', false);
        $this->RegisterPropertyString('PartialProtectionName', 'Teilschutz');
        $this->RegisterPropertyString('PartialProtectionIcon', 'Moon');
        $this->RegisterPropertyInteger('PartialProtectionColor', 255);
        $this->RegisterPropertyBoolean('EnableAlarmZoneState', true);
        $this->RegisterPropertyBoolean('EnableAlarmZoneDetailedState', false);
        $this->RegisterPropertyBoolean('EnableDoorWindowState', true);
        $this->RegisterPropertyBoolean('EnableMotionDetectorState', true);
        $this->RegisterPropertyBoolean('EnableAlarmState', true);
        $this->RegisterPropertyBoolean('EnableAlertingSensor', true);
        $this->RegisterPropertyBoolean('EnableAlarmSirenState', false);
        $this->RegisterPropertyBoolean('EnableAlarmLightState', false);
        $this->RegisterPropertyBoolean('EnableAlarmCallState', false);

        //Activation check
        $this->RegisterPropertyBoolean('CheckFullProtectionModeActivation', false);
        $this->RegisterPropertyBoolean('CheckHullProtectionModeActivation', false);
        $this->RegisterPropertyBoolean('CheckPartialProtectionModeActivation', false);

        //Activation delay
        $this->RegisterPropertyInteger('FullProtectionModeActivationDelay', 0);
        $this->RegisterPropertyInteger('HullProtectionModeActivationDelay', 0);
        $this->RegisterPropertyInteger('PartialProtectionModeActivationDelay', 0);

        //Alarm protocol
        $this->RegisterPropertyInteger('AlarmProtocol', 0);

        //Notification center
        $this->RegisterPropertyInteger('Notification', 0);

        //Notification disarmed
        $this->RegisterPropertyString('DeactivationNotification', '[{"Use":false,"Designation":"Alarmzone Aus","SpacerNotification":"","LabelMessageText":"","MessageText":"🟢 Alarmzone unscharf!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":""}]');

        //Notification full protection
        $this->RegisterPropertyString('FullProtectionAbortActivationNotification', '[{"Use":false,"Designation":"Vollschutz Abbruch","SpacerNotification":"","LabelMessageText":"","MessageText":"⚠️ Abbruch durch Aktivierungsprüfung!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":"","SpacerOpenDoorWindowNotification":"","LabelOpenDoorWindowNotification":"","UseOpenDoorWindowNotification":false}]');
        $this->RegisterPropertyString('FullProtectionDelayedActivationNotification', '[{"Use":false,"Designation":"Vollschutz Verzögerung","SpacerNotification":"","LabelMessageText":"","MessageText":"🕗 Alarmzone in %1$s Sekunden verzögert scharf!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":"","SpacerOpenDoorWindowNotification":"","LabelOpenDoorWindowNotification":"","UseOpenDoorWindowNotification":false}]');
        $this->RegisterPropertyString('FullProtectionActivationWithOpenDoorWindowNotification', '[{"Use":false,"Designation":"Vollschutz An + Tür/Fenster geöffnet","SpacerNotification":"","LabelMessageText":"","MessageText":"🟡 Alarmzone teilscharf!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":"","SpacerOpenDoorWindowNotification":"","LabelOpenDoorWindowNotification":"","UseOpenDoorWindowNotification":false}]');
        $this->RegisterPropertyString('FullProtectionActivationNotification', '[{"Use":false,"Designation":"Vollschutz An","SpacerNotification":"","LabelMessageText":"","MessageText":"🔴 Alarmzone scharf!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":"","SpacerOpenDoorWindowNotification":"","LabelOpenDoorWindowNotification":"","UseOpenDoorWindowNotification":false}]');

        //Notification hull protection
        $this->RegisterPropertyString('HullProtectionAbortActivationNotification', '[{"Use":false,"Designation":"Hüllschutz Abbruch","SpacerNotification":"","LabelMessageText":"","MessageText":"⚠️ Abbruch durch Aktivierungsprüfung!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":"","SpacerOpenDoorWindowNotification":"","LabelOpenDoorWindowNotification":"","UseOpenDoorWindowNotification":false}]');
        $this->RegisterPropertyString('HullProtectionDelayedActivationNotification', '[{"Use":false,"Designation":"Hüllschutz Verzögerung","SpacerNotification":"","LabelMessageText":"","MessageText":"🕗 Alarmzone in %1$s Sekunden verzögert scharf!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":"","SpacerOpenDoorWindowNotification":"","LabelOpenDoorWindowNotification":"","UseOpenDoorWindowNotification":false}]');
        $this->RegisterPropertyString('HullProtectionActivationWithOpenDoorWindowNotification', '[{"Use":false,"Designation":"Hüllschutz An + Tür/Fenster geöffnet","SpacerNotification":"","LabelMessageText":"","MessageText":"🟡 Alarmzone teilscharf!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":"","SpacerOpenDoorWindowNotification":"","LabelOpenDoorWindowNotification":"","UseOpenDoorWindowNotification":false}]');
        $this->RegisterPropertyString('HullProtectionActivationNotification', '[{"Use":false,"Designation":"Hüllschutz An","SpacerNotification":"","LabelMessageText":"","MessageText":"🔴 Alarmzone scharf!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":"","SpacerOpenDoorWindowNotification":"","LabelOpenDoorWindowNotification":"","UseOpenDoorWindowNotification":false}]');

        //Notification partial protection
        $this->RegisterPropertyString('PartialProtectionAbortActivationNotification', '[{"Use":false,"Designation":"Teilschutz Abbruch","SpacerNotification":"","LabelMessageText":"","MessageText":"⚠️ Abbruch durch Aktivierungsprüfung!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":"","SpacerOpenDoorWindowNotification":"","LabelOpenDoorWindowNotification":"","UseOpenDoorWindowNotification":false}]');
        $this->RegisterPropertyString('PartialProtectionDelayedActivationNotification', '[{"Use":false,"Designation":"Teilschutz Verzögerung","SpacerNotification":"","LabelMessageText":"","MessageText":"🕗 Alarmzone in %1$s Sekunden verzögert scharf!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":"","SpacerOpenDoorWindowNotification":"","LabelOpenDoorWindowNotification":"","UseOpenDoorWindowNotification":false}]');
        $this->RegisterPropertyString('PartialProtectionActivationWithOpenDoorWindowNotification', '[{"Use":false,"Designation":"Teilschutz An + Tür/Fenster geöffnet","SpacerNotification":"","LabelMessageText":"","MessageText":"🟡 Alarmzone teilscharf!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":"","SpacerOpenDoorWindowNotification":"","LabelOpenDoorWindowNotification":"","UseOpenDoorWindowNotification":false}]');
        $this->RegisterPropertyString('PartialProtectionActivationNotification', '[{"Use":false,"Designation":"Teilschutz An","SpacerNotification":"","LabelMessageText":"","MessageText":"🔴 Alarmzone scharf!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":"","SpacerOpenDoorWindowNotification":"","LabelOpenDoorWindowNotification":"","UseOpenDoorWindowNotification":false}]');

        //Notification open doors and windows
        $this->RegisterPropertyString('OpenDoorWindowNotification', '[{"Use":false,"Designation":"Tür/Fenster geöffnet","SpacerNotification":"","LabelMessageText":"","MessageText": "🔵 %1$s ist noch geöffnet!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":""}]');

        //Notification alarm
        $this->RegisterPropertyString('DoorWindowAlarmNotification', '[{"Use":false,"Designation":"Tür/Fenster Alarm","SpacerNotification":"","LabelMessageText":"","MessageText":"❗️%1$s hat einen Alarm ausgelöst!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"alarm","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":""}]');
        $this->RegisterPropertyString('MotionDetectorAlarmNotification', '[{"Use":false,"Designation":"Bewegungsmelder Alarm","SpacerNotification":"","LabelMessageText":"","MessageText":"❗%1$s hat einen Alarm ausgelöst!","UseTimestamp":true,"SpacerWebFrontNotification":"","LabelWebFrontNotification":"","UseWebFrontNotification":false,"WebFrontNotificationTitle":"","WebFrontNotificationIcon":"","WebFrontNotificationDisplayDuration":0,"SpacerWebFrontPushNotification":"","LabelWebFrontPushNotification":"","UseWebFrontPushNotification":false,"WebFrontPushNotificationTitle":"","WebFrontPushNotificationSound":"alarm","WebFrontPushNotificationTargetID":0,"SpacerMail":"","LabelMail":"","UseMailer":false,"Subject":"","SpacerSMS":"","LabelSMS":"","UseSMS":false,"SMSTitle":"","SpacerTelegram":"","LabelTelegram":"","UseTelegram":false,"TelegramTitle":""}]');

        //Alarm sensors
        $this->RegisterPropertyString('DoorWindowSensors', '[]');
        $this->RegisterPropertyString('MotionDetectors', '[]');

        ########## Variables

        //Active
        $id = @$this->GetIDForIdent('Active');
        $this->RegisterVariableBoolean('Active', 'Aktiv', '~Switch', 10);
        $this->EnableAction('Active');
        if (!$id) {
            $this->SetValue('Active', true);
        }

        //Location
        $id = @$this->GetIDForIdent('Location');
        $this->RegisterVariableString('Location', 'Standortbezeichnung', '', 20);
        $this->SetValue('Location', $this->ReadPropertyString('Location'));
        if (!$id) {
            IPS_SetIcon($this->GetIDForIdent('Location'), 'IPS');
        }

        //Alarm zone name
        $id = @$this->GetIDForIdent('AlarmZoneName');
        $this->RegisterVariableString('AlarmZoneName', 'Alarmzonenbezeichnung', '', 30);
        $this->SetValue('AlarmZoneName', $this->ReadPropertyString('AlarmZoneName'));
        if (!$id) {
            IPS_SetIcon($this->GetIDForIdent('AlarmZoneName'), 'IPS');
        }

        //Alarm zone state
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.AlarmZoneState';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 1);
        }
        IPS_SetVariableProfileIcon($profile, '');
        IPS_SetVariableProfileAssociation($profile, 0, 'Unscharf', 'IPS', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'Scharf', 'Warning', 0xFF0000);
        IPS_SetVariableProfileAssociation($profile, 2, 'Verzögert Scharf', 'Clock', 0xFFFF00);
        $this->RegisterVariableInteger('AlarmZoneState', 'Alarmzonenstatus', $profile, 50);

        //Alarm zone detailed state
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.AlarmZoneDetailedState';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 1);
        }
        IPS_SetVariableProfileIcon($profile, '');
        IPS_SetVariableProfileAssociation($profile, 0, 'Unscharf', 'IPS', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'Scharf', 'Warning', 0xFF0000);
        IPS_SetVariableProfileAssociation($profile, 2, 'Verzögert Scharf', 'Clock', 0xFFFF00);
        IPS_SetVariableProfileAssociation($profile, 3, 'Teilscharf', 'Warning', 0xFFFF00);
        IPS_SetVariableProfileAssociation($profile, 4, 'Verzögert Teilscharf', 'Warning', 0xFFFF00);
        $this->RegisterVariableInteger('AlarmZoneDetailedState', 'Detaillierter Alarmzonenstatus', $profile, 60);

        //Door and window state
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.DoorWindowState';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 0);
        }
        IPS_SetVariableProfileIcon($profile, 'Window');
        IPS_SetVariableProfileAssociation($profile, 0, 'Geschlossen', '', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'Geöffnet', '', 0xFF0000);
        $this->RegisterVariableBoolean('DoorWindowState', 'Tür- und Fensterstatus', $profile, 70);

        //Motion detector state
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.MotionDetectorState';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 0);
        }
        IPS_SetVariableProfileIcon($profile, 'Motion');
        IPS_SetVariableProfileAssociation($profile, 0, 'OK', '', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'Bewegung erkannt', '', 0xFF0000);
        $this->RegisterVariableBoolean('MotionDetectorState', 'Bewegungsmelderstatus', $profile, 80);

        //Alarm state
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.AlarmState';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 1);
        }
        IPS_SetVariableProfileIcon($profile, '');
        IPS_SetVariableProfileAssociation($profile, 0, 'OK', 'Warning', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'Alarm', 'Alert', 0xFF0000);
        $this->RegisterVariableInteger('AlarmState', 'Alarmstatus', $profile, 90);

        //Alerting sensor
        $id = @$this->GetIDForIdent('AlertingSensor');
        $this->RegisterVariableString('AlertingSensor', 'Auslösender Alarmsensor', '', 100);
        $this->SetValue('AlertingSensor', '');
        if (!$id) {
            IPS_SetIcon($this->GetIDForIdent('AlertingSensor'), 'Eyes');
        }

        //Alarm siren
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.AlarmSirenStatus';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 0);
        }
        IPS_SetVariableProfileIcon($profile, 'Alert');
        IPS_SetVariableProfileAssociation($profile, 0, 'Aus', '', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'An', '', 0xFF0000);
        $this->RegisterVariableBoolean('AlarmSiren', 'Alarmsirene', $profile, 110);

        //Alarm light
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.AlarmLightStatus';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 0);
        }
        IPS_SetVariableProfileIcon($profile, 'Bulb');
        IPS_SetVariableProfileAssociation($profile, 0, 'Aus', '', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'An', '', 0xFF0000);
        $this->RegisterVariableBoolean('AlarmLight', 'Alarmbeleuchtung', $profile, 120);

        //Alarm call
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.AlarmCallStatus';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 0);
        }
        IPS_SetVariableProfileIcon($profile, 'Mobile');
        IPS_SetVariableProfileAssociation($profile, 0, 'Aus', '', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'An', '', 0xFF0000);
        $this->RegisterVariableBoolean('AlarmCall', 'Alarmanruf', $profile, 130);

        ########## Timer

        $this->RegisterTimer('StartActivation', 0, self::MODULE_PREFIX . '_StartActivation(' . $this->InstanceID . ');');
    }

    public function ApplyChanges()
    {
        //Wait until IP-Symcon is started
        $this->RegisterMessage(0, IPS_KERNELSTARTED);

        //Never delete this line!
        parent::ApplyChanges();

        //Check runlevel
        if (IPS_GetKernelRunlevel() != KR_READY) {
            return;
        }

        ########## Maintain variable

        //Mode
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.Mode';
        if (IPS_VariableProfileExists($profile)) {
            IPS_DeleteVariableProfile($profile);
        }
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 1);
        }
        IPS_SetVariableProfileAssociation($profile, 0, $this->ReadPropertyString('DisarmedName'), $this->ReadPropertyString('DisarmedIcon'), $this->ReadPropertyInteger('DisarmedColor'));
        if ($this->ReadPropertyBoolean('UseFullProtectionMode')) {
            IPS_SetVariableProfileAssociation($profile, 1, $this->ReadPropertyString('FullProtectionName'), $this->ReadPropertyString('FullProtectionIcon'), $this->ReadPropertyInteger('FullProtectionColor'));
        }
        if ($this->ReadPropertyBoolean('UseHullProtectionMode')) {
            IPS_SetVariableProfileAssociation($profile, 2, $this->ReadPropertyString('HullProtectionName'), $this->ReadPropertyString('HullProtectionIcon'), $this->ReadPropertyInteger('HullProtectionColor'));
        }
        if ($this->ReadPropertyBoolean('UsePartialProtectionMode')) {
            IPS_SetVariableProfileAssociation($profile, 3, $this->ReadPropertyString('PartialProtectionName'), $this->ReadPropertyString('PartialProtectionIcon'), $this->ReadPropertyInteger('PartialProtectionColor'));
        }
        $this->RegisterVariableInteger('Mode', 'Modus', $profile, 40);
        $this->MaintainVariable('Mode', 'Modus', 1, $profile, 40, true);
        $this->EnableAction('Mode');

        ########## Options

        //Active
        IPS_SetHidden($this->GetIDForIdent('Active'), !$this->ReadPropertyBoolean('EnableActive'));

        //Location
        $this->SetValue('Location', $this->ReadPropertyString('Location'));
        IPS_SetHidden($this->GetIDForIdent('Location'), !$this->ReadPropertyBoolean('EnableLocation'));

        //Alarm zone name
        $this->SetValue('AlarmZoneName', $this->ReadPropertyString('AlarmZoneName'));
        IPS_SetHidden($this->GetIDForIdent('AlarmZoneName'), !$this->ReadPropertyBoolean('EnableAlarmZoneName'));

        //Mode
        IPS_SetHidden($this->GetIDForIdent('Mode'), !$this->ReadPropertyBoolean('EnableMode'));

        //Alarm zone state
        IPS_SetHidden($this->GetIDForIdent('AlarmZoneState'), !$this->ReadPropertyBoolean('EnableAlarmZoneState'));

        //Detailed alarm zone state
        IPS_SetHidden($this->GetIDForIdent('AlarmZoneDetailedState'), !$this->ReadPropertyBoolean('EnableAlarmZoneDetailedState'));

        //Door and window state
        IPS_SetHidden($this->GetIDForIdent('DoorWindowState'), !$this->ReadPropertyBoolean('EnableDoorWindowState'));

        //Motion detector state
        IPS_SetHidden($this->GetIDForIdent('MotionDetectorState'), !$this->ReadPropertyBoolean('EnableMotionDetectorState'));

        //Alarm state
        IPS_SetHidden($this->GetIDForIdent('AlarmState'), !$this->ReadPropertyBoolean('EnableAlarmState'));

        //Alerting sensor
        IPS_SetHidden($this->GetIDForIdent('AlertingSensor'), !$this->ReadPropertyBoolean('EnableAlertingSensor'));

        //Alarm siren state
        IPS_SetHidden($this->GetIDForIdent('AlarmSiren'), !$this->ReadPropertyBoolean('EnableAlarmSirenState'));

        //Alarm light state
        IPS_SetHidden($this->GetIDForIdent('AlarmLight'), !$this->ReadPropertyBoolean('EnableAlarmLightState'));

        //Alarm call state
        IPS_SetHidden($this->GetIDForIdent('AlarmCall'), !$this->ReadPropertyBoolean('EnableAlarmCallState'));

        ########## Timer

        $this->SetTimerInterval('StartActivation', 0);

        //State updates
        $mode = $this->GetValue('Mode');
        if ($mode == 0) {
            $this->ResetBlacklist();
            $this->CheckDoorWindowState($mode, false, false, false);
        } else {
            $this->CheckDoorWindowState($mode, true, true, false);
        }
        $this->CheckMotionDetectorState();

        ########## References

        //Delete all references
        foreach ($this->GetReferenceList() as $referenceID) {
            $this->UnregisterReference($referenceID);
        }

        //Delete all update messages
        foreach ($this->GetMessageList() as $senderID => $messages) {
            foreach ($messages as $message) {
                if ($message == VM_UPDATE) {
                    $this->UnregisterMessage($senderID, VM_UPDATE);
                }
            }
        }

        //Register references and update messages

        //Alarm protocol
        $id = $this->ReadPropertyInteger('AlarmProtocol');
        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
            $this->RegisterReference($id);
        }

        //Notification
        $id = $this->ReadPropertyInteger('Notification');
        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
            $this->RegisterReference($id);
        }

        //Door and window sensors
        $variables = json_decode($this->ReadPropertyString('DoorWindowSensors'), true);
        foreach ($variables as $variable) {
            if (!$variable['Use']) {
                continue;
            }
            //Primary condition
            if ($variable['PrimaryCondition'] != '') {
                $primaryCondition = json_decode($variable['PrimaryCondition'], true);
                if (array_key_exists(0, $primaryCondition)) {
                    if (array_key_exists(0, $primaryCondition[0]['rules']['variable'])) {
                        $id = $primaryCondition[0]['rules']['variable'][0]['variableID'];
                        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                            $this->RegisterReference($id);
                            $this->RegisterMessage($id, VM_UPDATE);
                        }
                    }
                }
            }
            //Secondary condition, multi
            if ($variable['SecondaryCondition'] != '') {
                $secondaryConditions = json_decode($variable['SecondaryCondition'], true);
                if (array_key_exists(0, $secondaryConditions)) {
                    if (array_key_exists('rules', $secondaryConditions[0])) {
                        $rules = $secondaryConditions[0]['rules']['variable'];
                        foreach ($rules as $rule) {
                            if (array_key_exists('variableID', $rule)) {
                                $id = $rule['variableID'];
                                if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                    $this->RegisterReference($id);
                                }
                            }
                        }
                    }
                }
            }
            //Alerting action
            if ($variable['UseAlertingAction']) {
                if ($variable['AlertingAction'] != '') {
                    $action = json_decode($variable['AlertingAction'], true);
                    if (array_key_exists('parameters', $action)) {
                        if (array_key_exists('TARGET', $action['parameters'])) {
                            $id = $action['parameters']['TARGET'];
                            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                $this->RegisterReference($id);
                            }
                        }
                    }
                }
            }
            //Alarm siren action
            if ($variable['UseAlarmSirenAction']) {
                if ($variable['AlarmSirenAction'] != '') {
                    $action = json_decode($variable['AlarmSirenAction'], true);
                    if (array_key_exists('parameters', $action)) {
                        if (array_key_exists('TARGET', $action['parameters'])) {
                            $id = $action['parameters']['TARGET'];
                            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                $this->RegisterReference($id);
                            }
                        }
                    }
                }
            }
            //Alarm light action
            if ($variable['UseAlarmLightAction']) {
                if ($variable['AlarmLightAction'] != '') {
                    $action = json_decode($variable['AlarmLightAction'], true);
                    if (array_key_exists('parameters', $action)) {
                        if (array_key_exists('TARGET', $action['parameters'])) {
                            $id = $action['parameters']['TARGET'];
                            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                $this->RegisterReference($id);
                            }
                        }
                    }
                }
            }
            //Alarm call action
            if ($variable['UseAlarmCallAction']) {
                if ($variable['AlarmCallAction'] != '') {
                    $action = json_decode($variable['AlarmCallAction'], true);
                    if (array_key_exists('parameters', $action)) {
                        if (array_key_exists('TARGET', $action['parameters'])) {
                            $id = $action['parameters']['TARGET'];
                            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                $this->RegisterReference($id);
                            }
                        }
                    }
                }
            }
        }

        //Motion Detectors
        $variables = json_decode($this->ReadPropertyString('MotionDetectors'), true);
        foreach ($variables as $variable) {
            if (!$variable['Use']) {
                continue;
            }
            //Primary condition
            if ($variable['PrimaryCondition'] != '') {
                $primaryCondition = json_decode($variable['PrimaryCondition'], true);
                if (array_key_exists(0, $primaryCondition)) {
                    if (array_key_exists(0, $primaryCondition[0]['rules']['variable'])) {
                        $id = $primaryCondition[0]['rules']['variable'][0]['variableID'];
                        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                            $this->RegisterReference($id);
                            $this->RegisterMessage($id, VM_UPDATE);
                        }
                    }
                }
            }
            //Secondary condition, multi
            if ($variable['SecondaryCondition'] != '') {
                $secondaryConditions = json_decode($variable['SecondaryCondition'], true);
                if (array_key_exists(0, $secondaryConditions)) {
                    if (array_key_exists('rules', $secondaryConditions[0])) {
                        $rules = $secondaryConditions[0]['rules']['variable'];
                        foreach ($rules as $rule) {
                            if (array_key_exists('variableID', $rule)) {
                                $id = $rule['variableID'];
                                if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                    $this->RegisterReference($id);
                                }
                            }
                        }
                    }
                }
            }
            //Alerting action
            if ($variable['UseAlertingAction']) {
                if ($variable['AlertingAction'] != '') {
                    $action = json_decode($variable['AlertingAction'], true);
                    if (array_key_exists('parameters', $action)) {
                        if (array_key_exists('TARGET', $action['parameters'])) {
                            $id = $action['parameters']['TARGET'];
                            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                $this->RegisterReference($id);
                            }
                        }
                    }
                }
            }
            //Alarm siren action
            if ($variable['UseAlarmSirenAction']) {
                if ($variable['AlarmSirenAction'] != '') {
                    $action = json_decode($variable['AlarmSirenAction'], true);
                    if (array_key_exists('parameters', $action)) {
                        if (array_key_exists('TARGET', $action['parameters'])) {
                            $id = $action['parameters']['TARGET'];
                            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                $this->RegisterReference($id);
                            }
                        }
                    }
                }
            }
            //Alarm light action
            if ($variable['UseAlarmLightAction']) {
                if ($variable['AlarmLightAction'] != '') {
                    $action = json_decode($variable['AlarmLightAction'], true);
                    if (array_key_exists('parameters', $action)) {
                        if (array_key_exists('TARGET', $action['parameters'])) {
                            $id = $action['parameters']['TARGET'];
                            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                $this->RegisterReference($id);
                            }
                        }
                    }
                }
            }
            //Alarm call action
            if ($variable['UseAlarmCallAction']) {
                if ($variable['AlarmCallAction'] != '') {
                    $action = json_decode($variable['AlarmCallAction'], true);
                    if (array_key_exists('parameters', $action)) {
                        if (array_key_exists('TARGET', $action['parameters'])) {
                            $id = $action['parameters']['TARGET'];
                            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                $this->RegisterReference($id);
                            }
                        }
                    }
                }
            }
        }

        $this->ValidateConfiguration();
    }

    public function Destroy()
    {
        //Never delete this line!
        parent::Destroy();

        //Delete profiles
        $profiles = ['Mode', 'AlarmZoneState', 'AlarmZoneDetailedState', 'AlarmState', 'DoorWindowState', 'MotionDetectorState', 'AlarmSirenStatus', 'AlarmLightStatus', 'AlarmCallStatus'];
        if (!empty($profiles)) {
            foreach ($profiles as $profile) {
                $profileName = self::MODULE_PREFIX . '.' . $this->InstanceID . '.' . $profile;
                if (IPS_VariableProfileExists($profileName)) {
                    IPS_DeleteVariableProfile($profileName);
                }
            }
        }
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        $this->SendDebug('MessageSink', 'Message from SenderID ' . $SenderID . ' with Message ' . $Message . "\r\n Data: " . print_r($Data, true), 0);
        switch ($Message) {
            case IPS_KERNELSTARTED:
                $this->KernelReady();
                break;

            case VM_UPDATE:

                //$Data[0] = actual value
                //$Data[1] = value changed
                //$Data[2] = last value
                //$Data[3] = timestamp actual value
                //$Data[4] = timestamp value changed
                //$Data[5] = timestamp last value

                if ($this->CheckMaintenance()) {
                    return;
                }

                //Check door and window variable
                $variables = json_decode($this->ReadPropertyString('DoorWindowSensors'), true);
                foreach ($variables as $variable) {
                    if (array_key_exists('PrimaryCondition', $variable)) {
                        $primaryCondition = json_decode($variable['PrimaryCondition'], true);
                        if ($primaryCondition != '') {
                            if (array_key_exists(0, $primaryCondition)) {
                                if (array_key_exists(0, $primaryCondition[0]['rules']['variable'])) {
                                    $id = $primaryCondition[0]['rules']['variable'][0]['variableID'];
                                    if ($id == $SenderID) {
                                        $valueChanged = 'false';
                                        if ($Data[1]) {
                                            $valueChanged = 'true';
                                        }
                                        $scriptText = self::MODULE_PREFIX . '_CheckDoorWindowSensorAlerting(' . $this->InstanceID . ', ' . $SenderID . ', ' . $valueChanged . ');';
                                        @IPS_RunScriptText($scriptText);
                                    }
                                }
                            }
                        }
                    }
                }

                //Check motion detector variable
                $variables = json_decode($this->ReadPropertyString('MotionDetectors'), true);
                foreach ($variables as $variable) {
                    if (array_key_exists('PrimaryCondition', $variable)) {
                        $primaryCondition = json_decode($variable['PrimaryCondition'], true);
                        if ($primaryCondition != '') {
                            if (array_key_exists(0, $primaryCondition)) {
                                if (array_key_exists(0, $primaryCondition[0]['rules']['variable'])) {
                                    $id = $primaryCondition[0]['rules']['variable'][0]['variableID'];
                                    if ($id == $SenderID) {
                                        $valueChanged = 'false';
                                        if ($Data[1]) {
                                            $valueChanged = 'true';
                                        }
                                        $scriptText = self::MODULE_PREFIX . '_CheckMotionDetectorAlerting(' . $this->InstanceID . ', ' . $SenderID . ', ' . $valueChanged . ');';
                                        @IPS_RunScriptText($scriptText);
                                    }
                                }
                            }
                        }
                    }
                }
                break;

        }
    }

    public function CreateAlarmProtocolInstance(): void
    {
        $id = @IPS_CreateInstance(self::ALARMPROTOCOL_MODULE_GUID);
        if (is_int($id)) {
            IPS_SetName($id, 'Alarmprotokoll');
            echo 'Instanz mit der ID ' . $id . ' wurde erfolgreich erstellt!';
        } else {
            echo 'Instanz konnte nicht erstellt werden!';
        }
    }

    public function CreateNotificationInstance(): void
    {
        $id = IPS_CreateInstance(self::NOTIFICATION_MODULE_GUID);
        if (is_int($id)) {
            IPS_SetName($id, 'Benachrichtigung');
            echo 'Instanz mit der ID ' . $id . ' wurde erfolgreich erstellt!';
        } else {
            echo 'Instanz konnte nicht erstellt werden!';
        }
    }

    #################### Request Action

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident) {

            case 'Active':
                $this->SetValue($Ident, $Value);
                break;

            case 'Mode':
                $id = $this->GetIDForIdent('Mode');
                $this->SelectProtectionMode($Value, (string) $id);
                break;

        }
    }

    #################### Private

    private function KernelReady(): void
    {
        $this->ApplyChanges();
    }

    private function ValidateConfiguration(): bool
    {
        $result = true;
        $status = 102;
        //Alarm protocol
        $id = $this->ReadPropertyInteger('AlarmProtocol');
        if ($id > 1 && @!IPS_ObjectExists($id)) {
            $result = false;
            $status = 200;
            $this->SendDebug(__FUNCTION__, 'Das zugewiesene Alarmprotokoll existiert nicht mehr!', 0);
            $this->LogMessage('ID ' . $this->InstanceID . ', ' . __FUNCTION__ . ', das zugewiesene Alarmprotokoll existiert nicht mehr!', KL_WARNING);
        }
        //Notification center
        $id = $this->ReadPropertyInteger('Notification');
        if ($id > 1 && @!IPS_ObjectExists($id)) {
            $result = false;
            $status = 200;
            $this->SendDebug(__FUNCTION__, 'Die zugewiesene Benachrichtigung existiert nicht mehr!', 0);
            $this->LogMessage('ID ' . $this->InstanceID . ',  ' . __FUNCTION__ . ', die zugewiesene Benachrichtigung existiert nicht mehr!', KL_WARNING);
        }
        $this->SetStatus($status);
        return $result;
    }

    private function CheckMaintenance(): bool
    {
        $result = false;
        if (!$this->GetValue('Active')) {
            $this->SendDebug(__FUNCTION__, 'Abbruch, die Instanz ist inaktiv!', 0);
            $result = true;
        }
        return $result;
    }
}