<?php

/**
 * @project       _Alarmzone/Alarmzonensteuerung
 * @file          module.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

declare(strict_types=1);

include_once __DIR__ . '/helper/AZST_autoload.php';

class Alarmzonensteuerung extends IPSModule
{
    //Helper
    use AZST_Config;
    use AZST_Control;
    use AZST_States;

    //Constants
    private const MODULE_NAME = 'Alarmzonensteuerung';
    private const MODULE_PREFIX = 'AZST';
    private const MODULE_VERSION = '7.0-1, 08.09.2022';
    private const ALARMZONE_MODULE_GUID = '{127AB08D-CD10-801D-D419-442CDE6E5C61}';

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        ########## Properties

        //Info
        $this->RegisterPropertyString('Note', '');

        //Designations
        $this->RegisterPropertyString('Location', '');

        //Functions
        $this->RegisterPropertyBoolean('EnableActive', false);
        $this->RegisterPropertyBoolean('EnableLocation', true);
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
        $this->RegisterPropertyBoolean('UseIndividualProtectionMode', false);
        $this->RegisterPropertyString('IndividualProtectionName', 'Individualschutz');
        $this->RegisterPropertyString('IndividualProtectionIcon', 'Eyes');
        $this->RegisterPropertyInteger('IndividualProtectionColor', 16749824);
        $this->RegisterPropertyBoolean('EnableSystemState', true);
        $this->RegisterPropertyBoolean('EnableSystemDetailedState', false);
        $this->RegisterPropertyBoolean('EnableDoorWindowState', true);
        $this->RegisterPropertyBoolean('EnableMotionDetectorState', true);
        $this->RegisterPropertyBoolean('EnableAlarmState', true);
        $this->RegisterPropertyBoolean('EnableAlertingSensor', true);
        $this->RegisterPropertyBoolean('EnableAlarmSirenState', false);
        $this->RegisterPropertyBoolean('EnableAlarmLightState', false);
        $this->RegisterPropertyBoolean('EnableAlarmCallState', false);

        //Alarm zones
        $this->RegisterPropertyString('AlarmZones', '[]');

        //Trigger
        $this->RegisterPropertyString('ProtectionMode', '[]');
        $this->RegisterPropertyString('SystemState', '[]');
        $this->RegisterPropertyString('SystemDetailedState', '[]');
        $this->RegisterPropertyString('AlarmState', '[]');
        $this->RegisterPropertyString('AlertingSensor', '[]');
        $this->RegisterPropertyString('DoorWindowState', '[]');
        $this->RegisterPropertyString('MotionDetectorState', '[]');
        $this->RegisterPropertyString('AlarmSiren', '[]');
        $this->RegisterPropertyString('AlarmLight', '[]');
        $this->RegisterPropertyString('AlarmCall', '[]');

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

        //System state
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.SystemState';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 1);
        }
        IPS_SetVariableProfileIcon($profile, '');
        IPS_SetVariableProfileAssociation($profile, 0, 'Unscharf', 'IPS', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'Scharf', 'Warning', 0xFF0000);
        IPS_SetVariableProfileAssociation($profile, 2, 'Verzögert Scharf', 'Clock', 0xFFFF00);
        $this->RegisterVariableInteger('SystemState', 'Systemstatus', $profile, 40);

        //System detailed state
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.SystemDetailedState';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 1);
        }
        IPS_SetVariableProfileIcon($profile, '');
        IPS_SetVariableProfileAssociation($profile, 0, 'Unscharf', 'IPS', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'Scharf', 'Warning', 0xFF0000);
        IPS_SetVariableProfileAssociation($profile, 2, 'Verzögert Scharf', 'Clock', 0xFFFF00);
        IPS_SetVariableProfileAssociation($profile, 3, 'Teilscharf', 'Warning', 0xFFFF00);
        IPS_SetVariableProfileAssociation($profile, 4, 'Verzögert Teilscharf', 'Warning', 0xFFFF00);
        $this->RegisterVariableInteger('SystemDetailedState', 'Detaillierter Systemstatus', $profile, 50);

        //Door and window state
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.DoorWindowState';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 0);
        }
        IPS_SetVariableProfileIcon($profile, 'Window');
        IPS_SetVariableProfileAssociation($profile, 0, 'Geschlossen', '', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'Geöffnet', '', 0xFF0000);
        $this->RegisterVariableBoolean('DoorWindowState', 'Tür und Fensterstatus', $profile, 60);

        //Motion detector state
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.MotionDetectorState';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 0);
        }
        IPS_SetVariableProfileIcon($profile, 'Motion');
        IPS_SetVariableProfileAssociation($profile, 0, 'OK', '', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'Bewegung erkannt', '', 0xFF0000);
        $this->RegisterVariableBoolean('MotionDetectorState', 'Bewegungsmelderstatus', $profile, 70);

        //Alarm state
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.AlarmState';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 1);
        }
        IPS_SetVariableProfileIcon($profile, '');
        IPS_SetVariableProfileAssociation($profile, 0, 'OK', 'Warning', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'Alarm', 'Alert', 0xFF0000);
        $this->RegisterVariableInteger('AlarmState', 'Alarmstatus', $profile, 80);

        //Alerting sensor
        $id = @$this->GetIDForIdent('AlertingSensor');
        $this->RegisterVariableString('AlertingSensor', 'Auslösender Alarmsensor', '', 90);
        $this->SetValue('AlertingSensor', $this->ReadPropertyString('AlertingSensor'));
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
        $this->RegisterVariableBoolean('AlarmSiren', 'Alarmsirene', $profile, 100);

        //Alarm light
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.AlarmLightStatus';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 0);
        }
        IPS_SetVariableProfileIcon($profile, 'Bulb');
        IPS_SetVariableProfileAssociation($profile, 0, 'Aus', '', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'An', '', 0xFF0000);
        $this->RegisterVariableBoolean('AlarmLight', 'Alarmbeleuchtung', $profile, 110);

        //Alarm call
        $profile = self::MODULE_PREFIX . '.' . $this->InstanceID . '.AlarmCallStatus';
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 0);
        }
        IPS_SetVariableProfileIcon($profile, 'Mobile');
        IPS_SetVariableProfileAssociation($profile, 0, 'Aus', '', 0x00FF00);
        IPS_SetVariableProfileAssociation($profile, 1, 'An', '', 0xFF0000);
        $this->RegisterVariableBoolean('AlarmCall', 'Alarmanruf', $profile, 120);

        ########## Attribute

        $this->RegisterAttributeBoolean('DisableUpdateMode', false);
    }

    public function ApplyChanges()
    {
        //Wait until IP-Symcon is started
        $this->RegisterMessage(0, IPS_KERNELSTARTED);

        //Never delete this line!
        parent::ApplyChanges();

        // Check runlevel
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
        if ($this->ReadPropertyBoolean('UseIndividualProtectionMode')) {
            IPS_SetVariableProfileAssociation($profile, 4, $this->ReadPropertyString('IndividualProtectionName'), $this->ReadPropertyString('IndividualProtectionIcon'), $this->ReadPropertyInteger('IndividualProtectionColor'));
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

        //Mode
        IPS_SetHidden($this->GetIDForIdent('Mode'), !$this->ReadPropertyBoolean('EnableMode'));

        //System state
        IPS_SetHidden($this->GetIDForIdent('SystemState'), !$this->ReadPropertyBoolean('EnableSystemState'));

        //System detailed state
        IPS_SetHidden($this->GetIDForIdent('SystemDetailedState'), !$this->ReadPropertyBoolean('EnableSystemDetailedState'));

        //Door and window state
        IPS_SetHidden($this->GetIDForIdent('DoorWindowState'), !$this->ReadPropertyBoolean('EnableDoorWindowState'));

        //Motion detector state
        IPS_SetHidden($this->GetIDForIdent('MotionDetectorState'), !$this->ReadPropertyBoolean('EnableMotionDetectorState'));

        //Alarm state
        IPS_SetHidden($this->GetIDForIdent('AlarmState'), !$this->ReadPropertyBoolean('EnableAlarmState'));

        //Alarm siren state
        IPS_SetHidden($this->GetIDForIdent('AlarmSiren'), !$this->ReadPropertyBoolean('EnableAlarmSirenState'));

        //Alarm light state
        IPS_SetHidden($this->GetIDForIdent('AlarmLight'), !$this->ReadPropertyBoolean('EnableAlarmLightState'));

        //Alarm call state
        IPS_SetHidden($this->GetIDForIdent('AlarmCall'), !$this->ReadPropertyBoolean('EnableAlarmCallState'));

        //Alerting sensor
        IPS_SetHidden($this->GetIDForIdent('AlertingSensor'), !$this->ReadPropertyBoolean('EnableAlertingSensor'));

        ########## Attribute

        $this->WriteAttributeBoolean('DisableUpdateMode', false);

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
        $alarmZones = json_decode($this->ReadPropertyString('AlarmZones'), true);
        foreach ($alarmZones as $alarmZone) {
            if (!$alarmZone['Use']) {
                continue;
            }
            $id = $alarmZone['ID'];
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                $this->RegisterReference($id);
            }
        }
        $properties = [
            'ProtectionMode',
            'SystemState',
            'SystemDetailedState',
            'AlarmState',
            'AlertingSensor',
            'DoorWindowState',
            'MotionDetectorState',
            'AlarmSiren',
            'AlarmLight',
            'AlarmCall'];
        foreach ($properties as $property) {
            $variables = json_decode($this->ReadPropertyString($property), true);
            foreach ($variables as $variable) {
                if ($variable['Use']) {
                    $id = $variable['ID'];
                    if ($id > 1 && IPS_ObjectExists($id)) { //0 = main category, 1 = none
                        $this->RegisterReference($id);
                        $this->RegisterMessage($id, VM_UPDATE);
                    }
                }
            }
        }
        $this->UpdateStates();
    }

    public function Destroy()
    {
        //Never delete this line!
        parent::Destroy();

        //Delete profiles
        $profiles = ['Mode', 'SystemState', 'SystemDetailedState', 'AlarmState', 'DoorWindowState', 'MotionDetectorState', 'AlarmSirenStatus', 'AlarmLightStatus', 'AlarmCallStatus'];
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

                //Check trigger variable
                $properties = [
                    'ProtectionMode',
                    'SystemState',
                    'SystemDetailedState',
                    'AlarmState',
                    'AlertingSensor',
                    'DoorWindowState',
                    'MotionDetectorState',
                    'AlarmSiren',
                    'AlarmLight',
                    'AlarmCall'];
                foreach ($properties as $property) {
                    $variables = json_decode($this->ReadPropertyString($property), true);
                    if (!empty($variables)) {
                        if (in_array($SenderID, array_column($variables, 'ID'))) {
                            $scriptText = self::MODULE_PREFIX . '_Update' . $property . '(' . $this->InstanceID . ');';
                            $this->SendDebug(__FUNCTION__, 'Methode: ' . $scriptText, 0);
                            @IPS_RunScriptText($scriptText);
                        }
                    }
                }
                break;

        }
    }

    public function CreateAlarmZoneInstance(): void
    {
        $id = @IPS_CreateInstance(self::ALARMZONE_MODULE_GUID);
        if (is_int($id)) {
            IPS_SetName($id, 'Alarmzone');
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