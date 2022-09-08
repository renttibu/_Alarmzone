<?php

declare(strict_types=1);

include_once __DIR__ . '/stubs/Validator.php';

class AlarmzoneValidationTest extends TestCaseSymconValidation
{
    public function testValidateLibrary(): void
    {
        $this->validateLibrary(__DIR__ . '/..');
    }

    public function testValidateModule_Alarmzone(): void
    {
        $this->validateModule(__DIR__ . '/../Alarmzone');
    }

    public function testValidateModule_Alarmzonensteuerung(): void
    {
        $this->validateModule(__DIR__ . '/../Alarmzonensteuerung');
    }
}