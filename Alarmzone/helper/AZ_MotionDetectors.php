<?php

/**
 * @project       _Alarmzone/Alarmzone
 * @file          AZ_MotionDetectors.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection PhpUndefinedFunctionInspection */
/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

declare(strict_types=1);

trait AZ_MotionDetectors
{
    /**
     * Determines automatically the variables of all existing HomeMatic and Homematic IP motion detectors.
     *
     * @return void
     * @throws Exception
     */
    public function DetermineMotionDetectorVariables(): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        //Determine variables first
        $determinedVariables = [];
        foreach (@IPS_GetInstanceListByModuleID(self::HOMEMATIC_DEVICE_GUID) as $instanceID) {
            $childrenIDs = @IPS_GetChildrenIDs($instanceID);
            foreach ($childrenIDs as $childrenID) {
                $match = false;
                $object = @IPS_GetObject($childrenID);
                if ($object['ObjectIdent'] == 'MOTION') {
                    $match = true;
                }
                if ($match) {
                    if ($object['ObjectType'] == 2) {
                        $name = strstr(@IPS_GetName($instanceID), ':', true);
                        if (!$name) {
                            $name = @IPS_GetName($instanceID);
                        }
                        $value = true;
                        if (IPS_GetVariable($childrenID)['VariableType'] == 1) {
                            $value = 1;
                        }
                        $primaryCondition[0] = [
                            'id'        => 0,
                            'parentID'  => 0,
                            'operation' => 0,
                            'rules'     => [
                                'variable' => [
                                    '0' => [
                                        'id'         => 0,
                                        'variableID' => $childrenID,
                                        'comparison' => 0,
                                        'value'      => $value,
                                        'type'       => 0
                                    ]
                                ],
                                'date'         => [],
                                'time'         => [],
                                'dayOfTheWeek' => []
                            ]
                        ];
                        $determinedVariables[] = [
                            'Use'                         => true,
                            'Designation'                 => $name,
                            'UseMultipleAlerts'           => false,
                            'PrimaryCondition'            => json_encode($primaryCondition),
                            'SecondaryCondition'          => '[]',
                            'FullProtectionModeActive'    => true,
                            'HullProtectionModeActive'    => false,
                            'PartialProtectionModeActive' => false,
                            'UseAlarmProtocol'            => true,
                            'UseNotification'             => true,
                            'UseAlarmSiren'               => true,
                            'UseAlarmLight'               => false,
                            'UseAlarmCall'                => false,
                            'UseAlertingAction'           => false,
                            'AlertingAction'              => '[]',
                            'UseAlarmSirenAction'         => false,
                            'AlarmSirenAction'            => '[]',
                            'UseAlarmLightAction'         => false,
                            'AlarmLightAction'            => '[]',
                            'UseAlarmCallAction'          => false,
                            'AlarmCallAction'             => '[]'];
                    }
                }
            }
        }

