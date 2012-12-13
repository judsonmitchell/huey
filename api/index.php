<?php
/*
    -----------------------------------------
    huey - API for Louisiana Laws
    -----------------------------------------
    + https://github.com/judsonmitchell/huey
    + Copyright 2012 Judson Mitchell, Three Pipe Problem, LLC
    + MIT License
    
*/

require_once('../db.php');

function handle_request()
{
    $request_type = $_SERVER['REQUEST_METHOD'];

    switch ($request_type) {
        case 'PUT':
            // code...
            break;
        case 'DELETE':
            // code...
            break;
        case 'POST':
            // code...
            break;
        case 'GET':
            // code...
            break;
        default:
            // code...
            break;
    }


}
if (isset($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $parameters);
    print_r($parameters);
    echo "shit";
}

$verb = $_SERVER['REQUEST_METHOD'];
$url_elements = explode('/', $_SERVER['PATH_INFO']);

echo $verb;
print_r($url_elements);

//What does an API request look like?:

//Look for an individual statute:
//    http://.../huey/api/RS/14/35/1 
//    http://.../huey/api/STATUTEGROUP/ID1/ID2/ID3
//
//    ID1 = first grouping in sortorder
//    ID2 = second grouping in sortorder
//    ID3 = third grouping in sortorder
//
//These ids are identified only by numbers
//
//OR 
//Do a search
//    http://..../huey/api/SEARCHTERM
//    http://..../huey/api/SEARCHTERM
//searches entire children's code for search term
//
//    http://.../huey/api/RS/14/SEARCHTERM
//searches all of criminal code for search term




