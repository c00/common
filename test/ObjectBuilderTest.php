<?php


namespace test;


use c00\common\ObjectBuilder;
use c00\QueryBuilder\Qry;
use c00\sample\MappedTeam;
use c00\sample\Session;
use c00\sample\User;

class ObjectBuilderTest extends \PHPUnit_Framework_TestCase
{

	public function testBuild() {
		$rows = [
			['u.id' => 1, 'u.name' => 'Peter', 'u.email' => 'Peter@example.com', 's.id'=> 1, 's.token' => 'qwerty'],
			['u.id' => 1, 'u.name' => 'Peter', 'u.email' => 'Peter@example.com', 's.id'=> 2, 's.token' => 'asdfgh'],
			['u.id' => 2, 'u.name' => 'Charlotte', 'u.email' => 'Charlotte@example.com', 's.id'=> 3, 's.token' => 'zxcvbn'],
			['active' => 1, 'email' => 'notTheEmail@example.com', 'u.id' => 3, 'u.name' => 'Nadia', 'name' => 'notNadia', 'u.email' => 'Nadia@example.com', 's.id'=> 4, 's.token' => 'poiuyt']
		];

		$q = Qry::select()
		        ->fromClass(User::class, 'user', 'u')
		        ->joinClass(Session::class, 'session', 's', 'u.id', '=', 's.id')
		        ->joinClass(MappedTeam::class, 'team', 't', 'u.teamId', '=', 't.id');

		$b = ObjectBuilder::newInstance($q)
		                  ->build($rows);



		$objects = $b->objects;

		$userCount = count($objects['u']);
		$sessionCount = count($objects['s']);

		$this->assertEquals(3, $userCount);
		$this->assertEquals(4, $sessionCount);
		/** @var User $nadia */
		$nadia = $objects['u'][3];
		$this->assertEquals('Nadia', $nadia->name);
		$this->assertEquals('Nadia@example.com', $nadia->email);
		$this->assertEquals(1, $nadia->active);

		//todo put them together


	}

	public function testBuildWithArray() {
		$rows = [
			['u.id' => 1, 'u.name' => 'Peter', 'u.email' => 'Peter@example.com', 's.id'=> 1, 's.token' => 'qwerty'],
			['u.id' => 1, 'u.name' => 'Peter', 'u.email' => 'Peter@example.com', 's.id'=> 2, 's.token' => 'asdfgh', 'q.id' => 1, 'q.foo' => 'bar'],
			['u.id' => 2, 'u.name' => 'Charlotte', 'u.email' => 'Charlotte@example.com', 's.id'=> 3, 's.token' => 'zxcvbn', 'q.id' => 2, 'q.foo' => 'baz'],
			['active' => 1, 'email' => 'notTheEmail@example.com', 'u.id' => 3, 'u.name' => 'Nadia', 'name' => 'notNadia', 'u.email' => 'Nadia@example.com', 's.id'=> 4, 's.token' => 'poiuyt']
		];

		$q = Qry::select()
		        ->fromClass(User::class, 'user', 'u')
		        ->joinClass(Session::class, 'session', 's', 'u.id', '=', 's.id')
		        ->joinClass(MappedTeam::class, 'team', 't', 'u.teamId', '=', 't.id');

		$b = ObjectBuilder::newInstance($q)
		                  ->build($rows);

		$objects = $b->objects;

		$userCount = count($objects['u']);
		$sessionCount = count($objects['s']);
		$foos = $objects['q'] ?? [];

		$this->assertEquals(count($foos), 2);

		$this->assertEquals(3, $userCount);
		$this->assertEquals(4, $sessionCount);
		/** @var User $nadia */
		$nadia = $objects['u'][3];
		$this->assertEquals('Nadia', $nadia->name);
		$this->assertEquals('Nadia@example.com', $nadia->email);
		$this->assertEquals(1, $nadia->active);

		$this->assertEquals('bar', $foos[1]['foo']);
		$this->assertEquals('baz', $foos[2]['foo']);

		//todo put them together


	}
}