# Common
A mini framework for common challenges such as database querying (ORM), 
dependency injection and date manipulation.

This is simply a personal usage toolkit that exists here for 2 reasons:
* So I can easily import it in my future projects with Composer
* To show potential employers code samples.

The Query Builder class is tested quite well. Not every SQL use case is covered, but the basic needs such as selects, joins, where, order by, group, max, avg, count are all covered. 

##Documentation
This wil fill up slowly. I'm currently documenting the most important Features.
But feel free to send me messages about something missing and I will expand. Feedback will motivate me to expand on this package.

It's currently still in Beta, tho I've the ORM portion extensively in a few different projects. Tests do cover most of the functionality.

### ORM - Super simple Database mappings
The ORM portion allows you to map Objects to the database with minimal setup. 
Objects just need to extend `AbstractDatabaseObject`. You can now save and query the database.

When just extending `AbstractDatabaseObject` without any extra setup, it will read and write to a database table of your choosing, mapping columns on property names. With a little setup it will use  mapping of your choosing (including casting to your own objects). If that's still not enough you can override the `toArray()` and `fromArray()` functions to do the work yourself. (But that kind of defeats the purpose of having an ORM.)

Examples are found in the `showAndTell` folder. Short versions are found below:

####Example 1: Get all users from the database
```
$allUsersQuery = Qry::select()
    ->from($userTable)
    ->asClass(User::class);

//All users will be an array over User objects.
$allUsers = $database->getRows($allUsersQuery);
```

####Example 2: Get one user from the database
```
$getQuery = Qry::select()
    ->from($userTable)
    ->where('id', '=', 1)
    ->asClass(User::class);

$user = $database->getRow($getQuery);
```

####Example 3: Insert a user to the database
```
$query = Qry::insert($userTable, $user);

$database->insertRow($query);
```

### Database - Easy and safe Database communications
Connect to a MySQL / MariaDB database with PDO. Using the Querybuilder ensures proper prepared statements.

Docs coming soon...

### Qry - The querybuilder
A Querybuilder that aims to stay as close to SQL as possible to minimize the learning curve. Automatically prepares your statements for you.
 
Automatically converts your objects to database-ready arrays and vice-versa.

Docs coming soon... 

###CovleDate - Date comparisons and casting
Docs coming soon...

###Settings - Easy setting saving and loading to a JSON file.
Docs coming soon...

###Dependencies - Simple dependency injection with type hinting
Docs coming soon...

#Todo:
- Bump to version 1.0
- Add a bunch of docs and samples.
- Use polymorphism for Qry. Make Qry abstract, and make select, update, insert and delete extend it.
- Retire old Query class


## QueryBuilder
- Add `->and` and `->or` support for where clauses and Join clauses

#License - MIT
So do whatever you want with it.