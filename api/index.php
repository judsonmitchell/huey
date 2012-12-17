<?php
/*
    -----------------------------------------
    huey - API for Louisiana Laws
    -----------------------------------------
    + https://github.com/judsonmitchell/huey
    + Copyright 2012 Judson Mitchell, Three Pipe Problem, LLC
    + MIT License
    
*/

/*
What does an API request look like?:

Look for an individual statute:
    http://.../huey/api/RS/14/35/1 
    http://.../huey/api/STATUTEGROUP/ID1/ID2/ID3

    ID1 = first grouping in sortorder
    ID2 = second grouping in sortorder
    ID3 = third grouping in sortorder

These ids are identified only by numbers

OR 
Do a search
http://..../huey/api/SEARCHTERM
http://..../huey/api/cch/SEARCHTERM
searches entire children's code for search term

http://.../huey/api/RS/14/SEARCHTERM
searches all of criminal code for search term
 */

require_once('../db.php');
require_once('request.class.php');


if ($_SERVER['REQUEST_METHOD'] !== 'GET') //we need only support GET
{
    header("{$_SERVER['SERVER_PROTOCOL']} 405 Method Not Allowed");
    die();
}

$query = new handle_request();
$query->run_query($dbh);
echo $query->return_data();

