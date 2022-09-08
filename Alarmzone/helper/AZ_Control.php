<?php

/**
 * @project       _Alarmzone/Alarmzone
 * @file          AZ_Control.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

declare(strict_types=1);

trait AZ_Control
{
    /**
     * Starts the activation by the StartActivation timer if a delayed activation is used.
     *
     * @return void
     * @throws Exception
     */
    public function StartActivation(): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $this->SetTimerInterval('StartActivation', 0);
        if ($this->CheckMaintenance()) {
            return;
        }
        $mode = $this->GetValue('Mode');
        switch ($mode) {
            //Full protection
            case 1:
                $useProtectionModeName = 'UseFullProtectionMode';
                $abortActivationNotificationName = 'FullProtectionAbortActivationNotification';
                $protectionModeName = 'FullProtectionName';
                $activationWithOpenDoorWindowNotificationName = 'FullProtectionActivationWithOpenDoorWindowNotification';
                $activationNotificationName = 'FullProtectionActivationNotification';
                break;

            //Hull protection
            case 2:
                $useProtectionModeName = 'UseHullProtectionMode';
                $abortActivationNotificationName = 'HullProtectionAbortActivationNotification';
                $protectionModeName = 'HullProtectionName';
                $activationWithOpenDoorWindowNotificationName = 'HullProtectionActivationWithOpenDoorWindowNotification';
                $activationNotificationName = 'HullProtectionActivationNotification';
                break;

            //Partial protection
            case 3:
                $useProtectionModeName = 'UsePartialProtectionMode';
                $abortActivationNotificationName = 'PartialProtectionAbortActivationNotification';
                $protectionModeName = 'PartialProtectionName';
                $activationWithOpenDoorWindowNotificationName = 'PartialProtectionActivationWithOpenDoorWindowNotification';
                $activationNotificationName = 'PartialProtectionActivationNotification';
                break;

            default:
                return;
        }
        //Check if the mode is used for this alarm zone
        if (!$this->ReadPropertyBoolean($useProtectionModeName)) {
            $this->SendDebug(__FUNCTION__, 'Der Modus ' . $this->ReadPropertyString($protectionModeName) . ' ist deaktiviert und steht nicht zur Verfügung!', 0);
            $this->LogMessage('ID ' . $this->InstanceID . ', ' . __FUNCTION__ . ', der Modus ' . $this->ReadPropertyString($protectionModeName) . ' ist deaktiviert und steht nicht zur Verfügung!', KL_WARNING);
            return;
        }
        //Check activation
        $activation = $this->CheckDoorWindowState($mode, true, true, false);
        $activationStateText = 'Abbruch';
        if ($activation) {
            $activationStateText = 'OK';
        }
        $this->SendDebug(__FUNCTION__, 'Aktivierung: ' . $activationStateText, 0);
        //Abort activation
        if (!$activation) {
            $this->ResetValues();
            //Protocol
            $text = 'Die Aktivierung wurde durch die Sensorenprüfung abgebrochen! (ID ' . $this->GetIDForIdent('Mode') . ')';
            $logText = date('d.m.Y, H:i:s') . ', ' . $this->ReadPropertyString('Location') . ', ' . $this->ReadPropertyString('AlarmZoneName') . ', ' . $text;
            $this->UpdateAlarmProtocol($logText, 0);
            //Notification
            $this->SendNotification($abortActivationNotificationName, '');
            $notification = json_decode($this->ReadPropertyString($abortActivationNotificationName), true);
            if ($notification[0]['Use'] && $notification[0]['UseOpenDoorWindowNotification']) {
                IPS_Sleep(self::SLEEP_DELAY);
                $this->CheckDoorWindowState($mode, false, false, true);
            }
        }
        //Activate
        else {
            $state = 1; //armed
            $this->SetValue('AlarmZoneState', $state);
            if ($this->GetValue('DoorWindowState')) {
                $state = 3; //partial armed
            }
            $this->SetValue('AlarmZoneDetailedState', $state);
            //Protocol
            $text = $this->ReadPropertyString($protectionModeName) . ' aktiviert. (Einschaltverzögerung, ID ' . $this->GetIDForIdent('Mode') . ')';
            $logText = date('d.m.Y, H:i:s') . ', ' . $this->ReadPropertyString('Location') . ', ' . $this->ReadPropertyString('AlarmZoneName') . ', ' . $text;
            $this->UpdateAlarmProtocol($logText, 1);
            //Notification
            if ($state == 3) { //partial armed
                //Activation with open doors and windows
                $this->SendNotification($activationWithOpenDoorWindowNotificationName, '');
                $notification = json_decode($this->ReadPropertyString($activationWithOpenDoorWindowNotificationName), true);
                if ($notification[0]['Use'] && $notification[0]['UseOpenDoorWindowNotification']) {
                    IPS_Sleep(self::SLEEP_DELAY);
                    $this->CheckDoorWindowState($mode, false, false, true);
                }
            }
            if ($state == 1) {
                $this->SendNotification($activationNotificationName, '');
                $notification = json_decode($this->ReadPropertyString($activationNotificationName), true);
                if ($notification[0]['Use'] && $notification[0]['UseOpenDoorWindowNotification']) {
                    IPS_Sleep(self::SLEEP_DELAY);
                    $this->CheckDoorWindowState($mode, false, false, true);
                }
            }
        }
    }

    /**
     * Selects the protection mode.
     *
     * @param int $Mode
     * 0 =  Disarmed
     * 1 =  Full protection mode
     * 2 =  Hull protection mode
     * 3 =  Partial protection mode
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

            default:
                $modeText = 'Unbekannt';
        }
        $this->SendDebug(__FUNCTION__, 'Modus: ' . $modeText, 0);
        $this->SendDebug(__FUNCTION__, 'Sender: ' . $SenderID, 0);
        if ($this->CheckMaintenance()) {
            return false;
        }
        switch ($Mode) {
            //Disarm
            case 0:
                $this->ResetValues();
                $this->SetTimerInterval('StartActivation', 0);
                //Protocol
                $text = $this->ReadPropertyString('SystemName') . ' deaktiviert. (ID ' . $SenderID . ', ID ' . $this->GetIDForIdent('Mode') . ')';
                $logText = date('d.m.Y, H:i:s') . ', ' . $this->ReadPropertyString('Location') . ', ' . $this->ReadPropertyString('AlarmZoneName') . ', ' . $text;
                $this->UpdateAlarmProtocol($logText, 1);
                //Notification
                $this->SendNotification('DeactivationNotification', '');
                $this->CheckDoorWindowState($Mode, false, false, false);
                return true;

            //Full protection mode
            case 1:
                $useProtectionModeName = 'UseFullProtectionMode';
                $activationDelayName = 'FullProtectionModeActivationDelay';
                $protectionModeName = 'FullProtectionName';
                $delayedActivationNotificationName = 'FullProtectionDelayedActivationNotification';
                $abortActivationNotificationName = 'FullProtectionAbortActivationNotification';
                $activationNotificationName = 'FullProtectionActivationNotification';
                $activationWithOpenDoorWindowNotificationName = 'FullProtectionActivationWithOpenDoorWindowNotification';
                break;

            //Hull protection mode
            case 2:
                $useProtectionModeName = 'UseHullProtectionMode';
                $activationDelayName = 'HullProtectionModeActivationDelay';
                $protectionModeName = 'HullProtectionName';
                $delayedActivationNotificationName = 'HullProtectionDelayedActivationNotification';
                $abortActivationNotificationName = 'HullProtectionAbortActivationNotification';
                $activationNotificationName = 'HullProtectionActivationNotification';
                $activationWithOpenDoorWindowNotificationName = 'HullProtectionActivationWithOpenDoorWindowNotification';
                break;

            //Partial protection mode
            case 3:
                $useProtectionModeName = 'UsePartialProtectionMode';
                $activationDelayName = 'PartialProtectionModeActivationDelay';
                $protectionModeName = 'PartialProtectionName';
                $delayedActivationNotificationName = 'PartialProtectionDelayedActivationNotification';
                $abortActivationNotificationName = 'PartialProtectionAbortActivationNotification';
                $activationNotificationName = 'PartialProtectionActivationNotification';
                $activationWithOpenDoorWindowNotificationName = 'PartialProtectionActivationWithOpenDoorWindowNotification';
                break;

            default:
                return false;
        }
        //Check if the mode is used for this alarm zone
        if (!$this->ReadPropertyBoolean($useProtectionModeName)) {
            $this->SendDebug(__FUNCTION__, 'Der Modus ' . $modeText . ' ist deaktiviert und steht nicht zur Verfügung!', 0);
            $this->LogMessage('ID ' . $this->InstanceID . ', ' . __FUNCTION__ . ', der Modus ' . $modeText . ' ist deaktiviert und steht nicht zur Verfügung!', KL_WARNING);
            return false;
        }
        $result = true;
        $this->SetValue('Mode', $Mode);
        $this->SetValue('AlarmState', 0);
        $this->SetValue('AlertingSensor', '');
        $this->SetValue('AlarmSiren', false);
        $this->SetValue('AlarmLight', false);
        $this->SetValue('AlarmCall', false);
        $this->ResetBlacklist();
        //Check activation delay
        $activationDelay = $this->ReadPropertyInteger($activationDelayName);
        if ($activationDelay > 0) {
            //Check actual state only
            $this->CheckDoorWindowState($Mode, false, false, false);
            //Activate timer, timer will execute the StartActivation methode
            $this->SetTimerInterval('StartActivation', $activationDelay * 1000);
            $stateValue = 2; //delayed armed
            $this->SetValue('AlarmZoneState', $stateValue);
            if ($this->GetValue('DoorWindowState')) {
                $stateValue = 4; //delayed partial armed
            }
            $this->SetValue('AlarmZoneDetailedState', $stateValue);
            //Protocol
            $text = $this->ReadPropertyString($protectionModeName) . ' wird in ' . $activationDelay . ' Sekunden automatisch aktiviert. (ID ' . $SenderID . ', ID ' . $this->GetIDForIdent('Mode') . ')';
            $logText = date('d.m.Y, H:i:s') . ', ' . $this->ReadPropertyString('Location') . ', ' . $this->ReadPropertyString('AlarmZoneName') . ', ' . $text;
            $this->UpdateAlarmProtocol($logText, 0);
            //Notification
            $this->SendNotification($delayedActivationNotificationName, (string) $activationDelay);
            $notification = json_decode($this->ReadPropertyString($delayedActivationNotificationName), true);
        }
        //Immediate activation
        else {
            $activation = $this->CheckDoorWindowState($Mode, false, false, false);
            //Abort activation
            if (!$activation) {
                $result = false;
                $this->ResetValues();
                $this->SetTimerInterval('StartActivation', 0);
                //Protocol
                $text = 'Die Aktivierung wurde durch die Sensorenprüfung abgebrochen! (ID ' . $this->GetIDForIdent('Mode') . ')';
                $logText = date('d.m.Y, H:i:s') . ', ' . $this->ReadPropertyString('Location') . ', ' . $this->ReadPropertyString('AlarmZoneName') . ', ' . $text;
                $this->UpdateAlarmProtocol($logText, 0);
                //Notification
                $this->SendNotification($abortActivationNotificationName, '');
                $notification = json_decode($this->ReadPropertyString($abortActivationNotificationName), true);
            }
            //Activate
            else {
                $this->CheckDoorWindowState($Mode, true, true, false); //adds a sensor of an open door or window to the blacklist
                $state = 1; //armed
                $this->SetValue('AlarmZoneState', $state);
                $doorWindowState = $this->GetValue('DoorWindowState');
                if ($doorWindowState) {
                    $state = 3; //partial armed
                }
                $this->SetValue('AlarmZoneDetailedState', $state);
                //Protocol
                $text = $this->ReadPropertyString($protectionModeName) . ' aktiviert. (ID ' . $SenderID . ', ID ' . $this->GetIDForIdent('Mode') . ')';
                $logText = date('d.m.Y, H:i:s') . ', ' . $this->ReadPropertyString('Location') . ', ' . $this->ReadPropertyString('AlarmZoneName') . ', ' . $text;
                $this->UpdateAlarmProtocol($logText, 1);
                //Notification
                if (!$doorWindowState) { //Closed
                    $this->SendNotification($activationNotificationName, '');
                    $notification = json_decode($this->ReadPropertyString($activationNotificationName), true);
                } else { //Opened
                    $this->SendNotification($activationWithOpenDoorWindowNotificationName, '');
                    $notification = json_decode($this->ReadPropertyString($activationWithOpenDoorWindowNotificationName), true);
                }
            }
        }
        if ($notification[0]['Use'] && $notification[0]['UseOpenDoorWindowNotification']) {
            IPS_Sleep(self::SLEEP_DELAY);
            $this->CheckDoorWindowState($Mode, false, false, true);
        }
        return $result;
    }

    #################### Private

    /**
     * Resets the values of the alarm zone.
     * @return void
     */
    private function ResetValues(): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $this->ResetBlacklist();
        $this->SetValue('Mode', 0);
        $this->SetValue('AlarmZoneState', 0);
        $this->SetValue('AlarmZoneDetailedState', 0);
        $this->SetValue('AlarmState', 0);
        $this->SetValue('AlertingSensor', '');
        $this->SetValue('AlarmSiren', false);
        $this->SetValue('AlarmLight', false);
        $this->SetValue('AlarmCall', false);
    }
}