<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 26/06/17
 * Time: 23:25
 */

namespace c00\sample;


use c00\common\AbstractSettings;

class NestedSettings extends AbstractSettings
{
    public $name;

    /** @var SampleSettings nested settings class */
    public $sampleSettings;


    public function loadDefaults()
    {
        $this->name = "nested-settings";
        $this->sampleSettings = new SampleSettings();
        $this->sampleSettings->loadDefaults();
    }


}