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

function handle_request($dbh,$params)
{
    //supported sections
    $books = array(
        'RS',   //Revised Statutes
        'CE',   //Code of Evidence
        'CCH',  //Children's Code
        'LAC',  //La. Admin Code
        'CA'    //Constit.  Amends.
        );
    
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

            //Sometimes sortcodes are 5 digits, sometimes 6; so we have 
            //to provide for both circumstances.
            $sections_5 = null;
            $sections_6 = null;

            foreach ($params as $param) {

                $sections6 .= " " .  str_pad($param, 6, '0', STR_PAD_LEFT);
                
            }

            foreach ($params as $param) {

                $sections5 .= " " .  str_pad($param, 5, '0', STR_PAD_LEFT);
                
            }

            $sortcode5 = $book . $sections5;
            $sortcode6 = $book . $sections6;
            $q = $dbh->prepare("select * from laws where sortcode like ? or sortcode like ?");
            $q->bindParam(1, $sortcode5);
            $q->bindParam(2, $sortcode6);
            $q->execute();
            $r = $q->fetch(PDO::FETCH_ASSOC);
            $error = $q->errorInfo();
            $result = null;
            if ($error[2]) 
            { 
                $result =  array('status' => '500 Internal Server Error','message' => $error[2]);
            }
            else if ($q->rowCount() < 1) 
            {
                $result =  array('status' => '404 Not Found','message' => 'No documents matched your request');
            }
            else 
            {
                //add status code to beginning of array
                $arr = array_reverse($r, true); 
                $arr['status'] = '200 OK'; 
                $result = array_reverse($arr, true); 
            }

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
header($_SERVER['SERVER_PROTOCOL'] . " " .  $result['status']);
echo json_encode($result);

