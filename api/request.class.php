<?php
/**
 * Huey - An API for Louisiana Statutory Laws
 *
 * This is the class which handles requests to 
 * Huey.  At initialization, it parses the request
 * sent from the client and returns a json encoded
 * result.
 *
 * @author Judson Mitchell <judsonmitchell@gmail.com>
 * @copyright 2012 Judson Mitchell, Three Pipe Problem, LLC
 * @url https://github.com/judsonmitchell/huey
 * @license MIT
 */

class handle_request
{
    //supported sections
    public $books = array(
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

     public function __construct() {
     //Get the parameters

         parse_str($_SERVER['QUERY_STRING'],$query_string); //check for query string

         if (count($query_string) > 0)
         {
             $params = $query_string;
         }
         else
         {
             $params = explode('/', $_SERVER['PATH_INFO']);
         }

         array_shift($params);
         $this->params = $params;
         $this->generate_sortcodes();
    }

   
    public function generate_sortcodes()
    {
        if (in_array(strtoupper($this->params[0]), $this->books) && ctype_digit(end($this->params)))
        {  //book specific, e.g, api/rs/14/49
            $this->sortcode = array_shift($this->params);
            foreach ($this->params as $val) {
                $this->sortcode .= " %" . str_pad($val, 5, '0', STR_PAD_LEFT) ;
            }
        }
        else if ( in_array(strtoupper($this->params[0]), $this->books) && ctype_alnum(end($this->params)))
        {   //book search, e.g, api/rs/14/intent
            $this->sortcode = array_shift($this->params);
            $search = array_pop($this->params);
            foreach ($this->params as $val) {
                $this->sortcode .= " %" .  str_pad($val, 5, '0', STR_PAD_LEFT);
            }
            $this->sortcode .= '%';
            $this->searchterm = "%$search%";
        } 
        else if (!end($this->params)) //book list, e.g. api/rs/14/32/
        {
            $this->sortcode = array_shift($this->params);
            foreach ($this->params as $val) {
                $this->sortcode .= " %" .  str_pad($val, 5, '0', STR_PAD_LEFT);
            }
            $this->sortcode .= "%";
        
        }
        else if (!in_array(strtoupper($this->params[0]))) //global search e.g, api/burglary
        {
            $this->sortcode = false;
            $this->searchterm = "%" . $this->params[0] . "%";
        }
        else
        {
            $this->status =  '500 Internal Server Error';
            $this->message = 'Huey did not understand your request';
            $this->fail = true;
        }
        
    }

    public function run_query($dbh)
    {
        if ($this->searchterm)
        {
            if ($this->sortcode)
            {
                $q = $dbh->prepare("select * from laws where sortcode like :sortcode and
                (description like :searchterm or law_text like :searchterm)"); 
                $data = array('sortcode' => $this->sortcode, 'searchterm' => $this->searchterm);
            }
            else
            {
                $q = $dbh->prepare("select * from laws where description like :searchterm
                or law_text like :searchterm");
                $data = array('searchterm' => $this->searchterm);
            }
        }
        else
        {
            $q = $dbh->prepare("select * from laws where sortcode like :sortcode");
            $data = array('sortcode' => $this->sortcode);
        }
        $q->execute($data);
        $error = $q->errorInfo();
        if ($error[2])
        {
            $this->status =  '500 Internal Server Error';
            $this->message = $error[2];
            $this->fail = true;
        }
        else if ($q->rowCount() < 1)
        {
            $this->status =  '404 Not Found';
            $this->message = 'No documents matched your request';
            $this->fail = true;
        }
        else
        {
            $this->status = '200 OK';
            $this->message = '';
            $this->fail = false;
        }
        $this->result = $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function return_data()
    {
        header($_SERVER['SERVER_PROTOCOL'] . " " .  $this->status);
        if ($this->fail)
        {
            $this->data = array($this->status,$this->message);
        }
        else
        {
            //add status code to beginning of array
            $arr = array_reverse($this->result, true); 
            $arr['status'] = $this->status; 
            $this->data = array_reverse($arr, true); 

        }

        return json_encode($this->data);
    }
} 
