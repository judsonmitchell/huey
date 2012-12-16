<?php

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
    
    public function get_query_type($params)
    {
        if (in_array(strtoupper($params[0]), $this->books) && ctype_digit(end($params)))
        {
            $this->type = 'book_specific';
        }
        else if ( in_array(strtoupper($params[0]), $this->books) && ctype_alnum(end($params)))
        {
            $this->type = 'book_search';
        }
        else if (!end($params)) //e.g. api/rs/14/32/
        {
            $this->type = 'book_list';
        }
        else if (!in_array(strtoupper($params[0])))
        {
            $this->type = 'global_search';
        }
        else
        {
            $this->type = 'fail';
        }

    }
    public function generate_sortcodes()
    {

        switch ($this->type) {
            case 'book_specific':
                return 'this was a book specific';
                   //$sections6 .= " " .  str_pad($param, 6, '0', STR_PAD_LEFT);
                break;
            case 'book_search':
                return 'this was a book search';
                break;
            case 'book_list':
                return 'this was a book list';
                break;
            case 'global_search':
                return 'this was a global search';
                break;
            default:
                // code...
                break;
        }
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
} 
