<?php

/**
 * @project       _Alarmzone/Alarmzone
 * @file          AZ_Blacklist.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection PhpUnusedPrivateMethodInspection */

declare(strict_types=1);

trait AZ_Blacklist
{
    /**
     * Resets the blacklist.
     *
     * @return void
     */
    public function ResetBlacklist(): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $this->SetBuffer('Blacklist', json_encode([]));
        $this->SendDebug(__FUNCTION__, 'Die Sperrliste wurde erfolgreich zurückgesetzt.', 0);
        $this->ReloadForm();
    }

    #################### Private

    /**
     * Adds a sensor to the blacklist.
     *
     * @param int $SensorID
     * @param string $SensorDesignation
     * @return void
     */
    private function AddSensorBlacklist(int $SensorID, string $SensorDesignation): void
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        $blackList = json_decode($this->GetBuffer('Blacklist'), true);
        $blackList[] = '{"sensorID": ' . $SensorID . ',"sensorDesignation": "' . $SensorDesignation . '"}';
        $this->SetBuffer('Blacklist', json_encode(array_unique($blackList)));
        $this->SendDebug(__FUNCTION__, 'Der Sensor mit der ID ' . $SensorID . ' wurde zur Sperrliste hinzugefügt.', 0);
    }

    /**
     * Checks if a sensor is blacklisted.
     *
     * @param int $SensorID
     * @return bool
     * false =  Not blacklisted
     * true =   Blacklisted
     */
    private function CheckSensorBlacklist(int $SensorID): bool
    {
        $this->SendDebug(__FUNCTION__, 'wird ausgeführt', 0);
        foreach (json_decode($this->GetBuffer('Blacklist')) as $element) {
            $variable = json_decode($element, true);
            if ($variable['sensorID'] == $SensorID) {
                return true;
            }
        }
        return false;
    }
}