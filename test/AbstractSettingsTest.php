<?php

namespace test;

use c00\sample\Box;
use c00\sample\NestedSettings;
use c00\sample\SampleSettings;
use c00\sample\Team;
use c00\sample\User;
use PHPUnit\Framework\TestCase;

class AbstractSettingsTest extends TestCase
{
    const FILE = "/tmp/sample-settings.json";

    protected function setUp(): void
    {
        parent::setUp();

        if (file_exists(self::FILE)) unlink(self::FILE);
    }

    function tearDown(): void
    {
        parent::tearDown();

        if (file_exists(self::FILE)) unlink(self::FILE);
    }

    function testSaveAndLoad(){
        $this->assertFalse(file_exists(self::FILE));

        //Save stuff
        $s = new SampleSettings('sample-settings', '/tmp/');

        $s->name = "Bessy";
        $s->logLevel = 1;
        $s->fruits = ['pineapple', 'papaya'];
        $s->team = Team::newInstance("The hooligans", "666", "satan.jpg");
        $s->users = [
            User::newInstance("Robert", "robert@covle.com"),
            User::newInstance("Moses", "moses@covle.com")
        ];
        $s->box = Box::newInstance(7, 8, 9);


        $s->save();
        $this->assertTrue(file_exists(self::FILE));


        $loaded = new SampleSettings('sample-settings', '/tmp/');
        $loaded->load();

        //compare classes
        $this->assertEquals(count($s->fruits), count($loaded->fruits));
        $this->assertEquals(get_class($s->team), get_class($loaded->team));
        $this->assertEquals(get_class($s->box), get_class($loaded->box));
        foreach ($s->users as $key => $user) {
            $this->assertEquals(get_class($user), get_class($loaded->users[$key]));
        }

        //Compare content as JSON
        $origJson = json_encode($s);
        $loadedJson = json_encode($loaded);
        $this->assertEquals($origJson, $loadedJson);
    }

    function testNestedSettingsClasses() {
        $s = new NestedSettings('nested-settings', '/tmp/');

        $s->loadDefaults();

        $this->assertEquals('nested-settings', $s->name);

        $sample = $s->sampleSettings;
        $this->assertTrue($sample instanceof SampleSettings);
        $this->assertTrue($sample->users[0] instanceof User);
        $this->assertTrue($sample->box instanceof Box);

        $s->save();

        $loaded = new NestedSettings('nested-settings', '/tmp/');
        $loaded->load();

        $this->assertEquals('nested-settings', $loaded->name);

        $loadedSample = $loaded->sampleSettings;
        $this->assertTrue($loadedSample instanceof SampleSettings);
        $this->assertTrue($loadedSample->users[0] instanceof User);
        $this->assertTrue($loadedSample->box instanceof Box);


    }

}