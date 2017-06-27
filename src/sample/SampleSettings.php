<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 26/06/17
 * Time: 23:25
 */

namespace c00\sample;


use c00\common\AbstractSettings;

class SampleSettings extends AbstractSettings
{
    public $name;
    public $logLevel;
    /** @var Team */
    public $team;
    /** @var array  */
    public $fruits = [];
    /** @var User[] */
    public $users = [];
    /** @var Box */
    public $box;


    public function loadDefaults()
    {
        $this->name = "Peter";
        $this->logLevel = 5;
        $this->fruits = ['apple', 'banana', 'jack fruit'];
        $this->team = Team::newInstance("The bananas", "123", "face.jpg");
        $this->users = [
            User::newInstance("Frank", "frank@covle.com"),
            User::newInstance("Steve", "steve@covle.com"),
            User::newInstance("Imelda", "imelda@covle.com")
        ];
        $this->box = Box::newInstance(2, 3, 4);
    }


}