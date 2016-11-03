<?php

use c00\common\AbstractDatabaseObject;
use c00\common\Database;
use c00\QueryBuilder\Qry;

class User extends AbstractDatabaseObject
{
    public $id;
    public $name;
    public $email;
    public $active;
    public $profileImage;
}

//database stuff
$host = "localhost";
$user = "root";
$pass = "";
$dbName = "MySampleDb";
$userTable = 'user';

//Database connection
$database = new Database($host, $user, $pass, $dbName);

//Create a user
$peter = new User();
$peter->id = 1;
$peter->name = "Peter";
$peter->email = "Peter@example.com";
$peter->active = true;
$peter->profileImage = "peter_face.jpg";

//Create a query
$query = Qry::insert($userTable, $peter);

//Save the user
$database->insertRow($query);

//Get a user from the database
$getQuery = Qry::select()
    ->from($userTable)
    ->where('id', '=', 1)
    ->asClass(User::class);

$userFromDb = $database->getRow($getQuery);

//Get all users from the database
$allUsersQuery = Qry::select()
    ->from($userTable)
    ->asClass(User::class);

//All users will be an array over User objects.
$allUsers = $database->getRows($allUsersQuery);