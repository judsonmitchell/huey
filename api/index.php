<?php
/**
 * Huey - An API for Louisiana Statutory Laws
 *
 * This is the controller which handles requests to 
 * Huey.  It calls the request.class.php and returns
 * a json encoded result. As of now, Huey expects a
 * RESTful request like so:
 * api/[book]/[title]/[section]/[subsection]/[searchterm]
 *
 * Sample Requests:
 * api/ce/404 -returns Code of Evidence Art. 404
 * api/rs/15/529/1 -returns La. RS. 15:529.1
 * api/rs/14/habeas -searches title 14 for the word 'habeas'
 *
 * @author Judson Mitchell <judsonmitchell@gmail.com>
 * @copyright 2012 Judson Mitchell, Three Pipe Problem, LLC
 * @url https://github.com/judsonmitchell/huey
 * @license MIT
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

