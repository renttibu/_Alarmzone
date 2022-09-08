<?php

/**
 * @project       _Alarmzone/Alarmzonensteuerung
 * @file          AZST_Config.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

declare(strict_types=1);

trait AZST_Config
{
    /**
     * Reloads the configuration form.
     *
     * @return void
     */
    public function ReloadConfig(): void
    {
        $this->ReloadForm();
    }

    /**
     * Modifies a configuration button.
     *
     * @param string $Field
     * @param string $Caption
     * @param int $ObjectID
     * @return void
     */
    public function ModifyButton(string $Field, string $Caption, int $ObjectID): void
    {
        $state = false;
        if ($ObjectID > 1 && @IPS_ObjectExists($ObjectID)) { //0 = main category, 1 = none
            $state = true;
        }
        $this->UpdateFormField($Field, 'caption', $Caption);
        $this->UpdateFormField($Field, 'visible', $state);
        $this->UpdateFormField($Field, 'objectID', $ObjectID);
    }

    /**
     * Gets the configuration form.
     *
     * @return false|string
     * @throws Exception
     */
    public function GetConfigurationForm()
    {
        $form = [];

        ########## Elements

        //Info
        $form['elements'][0] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Info',
            'items'   => [
                [
                    'type'    => 'Label',
                    'name'    => 'ModuleID',
                    'caption' => "ID:\t\t\t" . $this->InstanceID
                ],
                [
                    'type'    => 'Label',
                    'name'    => 'ModuleDesignation',
                    'caption' => "Modul:\t\t" . self::MODULE_NAME
                ],
                [
                    'type'    => 'Label',
                    'name'    => 'ModulePrefix',
                    'caption' => "Präfix:\t\t" . self::MODULE_PREFIX
                ],
                [
                    'type'    => 'Label',
                    'name'    => 'ModuleVersion',
                    'caption' => "Version:\t\t" . self::MODULE_VERSION
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'ValidationTextBox',
                    'name'    => 'Note',
                    'caption' => 'Notiz',
                    'width'   => '600px'
                ]
            ]
        ];

        //Designations
        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Bezeichnung',
            'items'   => [
                [
                    'type'    => 'ValidationTextBox',
                    'name'    => 'Location',
                    'caption' => 'Standortbezeichnung (z.B. Musterstraße 1)',
                    'width'   => '600px'
                ]
            ]
        ];

        //Functions
        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Funktionen',
            'items'   => [
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableActive',
                    'caption' => 'Aktiv (Schalter im WebFront)'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Bezeichnung',
                    'bold'    => true,
                    'italic'  => true,
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableLocation',
                    'caption' => 'Standortbezeichnung'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Modus',
                    'bold'    => true,
                    'italic'  => true,
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableMode',
                    'caption' => 'Modus'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Unscharf',
                    'bold'    => true,
                    'italic'  => true,
                ],
                [
                    'type'    => 'ValidationTextBox',
                    'name'    => 'DisarmedName',
                    'caption' => 'Bezeichnung',
                    'width'   => '600px'
                ],
                [
                    'type'    => 'SelectIcon',
                    'name'    => 'DisarmedIcon',
                    'caption' => 'Icon'
                ],
                [
                    'type'    => 'SelectColor',
                    'name'    => 'DisarmedColor',
                    'caption' => 'Farbe'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Vollschutz',
                    'bold'    => true,
                    'italic'  => true,
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'UseFullProtectionMode',
                    'caption' => 'Vollschutz'
                ],
                [
                    'type'    => 'ValidationTextBox',
                    'name'    => 'FullProtectionName',
                    'caption' => 'Bezeichnung',
                    'width'   => '600px'
                ],
                [
                    'type'    => 'SelectIcon',
                    'name'    => 'FullProtectionIcon',
                    'caption' => 'Icon'
                ],
                [
                    'type'    => 'SelectColor',
                    'name'    => 'FullProtectionColor',
                    'caption' => 'Farbe'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Hüllschutz',
                    'bold'    => true,
                    'italic'  => true,
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'UseHullProtectionMode',
                    'caption' => 'Hüllschutz'
                ],
                [
                    'type'    => 'ValidationTextBox',
                    'name'    => 'HullProtectionName',
                    'caption' => 'Hüllschutz',
                    'width'   => '600px'
                ],
                [
                    'type'    => 'SelectIcon',
                    'name'    => 'HullProtectionIcon',
                    'caption' => 'Icon'
                ],
                [
                    'type'    => 'SelectColor',
                    'name'    => 'HullProtectionColor',
                    'caption' => 'Farbe'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Teilschutz',
                    'bold'    => true,
                    'italic'  => true,
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'UsePartialProtectionMode',
                    'caption' => 'Teilschutz'
                ],
                [
                    'type'    => 'ValidationTextBox',
                    'name'    => 'PartialProtectionName',
                    'caption' => 'Teilschutz',
                    'width'   => '600px'
                ],
                [
                    'type'    => 'SelectIcon',
                    'name'    => 'PartialProtectionIcon',
                    'caption' => 'Icon'
                ],
                [
                    'type'    => 'SelectColor',
                    'name'    => 'PartialProtectionColor',
                    'caption' => 'Farbe'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Individualschutz',
                    'bold'    => true,
                    'italic'  => true,
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'UseIndividualProtectionMode',
                    'caption' => 'Individualschutz'
                ],
                [
                    'type'    => 'ValidationTextBox',
                    'name'    => 'IndividualProtectionName',
                    'caption' => 'Individualschutz',
                    'width'   => '600px'
                ],
                [
                    'type'    => 'SelectIcon',
                    'name'    => 'IndividualProtectionIcon',
                    'caption' => 'Icon'
                ],
                [
                    'type'    => 'SelectColor',
                    'name'    => 'IndividualProtectionColor',
                    'caption' => 'Farbe'
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'Label',
                    'caption' => 'Status',
                    'bold'    => true,
                    'italic'  => true,
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableSystemState',
                    'caption' => 'Systemstatus'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableSystemDetailedState',
                    'caption' => 'Detaillierter Systemstatus'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableDoorWindowState',
                    'caption' => 'Tür- und Fensterstatus'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableMotionDetectorState',
                    'caption' => 'Bewegungsmelderstatus'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableAlarmState',
                    'caption' => 'Alarmstatus'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableAlertingSensor',
                    'caption' => 'Auslösender Alarmsensor'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableAlarmSirenState',
                    'caption' => 'Alarmsirene'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableAlarmLightState',
                    'caption' => 'Alarmbeleuchtung'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableAlarmCallState',
                    'caption' => 'Alarmanruf'
                ]
            ]
        ];

        //Alarm zones
        $alarmZoneValues = [];
        $alarmZones = json_decode($this->ReadPropertyString('AlarmZones'), true);
        foreach ($alarmZones as $alarmZone) {
            if (!$alarmZone['Use']) {
                continue;
            }
            $rowColor = '#FFC0C0'; //red
            $id = $alarmZone['ID'];
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                $rowColor = '#C0FFC0'; //light green
            }
            $alarmZoneValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Alarmzonen',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'AlarmZones',
                    'rowCount' => 6,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
                        [
                            'name'    => 'Use',
                            'caption' => 'Aktiviert',
                            'width'   => '100px',
                            'add'     => true,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'caption' => 'Alarmzone',
                            'name'    => 'ID',
                            'width'   => '450px',
                            'add'     => 0,
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "AlarmZoneConfigurationButton", "ID " . $AlarmZones["ID"] . " Instanzkonfiguration", $AlarmZones["ID"]);',
                            'edit'    => [
                                'type'     => 'SelectModule',
                                'moduleID' => self::ALARMZONE_MODULE_GUID
                            ]
                        ],
                        [
                            'name'    => 'Designation',
                            'caption' => 'Bezeichnung',
                            'width'   => '400px',
                            'add'     => '',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "AlarmZoneConfigurationButton", "ID " . $AlarmZones["ID"] . " Instanzkonfiguration", $AlarmZones["ID"]);',
                            'edit'    => [
                                'type' => 'ValidationTextBox'
                            ]
                        ],
                        [
                            'name'    => 'IndividualProtectionMode',
                            'caption' => 'Individualschutz',
                            'width'   => '200px',
                            'add'     => 1,
                            'edit'    => [
                                'type'    => 'Select',
                                'options' => [
                                    [
                                        'caption' => 'Unscharf',
                                        'value'   => 0
                                    ],
                                    [
                                        'caption' => 'Vollschutz',
                                        'value'   => 1
                                    ],
                                    [
                                        'caption' => 'Hüllschutz',
                                        'value'   => 2
                                    ],
                                    [
                                        'caption' => 'Teilschutz',
                                        'value'   => 3
                                    ],
                                    [
                                        'caption' => 'Keine Funktion',
                                        'value'   => 4
                                    ]
                                ]
                            ]
                        ]

                    ],
                    'values' => $alarmZoneValues,
                ],
                [
                    'type'  => 'RowLayout',
                    'items' => [
                        [
                            'type'    => 'Button',
                            'caption' => 'Neue Instanz erstellen',
                            'onClick' => self::MODULE_PREFIX . '_CreateAlarmZoneInstance($id);'
                        ],
                        [
                            'type'    => 'Label',
                            'caption' => ' '
                        ],
                        [
                            'type'     => 'OpenObjectButton',
                            'caption'  => 'Instanzkonfiguration',
                            'name'     => 'AlarmZoneConfigurationButton',
                            'visible'  => false,
                            'objectID' => 0
                        ]
                    ]
                ]
            ]
        ];

        //Protection mode
        $protectionModeValues = [];
        $variables = json_decode($this->ReadPropertyString('ProtectionMode'), true);
        foreach ($variables as $variable) {
            $rowColor = '#FFC0C0'; //red
            $id = $variable['ID'];
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                $rowColor = '#DFDFDF'; # grey
                if ($variable['Use']) {
                    $rowColor = '#C0FFC0'; //light green
                }
            }
            $protectionModeValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Modus',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'ProtectionMode',
                    'rowCount' => 6,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
                        [
                            'name'    => 'Use',
                            'caption' => 'Aktiviert',
                            'width'   => '100px',
                            'add'     => true,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'name'    => 'ID',
                            'caption' => 'Variable',
                            'width'   => '450px',
                            'add'     => 0,
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "ProtectionModeConfigurationButton", "ID " . $ProtectionMode["ID"] . " aufrufen", $ProtectionMode["ID"]);',
                            'edit'    => [
                                'type' => 'SelectVariable'
                            ]
                        ],
                        [
                            'caption' => 'Bezeichnung',
                            'name'    => 'Designation',
                            'width'   => '400px',
                            'add'     => '',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "ProtectionModeConfigurationButton", "ID " . $ProtectionMode["ID"] . " aufrufen", $ProtectionMode["ID"]);',
                            'edit'    => [
                                'type' => 'ValidationTextBox'
                            ]
                        ]
                    ],
                    'values' => $protectionModeValues,
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'caption'  => 'Bearbeiten',
                    'name'     => 'ProtectionModeConfigurationButton',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //System state
        $systemStateValues = [];
        $variables = json_decode($this->ReadPropertyString('SystemState'), true);
        foreach ($variables as $variable) {
            $rowColor = '#FFC0C0'; //red
            $id = $variable['ID'];
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                $rowColor = '#DFDFDF'; # grey
                if ($variable['Use']) {
                    $rowColor = '#C0FFC0'; //light green
                }
            }
            $systemStateValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Systemstatus',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'SystemState',
                    'rowCount' => 6,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
                        [
                            'name'    => 'Use',
                            'caption' => 'Aktiviert',
                            'width'   => '100px',
                            'add'     => true,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'name'    => 'ID',
                            'caption' => 'Variable',
                            'width'   => '450px',
                            'add'     => 0,
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "SystemStateConfigurationButton", "ID " . $SystemState["ID"] . " aufrufen", $SystemState["ID"]);',
                            'edit'    => [
                                'type' => 'SelectVariable'
                            ]
                        ],
                        [
                            'caption' => 'Bezeichnung',
                            'name'    => 'Designation',
                            'width'   => '400px',
                            'add'     => '',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "SystemStateConfigurationButton", "ID " . $SystemState["ID"] . " aufrufen", $SystemState["ID"]);',
                            'edit'    => [
                                'type' => 'ValidationTextBox'
                            ]
                        ]
                    ],
                    'values' => $systemStateValues,
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'caption'  => 'Bearbeiten',
                    'name'     => 'SystemStateConfigurationButton',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //System detailed state
        $systemDetailedStateValues = [];
        $variables = json_decode($this->ReadPropertyString('SystemDetailedState'), true);
        foreach ($variables as $variable) {
            $rowColor = '#FFC0C0'; //red
            $id = $variable['ID'];
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                $rowColor = '#DFDFDF'; # grey
                if ($variable['Use']) {
                    $rowColor = '#C0FFC0'; //light green
                }
            }
            $systemDetailedStateValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Detaillierter Systemstatus',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'SystemDetailedState',
                    'rowCount' => 6,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
                        [
                            'name'    => 'Use',
                            'caption' => 'Aktiviert',
                            'width'   => '100px',
                            'add'     => true,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'name'    => 'ID',
                            'caption' => 'Variable',
                            'width'   => '450px',
                            'add'     => 0,
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "SystemDetailedStateConfigurationButton", "ID " . $SystemDetailedState["ID"] . " aufrufen", $SystemDetailedState["ID"]);',
                            'edit'    => [
                                'type' => 'SelectVariable'
                            ]
                        ],
                        [
                            'caption' => 'Bezeichnung',
                            'name'    => 'Designation',
                            'width'   => '400px',
                            'add'     => '',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "SystemDetailedStateConfigurationButton", "ID " . $SystemDetailedState["ID"] . " aufrufen", $SystemDetailedState["ID"]);',
                            'edit'    => [
                                'type' => 'ValidationTextBox'
                            ]
                        ]
                    ],
                    'values' => $systemDetailedStateValues,
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'caption'  => 'Bearbeiten',
                    'name'     => 'SystemDetailedStateConfigurationButton',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Alarm state
        $alarmStateValues = [];
        $variables = json_decode($this->ReadPropertyString('AlarmState'), true);
        foreach ($variables as $variable) {
            $rowColor = '#FFC0C0'; //red
            $id = $variable['ID'];
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                $rowColor = '#DFDFDF'; # grey
                if ($variable['Use']) {
                    $rowColor = '#C0FFC0'; //light green
                }
            }
            $alarmStateValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Alarmstatus',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'AlarmState',
                    'rowCount' => 6,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
                        [
                            'name'    => 'Use',
                            'caption' => 'Aktiviert',
                            'width'   => '100px',
                            'add'     => true,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'name'    => 'ID',
                            'caption' => 'Variable',
                            'width'   => '450px',
                            'add'     => 0,
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "AlarmStateConfigurationButton", "ID " . $AlarmState["ID"] . " aufrufen", $AlarmState["ID"]);',
                            'edit'    => [
                                'type' => 'SelectVariable'
                            ]
                        ],
                        [
                            'caption' => 'Bezeichnung',
                            'name'    => 'Designation',
                            'width'   => '400px',
                            'add'     => '',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "AlarmStateConfigurationButton", "ID " . $AlarmState["ID"] . " aufrufen", $AlarmState["ID"]);',
                            'edit'    => [
                                'type' => 'ValidationTextBox'
                            ]
                        ]
                    ],
                    'values' => $alarmStateValues,
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'caption'  => 'Bearbeiten',
                    'name'     => 'AlarmStateConfigurationButton',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Alerting sensors
        $alertingSensorValues = [];
        $variables = json_decode($this->ReadPropertyString('AlertingSensor'), true);
        foreach ($variables as $variable) {
            $rowColor = '#FFC0C0'; //red
            $id = $variable['ID'];
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                $rowColor = '#DFDFDF'; # grey
                if ($variable['Use']) {
                    $rowColor = '#C0FFC0'; //light green
                }
            }
            $alertingSensorValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Auslösender Alarmsensor',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'AlertingSensor',
                    'rowCount' => 6,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
                        [
                            'name'    => 'Use',
                            'caption' => 'Aktiviert',
                            'width'   => '100px',
                            'add'     => true,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'name'    => 'ID',
                            'caption' => 'Variable',
                            'width'   => '450px',
                            'add'     => 0,
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "AlertingSensorConfigurationButton", "ID " . $AlertingSensor["ID"] . " aufrufen", $AlertingSensor["ID"]);',
                            'edit'    => [
                                'type' => 'SelectVariable'
                            ]
                        ],
                        [
                            'caption' => 'Bezeichnung',
                            'name'    => 'Designation',
                            'width'   => '400px',
                            'add'     => '',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "AlertingSensorConfigurationButton", "ID " . $AlertingSensor["ID"] . " aufrufen", $AlertingSensor["ID"]);',
                            'edit'    => [
                                'type' => 'ValidationTextBox'
                            ]
                        ]
                    ],
                    'values' => $alertingSensorValues,
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'caption'  => 'Bearbeiten',
                    'name'     => 'AlertingSensorConfigurationButton',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Door and window states
        $doorWindowStateValues = [];
        $variables = json_decode($this->ReadPropertyString('DoorWindowState'), true);
        foreach ($variables as $variable) {
            $rowColor = '#FFC0C0'; //red
            $id = $variable['ID'];
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                $rowColor = '#DFDFDF'; # grey
                if ($variable['Use']) {
                    $rowColor = '#C0FFC0'; //light green
                }
            }
            $doorWindowStateValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Tür- und Fensterstatus',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'DoorWindowState',
                    'rowCount' => 6,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
                        [
                            'name'    => 'Use',
                            'caption' => 'Aktiviert',
                            'width'   => '100px',
                            'add'     => true,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'name'    => 'ID',
                            'caption' => 'Variable',
                            'width'   => '450px',
                            'add'     => 0,
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "DoorWindowStateConfigurationButton", "ID " . $DoorWindowState["ID"] . " aufrufen", $DoorWindowState["ID"]);',
                            'edit'    => [
                                'type' => 'SelectVariable'
                            ]
                        ],
                        [
                            'caption' => 'Bezeichnung',
                            'name'    => 'Designation',
                            'width'   => '400px',
                            'add'     => '',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "DoorWindowStateConfigurationButton", "ID " . $DoorWindowState["ID"] . " aufrufen", $DoorWindowState["ID"]);',
                            'edit'    => [
                                'type' => 'ValidationTextBox'
                            ]
                        ]
                    ],
                    'values' => $doorWindowStateValues,
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'caption'  => 'Bearbeiten',
                    'name'     => 'DoorWindowStateConfigurationButton',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Motion detector states
        $motionDetectorStateValues = [];
        $variables = json_decode($this->ReadPropertyString('MotionDetectorState'), true);
        foreach ($variables as $variable) {
            $rowColor = '#FFC0C0'; //red
            $id = $variable['ID'];
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                $rowColor = '#DFDFDF'; # grey
                if ($variable['Use']) {
                    $rowColor = '#C0FFC0'; //light green
                }
            }
            $motionDetectorStateValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Bewegungsmelderstatus',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'MotionDetectorState',
                    'rowCount' => 6,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
                        [
                            'name'    => 'Use',
                            'caption' => 'Aktiviert',
                            'width'   => '100px',
                            'add'     => true,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'name'    => 'ID',
                            'caption' => 'Variable',
                            'width'   => '450px',
                            'add'     => 0,
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "MotionDetectorStateConfigurationButton", "ID " . $MotionDetectorState["ID"] . " aufrufen", $MotionDetectorState["ID"]);',
                            'edit'    => [
                                'type' => 'SelectVariable'
                            ]
                        ],
                        [
                            'caption' => 'Bezeichnung',
                            'name'    => 'Designation',
                            'width'   => '400px',
                            'add'     => '',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "MotionDetectorStateConfigurationButton", "ID " . $MotionDetectorState["ID"] . " aufrufen", $MotionDetectorState["ID"]);',
                            'edit'    => [
                                'type' => 'ValidationTextBox'
                            ]
                        ]
                    ],
                    'values' => $motionDetectorStateValues,
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'caption'  => 'Bearbeiten',
                    'name'     => 'MotionDetectorStateConfigurationButton',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Alarm sirens
        $alarmSirenValues = [];
        $variables = json_decode($this->ReadPropertyString('AlarmSiren'), true);
        foreach ($variables as $variable) {
            $rowColor = '#FFC0C0'; //red
            $id = $variable['ID'];
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                $rowColor = '#DFDFDF'; # grey
                if ($variable['Use']) {
                    $rowColor = '#C0FFC0'; //light green
                }
            }
            $alarmSirenValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Alarmsirene',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'AlarmSiren',
                    'rowCount' => 6,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
                        [
                            'name'    => 'Use',
                            'caption' => 'Aktiviert',
                            'width'   => '100px',
                            'add'     => true,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'name'    => 'ID',
                            'caption' => 'Variable',
                            'width'   => '450px',
                            'add'     => 0,
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "AlarmSirenConfigurationButton", "ID " . $AlarmSiren["ID"] . " aufrufen", $AlarmSiren["ID"]);',
                            'edit'    => [
                                'type' => 'SelectVariable'
                            ]
                        ],
                        [
                            'caption' => 'Bezeichnung',
                            'name'    => 'Designation',
                            'width'   => '400px',
                            'add'     => '',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "AlarmSirenConfigurationButton", "ID " . $AlarmSiren["ID"] . " aufrufen", $AlarmSiren["ID"]);',
                            'edit'    => [
                                'type' => 'ValidationTextBox'
                            ]
                        ]
                    ],
                    'values' => $alarmSirenValues,
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'caption'  => 'Bearbeiten',
                    'name'     => 'AlarmSirenConfigurationButton',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Alarm lights
        $alarmLightValues = [];
        $variables = json_decode($this->ReadPropertyString('AlarmLight'), true);
        foreach ($variables as $variable) {
            $rowColor = '#FFC0C0'; //red
            $id = $variable['ID'];
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                $rowColor = '#DFDFDF'; # grey
                if ($variable['Use']) {
                    $rowColor = '#C0FFC0'; //light green
                }
            }
            $alarmLightValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Alarmbeleuchtung',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'AlarmLight',
                    'rowCount' => 6,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
                        [
                            'name'    => 'Use',
                            'caption' => 'Aktiviert',
                            'width'   => '100px',
                            'add'     => true,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'name'    => 'ID',
                            'caption' => 'Variable',
                            'width'   => '450px',
                            'add'     => 0,
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "AlarmLightConfigurationButton", "ID " . $AlarmLight["ID"] . " aufrufen", $AlarmLight["ID"]);',
                            'edit'    => [
                                'type' => 'SelectVariable'
                            ]
                        ],
                        [
                            'caption' => 'Bezeichnung',
                            'name'    => 'Designation',
                            'width'   => '400px',
                            'add'     => '',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "AlarmLightConfigurationButton", "ID " . $AlarmLight["ID"] . " aufrufen", $AlarmLight["ID"]);',
                            'edit'    => [
                                'type' => 'ValidationTextBox'
                            ]
                        ]
                    ],
                    'values' => $alarmLightValues,
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'caption'  => 'Bearbeiten',
                    'name'     => 'AlarmLightConfigurationButton',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Alarm calls
        $alarmCallValues = [];
        $variables = json_decode($this->ReadPropertyString('AlarmCall'), true);
        foreach ($variables as $variable) {
            $rowColor = '#FFC0C0'; //red
            $id = $variable['ID'];
            if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                $rowColor = '#DFDFDF'; # grey
                if ($variable['Use']) {
                    $rowColor = '#C0FFC0'; //light green
                }
            }
            $alarmCallValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Alarmanruf',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'AlarmCall',
                    'rowCount' => 6,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
                        [
                            'name'    => 'Use',
                            'caption' => 'Aktiviert',
                            'width'   => '100px',
                            'add'     => true,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'name'    => 'ID',
                            'caption' => 'Variable',
                            'width'   => '450px',
                            'add'     => 0,
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "AlarmCallConfigurationButton", "ID " . $AlarmCall["ID"] . " aufrufen", $AlarmCall["ID"]);',
                            'edit'    => [
                                'type' => 'SelectVariable'
                            ]
                        ],
                        [
                            'caption' => 'Bezeichnung',
                            'name'    => 'Designation',
                            'width'   => '400px',
                            'add'     => '',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "AlarmCallConfigurationButton", "ID " . $AlarmCall["ID"] . " aufrufen", $AlarmCall["ID"]);',
                            'edit'    => [
                                'type' => 'ValidationTextBox'
                            ]
                        ]
                    ],
                    'values' => $alarmCallValues,
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'caption'  => 'Bearbeiten',
                    'name'     => 'AlarmCallConfigurationButton',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        ########## Actions

        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Konfiguration',
            'items'   => [
                [
                    'type'    => 'Button',
                    'caption' => 'Neu laden',
                    'onClick' => self::MODULE_PREFIX . '_ReloadConfig($id);'
                ]
            ]
        ];

        //Test center
        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Schaltfunktionen',
            'items'   => [
                [
                    'type' => 'TestCenter',
                ]
            ]
        ];

        //Registered references
        $registeredReferences = [];
        $references = $this->GetReferenceList();
        foreach ($references as $reference) {
            $name = 'Objekt #' . $reference . ' existiert nicht';
            $rowColor = '#FFC0C0'; //red
            if (@IPS_ObjectExists($reference)) {
                $name = IPS_GetName($reference);
                $rowColor = '#C0FFC0'; //light green
            }
            $registeredReferences[] = [
                'ObjectID' => $reference,
                'Name'     => $name,
                'rowColor' => $rowColor];
        }

        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Registrierte Referenzen',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'RegisteredReferences',
                    'rowCount' => 10,
                    'sort'     => [
                        'column'    => 'ObjectID',
                        'direction' => 'ascending'
                    ],
                    'columns' => [
                        [
                            'caption' => 'ID',
                            'name'    => 'ObjectID',
                            'width'   => '150px',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "RegisteredReferencesConfigurationButton", "ID " . $RegisteredReferences["ObjectID"] . " aufrufen", $RegisteredReferences["ObjectID"]);'
                        ],
                        [
                            'caption' => 'Name',
                            'name'    => 'Name',
                            'width'   => '300px',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "RegisteredReferencesConfigurationButton", "ID " . $RegisteredReferences["ObjectID"] . " aufrufen", $RegisteredReferences["ObjectID"]);'
                        ]
                    ],
                    'values' => $registeredReferences
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'name'     => 'RegisteredReferencesConfigurationButton',
                    'caption'  => 'Aufrufen',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Registered messages
        $registeredMessages = [];
        $messages = $this->GetMessageList();
        foreach ($messages as $id => $messageID) {
            $name = 'Objekt #' . $id . ' existiert nicht';
            $rowColor = '#FFC0C0'; //red
            if (@IPS_ObjectExists($id)) {
                $name = IPS_GetName($id);
                $rowColor = '#C0FFC0'; //light green
            }
            switch ($messageID) {
                case [10001]:
                    $messageDescription = 'IPS_KERNELSTARTED';
                    break;

                case [10603]:
                    $messageDescription = 'VM_UPDATE';
                    break;

                default:
                    $messageDescription = 'keine Bezeichnung';
            }
            $registeredMessages[] = [
                'ObjectID'           => $id,
                'Name'               => $name,
                'MessageID'          => $messageID,
                'MessageDescription' => $messageDescription,
                'rowColor'           => $rowColor];
        }

        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Registrierte Nachrichten',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'RegisteredMessages',
                    'rowCount' => 10,
                    'sort'     => [
                        'column'    => 'ObjectID',
                        'direction' => 'ascending'
                    ],
                    'columns' => [
                        [
                            'caption' => 'ID',
                            'name'    => 'ObjectID',
                            'width'   => '150px',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "RegisteredMessagesConfigurationButton", "ID " . $RegisteredMessages["ObjectID"] . " aufrufen", $RegisteredMessages["ObjectID"]);'
                        ],
                        [
                            'caption' => 'Name',
                            'name'    => 'Name',
                            'width'   => '300px',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "RegisteredMessagesConfigurationButton", "ID " . $RegisteredMessages["ObjectID"] . " aufrufen", $RegisteredMessages["ObjectID"]);'
                        ],
                        [
                            'caption' => 'Nachrichten ID',
                            'name'    => 'MessageID',
                            'width'   => '150px'
                        ],
                        [
                            'caption' => 'Nachrichten Bezeichnung',
                            'name'    => 'MessageDescription',
                            'width'   => '250px'
                        ]
                    ],
                    'values' => $registeredMessages
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'name'     => 'RegisteredMessagesConfigurationButton',
                    'caption'  => 'Aufrufen',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Alarm zone
        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Alarmzonen',
            'items'   => [
                [
                    'type'    => 'PopupButton',
                    'caption' => 'Variablen ermitteln',
                    'popup'   => [
                        'caption' => 'Variablen wirklich automatisch ermitteln?',
                        'items'   => [
                            [
                                'type'    => 'Button',
                                'caption' => 'Variablen ermitteln',
                                'onClick' => self::MODULE_PREFIX . '_DetermineAlarmZoneVariables($id);'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        ########## Status

        $form['status'][] = [
            'code'    => 101,
            'icon'    => 'active',
            'caption' => self::MODULE_NAME . ' wird erstellt',
        ];
        $form['status'][] = [
            'code'    => 102,
            'icon'    => 'active',
            'caption' => self::MODULE_NAME . ' ist aktiv',
        ];
        $form['status'][] = [
            'code'    => 103,
            'icon'    => 'active',
            'caption' => self::MODULE_NAME . ' wird gelöscht',
        ];
        $form['status'][] = [
            'code'    => 104,
            'icon'    => 'inactive',
            'caption' => self::MODULE_NAME . ' ist inaktiv',
        ];
        $form['status'][] = [
            'code'    => 200,
            'icon'    => 'inactive',
            'caption' => 'Es ist Fehler aufgetreten, weitere Informationen unter Meldungen, im Log oder Debug!',
        ];

        return json_encode($form);
    }
}