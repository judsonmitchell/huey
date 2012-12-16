<?php

class handle_request
{
    var $dbh;
    var $params;
    //supported sections
    var $books = array(
        'RS',   //Revised Statutes
        'CE',   //Code of Evidence
        'CHC',  //Children's Code
        'LAC',  //La. Admin Code
        'CA',   //Constit.  Amends.
        'CE',   //Code of Evidence
        'CC',   //Civil Code
        'CCRP', //Code of Crim. Pro.
        'CCP',  //Code of Civ. Pro.
        'LAC',  //La. Admin. Code
        'AGO',  //AG Opinions
        'CONST' //La. Constit
        );
    
    public function get_query_type()
    {



    }
    public function generate_sortcodes()
    {



    }
    public function run_query($dbh,$params)
    {
        $q = $dbh->prepare('select * from laws where sortcode like ? limit 10');
        $this->sortcode =  array_shift($params) . "%";
        $q->bindParam(1,$this->sortcode);
        $q->execute();
        $this->r = $q->fetchAll();

    }
    public function return_data()
    {
        $this->output = null;
        foreach ($this->r as $key) {
            $this->output .= $key[1];
        }
        //$this->output =  strtoupper($this->val);
        return $this->output;

    }
 //   if (in_array(strtoupper($params[0]), $books))
 //   {
 //       $book = strtoupper($params[0]);

 //       //if last array element is all numeric characters,
 //       //we are doing are lookup by statute number.  Otherwise,
 //       //we are doing a text search
 //       if (ctype_digit(end($params))) //e.g. api/14/32
 //       {
 //           //strip the book title (RS,CE, etc), leave only section numbers
 //           array_shift($params);

 //           //Sometimes sortcodes are 5 digits, sometimes 6; so we have 
 //           //to provide for both circumstances.
 //           $sections_5 = null;
 //           $sections_6 = null;

 //           foreach ($params as $param) {

 //               $sections6 .= " " .  str_pad($param, 6, '0', STR_PAD_LEFT);
 //               
 //           }

 //           foreach ($params as $param) {

 //               $sections5 .= " " .  str_pad($param, 5, '0', STR_PAD_LEFT);
 //               
 //           }

 //           $sortcode5 = $book . $sections5;
 //           $sortcode6 = $book . $sections6;
 //           $q = $dbh->prepare("select * from laws where sortcode like ? or sortcode like ?");
 //           $q->bindParam(1, $sortcode5);
 //           $q->bindParam(2, $sortcode6);
 //           $q->execute();
 //           $r = $q->fetch(PDO::FETCH_ASSOC);
 //           $error = $q->errorInfo();
 //           $result = null;
 //           if ($error[2]) 
 //           { 
 //               $result =  array('status' => '500 Internal Server Error',
 //               'message' => $error[2]);
 //           }
 //           else if ($q->rowCount() < 1) 
 //           {
 //               $result =  array('status' => '404 Not Found',
 //               'message' => 'No documents matched your request');
 //           }
 //           else 
 //           {
 //               //add status code to beginning of array
 //               $arr = array_reverse($r, true); 
 //               $arr['status'] = '200 OK'; 
 //               $result = array_reverse($arr, true); 
 //           }

 //       }
 //       else if (ctype_alnum(end($params))) //e.g. api/ce/404/character
 //       {
 //           //do a text search
 //           $search_term = array_pop($params); //define the search term and remove
 //           array_shift($params); //take the book off

 //           if (!empty($params)) //there are statute numbers left over
 //           {
 //               //Sometimes sortcodes are 5 digits, sometimes 6; so we have 
 //               //to provide for both circumstances.
 //               $sections_5 = null;
 //               $sections_6 = null;

 //               foreach ($params as $param) {

 //                   $sections6 .= " " .  str_pad($param, 6, '0', STR_PAD_LEFT);

 //               }

 //               foreach ($params as $param) {

 //                   $sections5 .= " " .  str_pad($param, 5, '0', STR_PAD_LEFT);

 //               }
 //               
 //               $sortcode5  = $book . $sortcode_5 . '%';
 //               $sortcode6  = $book . $sortcode_6 . '%';
 //               $q = $dbh->prepare("select from laws where (sortcode like $sortcode5 
 //               or sortcode like $sortcode6) and (description like $searchterm or 
 //               body_text like $searchterm");
 //           }
 //       }
 //       else if (!end($params)) //e.g. api/rs/14/32/
 //       {
 //           //list all entries under title
 //       }
 //       else
 //       {
 //           $result =  array('status' => '400 Bad Request',
 //           'message' => 'Huey did not understand your request');
 //       }
 //       
 //   }
 //   else
 //   {
 //       //e.g. api/burglary
 //       //search the entire database for the search term
 //   }

 //   return $result;
}
