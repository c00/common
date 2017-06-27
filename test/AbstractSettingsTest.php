<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\sample\Box;
use c00\sample\SampleSettings;
use c00\sample\Team;
use c00\sample\User;

class AbstractSettingsTest extends \PHPUnit_Framework_TestCase
{
    const FILE = "/tmp/sample-settings.json";

    protected function setUp()
    {
        parent::setUp();

        if (file_exists(self::FILE)) unlink(self::FILE);
    }

    function tearDown()
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

}