        //Get already listed variables
        $listedVariables = json_decode($this->ReadPropertyString('MotionDetectors'), true);
        foreach ($determinedVariables as $determinedVariable) {
            if (array_key_exists('PrimaryCondition', $determinedVariable)) {
                $primaryCondition = json_decode($determinedVariable['PrimaryCondition'], true);
                if ($primaryCondition != '') {
                    if (array_key_exists(0, $primaryCondition)) {
                        if (array_key_exists(0, $primaryCondition[0]['rules']['variable'])) {
                            $determinedVariableID = $primaryCondition[0]['rules']['variable'][0]['variableID'];
                            if ($determinedVariableID > 1 && @IPS_ObjectExists($determinedVariableID)) {
                                //Check variable id with already listed variable ids
                                $add = true;
                                foreach ($listedVariables as $listedVariable) {
                                    if (array_key_exists('PrimaryCondition', $listedVariable)) {
                                        $primaryCondition = json_decode($listedVariable['PrimaryCondition'], true);
                                        if ($primaryCondition != '') {
                                            if (array_key_exists(0, $primaryCondition)) {
                                                if (array_key_exists(0, $primaryCondition[0]['rules']['variable'])) {
                                                    $listedVariableID = $primaryCondition[0]['rules']['variable'][0]['variableID'];
                                                    if ($listedVariableID > 1 && @IPS_ObjectExists($determinedVariableID)) {
                                                        if ($determinedVariableID == $listedVariableID) {
                                                            $add = false;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                //Add new variable to already listed variables
                                if ($add) {
                                    $listedVariables[] = $determinedVariable;
                                }
                            }
                        }
                    }
                }
            }
        }
        //Sort variables by name
        array_multisort(array_column($listedVariables, 'Designation'), SORT_ASC, $listedVariables);
        @IPS_SetProperty($this->InstanceID, 'MotionDetectors', json_encode(array_values($listedVariables)));
        if (@IPS_HasChanges($this->InstanceID)) {
            @IPS_ApplyChanges($this->InstanceID);
        }
        echo 'Die Bewegungsmelder wurden erfolgreich hinzugefügt!';
    }

    /**
     * Checks the alerting of a motion detector.
     *
     * @param int $SenderID
     *
     * @param bool $ValueChanged
     * false =  Same value
     * true =   New value
     *
     * @return bool
     * false =  No alert
     * true =   Alert
     *
     * @throws Exception
     */
    public function CheckMotionDetectorAlerting(int $SenderID, bool $ValueChanged): bool
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $this->SendDebug(__FUNCTION__, 'Sender: ' . $SenderID, 0);
        $valueChangedText = 'nicht ';
        if ($ValueChanged) {
            $valueChangedText = '';
        }
        $this->SendDebug(__FUNCTION__, 'Der Wert hat sich ' . $valueChangedText . 'geändert', 0);
        if ($this->CheckMaintenance()) {
            return false;
        }
        $this->CheckMotionDetectorState();
        $variables = json_decode($this->ReadPropertyString('MotionDetectors'), true);
        if (empty($variables)) {
            return false;
        }
        $result = false;
        foreach ($variables as $variable) {
            if (array_key_exists('PrimaryCondition', $variable)) {
                $primaryCondition = json_decode($variable['PrimaryCondition'], true);
                if ($primaryCondition != '') {
                    if (array_key_exists(0, $primaryCondition)) {
                        if (array_key_exists(0, $primaryCondition[0]['rules']['variable'])) {
                            $id = $primaryCondition[0]['rules']['variable'][0]['variableID'];
                            if ($id == $SenderID) {
                                if (!$variable['Use']) {
                                    $this->SendDebug(__FUNCTION__, 'Abbruch, der Bewegungsmelder ist nicht aktiviert!', 0);
                                    return false;
                                }
                                if (!$variable['UseMultipleAlerts'] && !$ValueChanged) {
                                    $this->SendDebug(__FUNCTION__, 'Abbruch, die Mehrfachauslösung ist nicht aktiviert!', 0);
                                    return false;
                                }
                                if (IPS_IsConditionPassing($variable['PrimaryCondition']) && IPS_IsConditionPassing($variable['SecondaryCondition'])) {
                                    $this->SendDebug(__FUNCTION__, 'Die Bedingungen wurden erfüllt. Es wurde eine Bewegung erkannt!', 0);
                                    $alerting = false;
                                    switch ($this->GetValue('AlarmZoneDetailedState')) {
                                        case 1: //armed
                                        case 3: //partial armed
                                            $mode = $this->GetValue('Mode');
                                            switch ($mode) {
                                                //Check if sensor is activated for full protection mode
                                                case 1:
                                                    if ($variable['FullProtectionModeActive']) {
                                                        $alerting = true;
                                                    }
                                                    break;

                                                //Check if sensor is activated for hull protection mode
                                                case 2:
                                                    if ($variable['HullProtectionModeActive']) {
                                                        $alerting = true;
                                                    }
                                                    break;

                                                //Check if sensor is activated for partial protection mode
                                                case 3:
                                                    if ($variable['PartialProtectionModeActive']) {
                                                        $alerting = true;
                                                    }
                                                    break;
                                            }
                                            //Alerting
                                            if ($alerting) {
                                                $result = true;
                                                //Alarm state
                                                $this->SetValue('AlarmState', 1);
                                                $this->SetValue('AlertingSensor', $variable['Designation']);
                                                //Alarm protocol
                                                if ($variable['UseAlarmProtocol']) {
                                                    $text = $variable['Designation'] . ' hat eine Bewegung erkannt und einen Alarm ausgelöst. (ID ' . $SenderID . ')';
                                                    $logText = date('d.m.Y, H:i:s') . ', ' . $this->ReadPropertyString('Location') . ', ' . $this->ReadPropertyString('AlarmZoneName') . ', ' . $text;
                                                    $this->UpdateAlarmProtocol($logText, 2);
                                                }
                                                //Notification
                                                if ($variable['UseNotification']) {
                                                    $this->SendNotification('MotionDetectorAlarmNotification', (string) $variable['Designation']);
                                                }
                                                //Options
                                                if ($variable['UseAlarmSiren']) {
                                                    $this->SetValue('AlarmSiren', true);
                                                }
                                                if ($variable['UseAlarmLight']) {
                                                    $this->SetValue('AlarmLight', true);
                                                }
                                                if ($variable['UseAlarmCall']) {
                                                    $this->SetValue('AlarmCall', true);
                                                }
                                                if ($variable['UseAlertingAction']) {
                                                    $action = json_decode($variable['AlertingAction'], true);
                                                    @IPS_RunAction($action['actionID'], $action['parameters']);
                                                }
                                                if ($variable['UseAlarmSirenAction']) {
                                                    $action = json_decode($variable['AlarmSirenAction'], true);
                                                    @IPS_RunAction($action['actionID'], $action['parameters']);
                                                }
                                                if ($variable['UseAlarmLightAction']) {
                                                    $action = json_decode($variable['AlarmLightAction'], true);
                                                    @IPS_RunAction($action['actionID'], $action['parameters']);
                                                }
                                                if ($variable['UseAlarmCallAction']) {
                                                    $action = json_decode($variable['AlarmCallAction'], true);
                                                    @IPS_RunAction($action['actionID'], $action['parameters']);
                                                }
                                            }
                                            break;

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    #################### Private

    /**
     * Checks the state of all activated motion detectors.
     *
     * @return bool
     * false =  No motion
     * true =   Motion detected
     *
     * @throws Exception
     */
    private function CheckMotionDetectorState(): bool
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $state = false;
        $variables = json_decode($this->ReadPropertyString('MotionDetectors'), true);
        foreach ($variables as $variable) {
            if (!$variable['Use']) {
                continue;
            }
            //Check conditions
            if (IPS_IsConditionPassing($variable['PrimaryCondition']) && IPS_IsConditionPassing($variable['SecondaryCondition'])) {
                $state = true;
            }
        }
        $this->SetValue('MotionDetectorState', $state);
        return $state;
    }
}