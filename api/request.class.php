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
             if (in_array('fuzzy',$params)) // api/index.php?type=fuzzy$s=liability
             {
                $this->params = $this->fuzzy_request($params);
             }
             
             if (in_array('docid',$params)) // specific document api/index.php?type=docid&s=23244
             {

                $this->docid = $params['s'];
             }
         }
         else
         {
             $params = explode('/', $_SERVER['PATH_INFO']); //REST request api/15/529/1
             array_shift($params);
             $this->params = $params;
                //print_r($this->params);
         }

         $this->generate_sortcodes();
    }

    public function match_books($needle,$haystack)
    {
        foreach ($haystack as $item) {
            foreach ($item as $i) {
                if (stristr($needle,$i))
                {
                    return array($item[0],$i);
                }
            }
        }
        return false;
    }

    public function fuzzy_request($params) //handle "natural language" request
    {
                
        $parsed = array();  //array which will be returned by this function
        $matched = array(); //array that will contain parts of string that have been matched

        //define common citation words;looked at other methods of doing this (levenshtein, etc),
        //but a dumb list of potential words seems best at the moment.
        $citation_words = array(
            array('rs ','revised statutes' , 'rev stat','r.s.'),
            array('ce ','code of evidence','c.e.'),
            array('chc ','childrens code','ch code'),
            array('lac ','louisiana administrative code','admin code','la admin code'),
            array('ca ','constitutional amendment','con amend','con. amend.'),
            array('cc ','civil code','cc art'),
            array('ccrp ','code of criminal procedure','code of crim pro','c cr p'),
            array('ccp ', 'code of civil procedure','code of civil pro','civ pro','c c p'),
            array('lac ','louisiana administrative code','la admin code'),
            array('ago ','attorney general opinion','ag opinion','ag op'),
            array('const ','louisiana constitution','la constitution','la constit')
        );
            
        //if words in the query match our citation words, add the book to the parsed array;
        //also add the matched element to an array so that we can remove it from the query later
        if ($r = $this->match_books($params['s'],$citation_words))
        { 
            array_push($parsed, trim(strtoupper($r[0])));
            array_push($matched,$r[1]);
        }

        //look for numbers, add them to parsed array 
        preg_match_all('!\d+!', $params['s'], $matches);
        if (count($matches) > 0)
        {
            foreach ($matches[0] as $match) {
                array_push($parsed,$match);
                array_push($matched,$match);
            }
        }

       //Remove unnecessary punctuation and words
       array_push($matched,' la ',' louisiana ',':',',','.',' sec ',' section ');
       $search = str_ireplace($matched, '',$params['s']);

       if(!ctype_space($search))
       {
           //anything left over, put in search
           array_push($parsed,trim($search));
       }
       return $parsed;
    }

    public function generate_sortcodes()
    {
        $this->preview = true;

        if (in_array(strtoupper($this->params[0]), $this->books) && ctype_digit(end($this->params)))
        {  //book specific, e.g, api/rs/14/49
            $this->sortcode = array_shift($this->params);
            foreach ($this->params as $val) {
                $this->sortcode .= " %" . str_pad($val, 5, '0', STR_PAD_LEFT) ;
            }

            $this->preview = false;
        }
        else if ( in_array(strtoupper($this->params[0]), $this->books) && ctype_alnum(end(str_replace(' ','',$this->params))))
        {   //book search, e.g, api/rs/14/intent
            $this->sortcode = array_shift($this->params);
            $search = array_pop($this->params);
            foreach ($this->params as $val) {
                $this->sortcode .= " %" .  str_pad($val, 5, '0', STR_PAD_LEFT);
            }
            $this->sortcode .= '%';
            $this->searchterm = "%$search%";
        } 
        else if (!end($this->params)) //book list, e.g. api/rs/14/ - list all laws in title 14
        {
            $this->sortcode = array_shift($this->params);
            foreach ($this->params as $val) {
                $this->sortcode .= " %" .  str_pad($val, 5, '0', STR_PAD_LEFT);
            }
            $this->sortcode .= "%";
        
        }
        else if (!in_array(strtoupper($this->params[0]),$this->books)) //global search e.g, api/burglary
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

    public function highlight($needle, $haystack){ 
        $ind = stripos($haystack, $needle); 
        $len = strlen($needle); 
        if($ind !== false){ 
            return substr($haystack, 0, $ind) . "<span class='highlight'>" . substr($haystack, $ind, $len) . "</span>" . 
                $this->highlight($needle, substr($haystack, $ind + $len)); 
        } else return $haystack; 
    } 

    public function excerpt($text, $phrase, $radius = 100, $ending = "...") {
        $phraseLen = strlen($phrase);
        if ($radius < $phraseLen) {
            $radius = $phraseLen;
        }

        $pos = strpos(strtolower($text), strtolower($phrase));

        $startPos = 0;
        if ($pos > $radius) {
            $startPos = $pos - $radius;
        }

        $textLen = strlen($text);

        $endPos = $pos + $phraseLen + $radius;
        if ($endPos >= $textLen) {
            $endPos = $textLen;
        }

        $excerpt = substr($text, $startPos, $endPos - $startPos);
        if ($startPos != 0) {
            $excerpt = substr_replace($excerpt, $ending, 0, $phraseLen);
        }

        if ($endPos != $textLen) {
            $excerpt = substr_replace($excerpt, $ending, -$phraseLen);
        }

        return $this->highlight($phrase, $excerpt);
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
        else if($this->docid)
        {

                $this->preview = false; //turn off previews
                $q = $dbh->prepare("select * from laws where docid = :docid");
                $data = array('docid' => $this->docid);
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
            $this->data = array('status' => $this->status, 'message' => $this->message);
        }
        else
        {
            if ($this->preview === true)
            {
                foreach ($this->result as $key=>$rr) {
                    $snippet = $this->excerpt($rr['law_text'],trim($this->searchterm, '%'));
                    $preview = array('law_text' => $snippet);
                    $url = array("law_url","http://hueylaw.org/api?docId=" . $rr['docid']);
                    $rr = array_replace($rr,$preview,$url);
                    $this->data[$key] = $rr;
                }
            }
            else
            {
                $this->data = $this->result;
            }
            
        }

        return json_encode($this->data);
    }
} 
