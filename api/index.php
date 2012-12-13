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
error_reporting(E_ALL);
function handle_request($dbh,$params)
{
    //print_r($params);die;
    $books = array('RS','CE','CCH','LAC');//supported sections
    
    if (in_array(strtoupper($params[0]), $books))
    {
        //if last array element is all numeric characters,
        //we are doing are lookup by statute number.  Otherwise,
        //we are doing a text search
        if (ctype_digit(end($params)))
        {
            $book = strtoupper($params[0]);
            //strip the book title (RS,CE, etc), leave only section numbers
            array_shift($params);
            $sections = null;
            foreach ($params as $param) {

                $sections .= " " .  str_pad($param, 6, '0', STR_PAD_LEFT);
                
            }
            
            $sortcode = $book . $sections;
            $q = $dbh->prepare("select * from laws where sortcode like ?");
            $q->bindParam(1, $sortcode);
            $q->execute();
            $result = $q->fetch(PDO::FETCH_ASSOC);
            $error = $q->errorInfo();
            //if ($error[1]) { 
            //    return array('status' => '500 Internal Server Error','message' => $error[2]);}
            //if ($result->rowCount() < 1) {
            //    return array('status' => '404 Not Found','message' => 'No documents matched your request');}
            //else {
            //    $result['status'] = '200 OK';
            //    return $result;
            //}
            return $result;
        }
    }
} 

if ($_SERVER['REQUEST_METHOD'] !== 'GET') //we need only support GET
{
    header("{$_SERVER['SERVER_PROTOCOL']} 405 Method Not Allowed");
    die();
}

//Get the parameters

parse_str($_SERVER['QUERY_STRING'],$query_string); //check for query string

if (count($query_string) > 0)
{
    $params = $query_string;
}
else
{
    $params = explode('/', trim($_SERVER['PATH_INFO'],'/'));
}

$result = handle_request($dbh,$params);
//header("{$_SERVER['SERVER_PROTOCOL']}" . $result['status']);
echo json_encode($result);

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




