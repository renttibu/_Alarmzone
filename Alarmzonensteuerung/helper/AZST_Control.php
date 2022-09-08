<?php

/**
 * @project       _Alarmzone/Alarmzonensteuerung
 * @file          AZST_Control.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection PhpUndefinedFunctionInspection */
/** @noinspection PhpUnused */

declare(strict_types=1);

trait AZST_Control
{
    /**
     * Determines the variables of the alarm zones.
     *
     * @return void
     * @throws Exception
     */
    public function DetermineAlarmZoneVariables(): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $alarmZones = json_decode($this->ReadPropertyString('AlarmZones'), true);
        if (empty($alarmZones)) {
            return;
        }
        $protectionMode = [];
        $systemState = [];
        $systemDetailedState = [];
        $alarmState = [];
        $alertingSensor = [];
        $doorWindowState = [];
        $motionDetectorState = [];
        $alarmSiren = [];
        $alarmLight = [];
        $alarmCall = [];
        foreach ($alarmZones as $alarmZone) {
            if (!$alarmZone['Use']) {
                continue;
            }
            $id = $alarmZone['ID'];
            if ($id <= 1 || !@IPS_ObjectExists($id)) {
                continue;
            }
            $children = IPS_GetChildrenIDs($id);
            if (empty($children)) {
                continue;
            }
            $description = $alarmZone['Designation'];
            foreach ($children as $child) {
                if ($child <= 1 || !@IPS_ObjectExists($child)) {
                    continue;
                }
                $ident = IPS_GetObject($child)['ObjectIdent'];
                switch ($ident) {
                    case 'Mode':
                        $protectionMode[] = ['Use' => true, 'ID' => $child, 'Designation' => $description];
                        break;

                    case 'AlarmZoneState':
                        $systemState[] = ['Use' => true, 'ID' => $child, 'Designation' => $description];
                        break;

                    case 'AlarmZoneDetailedState':
                        $systemDetailedState[] = ['Use' => true, 'ID' => $child, 'Designation' => $description];
                        break;

                    case 'AlarmState':
                        $alarmState[] = ['Use' => true, 'ID' => $child, 'Designation' => $description];
                        break;

                    case 'AlertingSensor':
                        $alertingSensor[] = ['Use' => true, 'ID' => $child, 'Designation' => $description];
                        break;

                    case 'DoorWindowState':
                        $doorWindowState[] = ['Use' => true, 'ID' => $child, 'Designation' => $description];
                        break;

                    case 'MotionDetectorState':
                        $motionDetectorState[] = ['Use' => true, 'ID' => $child, 'Designation' => $description];
                        break;

                    case 'AlarmSiren':
                        $alarmSiren[] = ['Use' => true, 'ID' => $child, 'Designation' => $description];
                        break;

                    case 'AlarmLight':
                        $alarmLight[] = ['Use' => true, 'ID' => $child, 'Designation' => $description];
                        break;

                    case 'AlarmCall':
                        $alarmCall[] = ['Use' => true, 'ID' => $child, 'Designation' => $description];
                        break;

                }
            }
        }
        @IPS_SetProperty($this->InstanceID, 'ProtectionMode', json_encode($protectionMode));
        @IPS_SetProperty($this->InstanceID, 'SystemState', json_encode($systemState));
        @IPS_SetProperty($this->InstanceID, 'SystemDetailedState', json_encode($systemDetailedState));
        @IPS_SetProperty($this->InstanceID, 'AlarmState', json_encode($alarmState));
        @IPS_SetProperty($this->InstanceID, 'AlertingSensor', json_encode($alertingSensor));
        @IPS_SetProperty($this->InstanceID, 'DoorWindowState', json_encode($doorWindowState));
        @IPS_SetProperty($this->InstanceID, 'MotionDetectorState', json_encode($motionDetectorState));
        @IPS_SetProperty($this->InstanceID, 'AlarmSiren', json_encode($alarmSiren));
        @IPS_SetProperty($this->InstanceID, 'AlarmLight', json_encode($alarmLight));
        @IPS_SetProperty($this->InstanceID, 'AlarmCall', json_encode($alarmCall));
        if (@IPS_HasChanges($this->InstanceID)) {
            @IPS_ApplyChanges($this->InstanceID);
        }
        echo 'Die Variablen wurden erfolgreich ermittelt!';
    }

    /**
     * Selects the protection mode.
     *
     * @param int $Mode
     * 0 =  Disarmed
     * 1 =  Full protection mode
     * 2 =  Hull protection mode
     * 3 =  Partial protection mode
     * 4 =  Individual protection mode
     *
     * @param string $SenderID
     *
     * @return bool
     * false =  An error occurred
     * true =   Successful
     *
     * @throws Exception
     */
    public function SelectProtectionMode(int $Mode, string $SenderID): bool
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        switch ($Mode) {
            case 0:
                $modeText = $this->ReadPropertyString('DisarmedName');
                break;

            case 1:
                $modeText = $this->ReadPropertyString('FullProtectionName');
                break;

            case 2:
                $modeText = $this->ReadPropertyString('HullProtectionName');
                break;

            case 3:
                $modeText = $this->ReadPropertyString('PartialProtectionName');
                break;

            case 4:
                $modeText = $this->ReadPropertyString('IndividualProtectionName');
                break;

            default:
                $modeText = 'Unbekannt';
        }
        $this->SendDebug(__FUNCTION__, 'Modus: ' . $modeText, 0);
        $this->SendDebug(__FUNCTION__, 'Sender: ' . $SenderID, 0);
        if ($this->CheckMaintenance()) {
            return false;
        }
        $alarmZones = json_decode($this->ReadPropertyString('AlarmZones'), true);
        if (empty($alarmZones)) {
            return false;
        }
        $this->WriteAttributeBoolean('DisableUpdateMode', true);
        $result = true;
        if ($Mode >= 0 && $Mode <= 4) {
            $this->SetValue('Mode', $Mode);
        }
        //Check if the mode is used
        $check = true;
        switch ($Mode) {
            case 1: //Full protection  mode
                $useProtectionModeName = 'UseFullProtectionMode';
                break;

            case 2: //Hull protection  mode
                $useProtectionModeName = 'UseHullProtectionMode';
                break;

            case 3: //Partial protection  mode
                $useProtectionModeName = 'UsePartialProtectionMode';
                break;

            case 4: //Individual protection  mode
                $useProtectionModeName = 'UseIndividualProtectionMode';
                break;

            default:
                $useProtectionModeName = '';
                $check = false;

        }
        if ($check) {
            if (!$this->ReadPropertyBoolean($useProtectionModeName)) {
                $this->SendDebug(__FUNCTION__, 'Der Modus ist deaktiviert und steht nicht zur Verfügung!', 0);
                $this->LogMessage('ID ' . $this->InstanceID . ', ' . __FUNCTION__ . ', der Modus ' . $modeText . ' ist deaktiviert und steht nicht zur Verfügung!', KL_WARNING);
                return false;
            }
        }
        //Select protection mode
        switch ($Mode) {
            case 0: //Disarm
            case 1: //Full protection mode
            case 2: //Hull protection mode
            case 3: //Partial protection mode
                foreach ($alarmZones as $alarmZone) {
                    if (!$alarmZone['Use']) {
                        continue;
                    }
                    $id = $alarmZone['ID'];
                    if ($id == 0 || @!IPS_ObjectExists($id)) { //0 = main category, 1 = none
                        continue;
                    }
                    $response = @AZ_SelectProtectionMode($id, $Mode, $SenderID);
                    if (!$response) {
                        $result = false;
                    }
                }
                break;

            case 4: //Individual protection mode
                foreach ($alarmZones as $alarmZone) {
                    if (!$alarmZone['Use']) {
                        continue;
                    }
                    $id = $alarmZone['ID'];
                    if ($id == 0 || @!IPS_ObjectExists($id)) { //0 = main category, 1 = none
                        continue;
                    }
                    switch ($alarmZone['IndividualProtectionMode']) {
                        case 0: //Disarmed
                            $response = @AZ_SelectProtectionMode($id, 0, $SenderID);
                            if (!$response) {
                                $result = false;
                            }
                            break;

                        case 1: //Full protection mode
                            $response = @AZ_SelectProtectionMode($id, 1, $SenderID);
                            if (!$response) {
                                $result = false;
                            }
                            break;

                        case 2: //Hull protection mode
                            $response = @AZ_SelectProtectionMode($id, 2, $SenderID);
                            if (!$response) {
                                $result = false;
                            }
                            break;

                        case 3: //Partial protection mode
                            $response = @AZ_SelectProtectionMode($id, 3, $SenderID);
                            if (!$response) {
                                $result = false;
                            }
                            break;

                        case 4: //No function
                            $this->SendDebug(__FUNCTION__, 'Alarmzone ID ' . $id . ' hat keinen Individualschutz!', 0);
                            break;

                    }
                }
                break;

            default:
                $result = false;

        }
        $this->WriteAttributeBoolean('DisableUpdateMode', false);
        $this->UpdateSystemState();
        $this->UpdateSystemDetailedState();
        return $result;
    }
}