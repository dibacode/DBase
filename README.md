# DBase

A simple php class for quickly and safely connection to MySQL database using the mysqli extension, useful for simple projects.


## Usage

##### 1. Using the CREATE command

```php
<?php 
  # Database Class
    require_once("DBase.class.php");

    global $dbclass;

    // Debuger
    $dbclass->debug = true;

    // Initialize database
    $dbclass->init( 'localhost', 'db_name', 'root', 'db_password', 'utf8', 'utf8_general_ci' );

    $db_charset = $dbclass->get_charset_collate();

    $query = "CREATE TABLE IF NOT EXISTS `people` (
      `PersonId` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `Name` varchar(45) NOT NULL,
      `Age` int(10) unsigned NOT NULL,
      `RecordDate` datetime NOT NULL,
      PRIMARY KEY (`PersonId`)
    ) ENGINE=InnoDB " . $db_charset . ";" ; 

    # Execute the CREATE command
    $dbclass->query( $query );

    if( $error = $dbclass->get_error() ){
        var_dump( $error );

    }else{
        echo "The table created. <br />";

    }
?>
```


##### 1. Using the INSERT command

```php
<?php 

  $values = array( 'Douglas Adams', 42 );
    $query = $dbclass->prepare("INSERT INTO `people` (`Name`, `Age`, `RecordDate`) VALUES( %s, %d, now() ) ;", $values);

    # Execute the INSERT command
    $dbclass->query( $query );

    if( $error = $dbclass->get_error() ){
        var_dump( $error );

    }else{
        echo "The person inserted. <br />";

    }
?>
```


##### 3. Using the SELECT command

```php
<?php 
  $person_id = array( 1 );
    $query = $dbclass->prepare("SELECT * FROM people WHERE PersonId = %d ;", $person_id );

    # Execute the SELECT command
    $results = $dbclass->get_results( $query );

    if( $error = $dbclass->get_error() ){
        var_dump( $error );

    }else{
        var_dump( $results );

        $results = $results[0];
        echo "hello, " . $results->Name;
    }
?>
```


## License

The MIT License (MIT) Copyright (c) 2015 DibaCode Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions: The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software. THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.