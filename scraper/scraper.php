<?php
/*
    -----------------------------------------
    huey - Statute Scraper for Louisiana Laws
    -----------------------------------------
    + https://github.com/judsonmitchell/huey
    + Copyright 2012 Judson Mitchell, Three Pipe Problem, LLC
    + MIT License
    
    Uses PHP Simple HTML DOM Parser:
    http://simplehtmldom.sourceforge.net/
*/

require_once('../db.php');
require_once('simple_html_dom.php');

$time_start = microtime(true);

//Function to deal with anomalies in sortcode; remove any data
//which is unncessary or which destroys the sort
function clean_sortcodes($val)
{
    switch ($val) {
        case  substr_count($val,'RS') > 1: //revised statutes; duplicate rs line
            $sortcode = substr($val,10);    
            return $sortcode;
            break;
        case  substr_count($val,'CE') > 1: //code of evidence; duplicate "CE"
            $sortcode = substr($val,3);    
            return $sortcode;
            break;
        case  strstr($val,'CC  000200'): //civil code; mysterious "000200" 
            $sortcode = str_replace('CC  000200','CC',$val);    
            return $sortcode;
            break;
        case  substr_count($val,'CHC') > 1: //children's code; duplicate "CHC"
            $sortcode = substr($val,4);    
            return $sortcode;
            break;
        case  substr_count($val,'CCP') > 1: //code of civil proc.; duplicate "CCP"
            $sortcode = substr($val,4);    
            return $sortcode;
            break;
        case  substr_count($val,'CCRP') > 1: //code of criminal proc; duplicate "CCRP"
            $sortcode = substr($val,5);    
            return $sortcode;
            break;
        case  substr_count($val,'CONST') > 1: //constitution; duplicate "CONST"
            $sortcode = substr($val,13);    
            return $sortcode;
            break;
        case  substr_count($val,'LAC') > 1: //admin code;duplicate "LAC"
            $sortcode = substr($val,11);    
            return $sortcode;
            break;
        default:
            return $val;
            break;
    }
}

echo "Scraping...this could take a while....";
$counter = 0; //number of laws successfully scraped
$errors = 0; //db errors
$docs = 0; //number of urls touched

//Define the ranges of document ids we are requesting; State does not
//appear to have any logic to assigning these ids, but as far as I can
//tell the lowest id is around 66000 and the highest around 750000 
$min = 66000;
$max = 750000;


for ($min; $min <= $max; $min++) {

    $law = file_get_html('http://legis.la.gov/lss/newWin.asp?doc=' . $min);
    if ($law)
    {
        $docs++; //url has been hit
    }

    if (!$law->find('html',0)) //Server returns 'file not found'
    {
        $law->clear(); 
        unset($law);
    }
    else
    {
        //Parse meta tags
        $meta = array();
        foreach($law->find('meta') as $item) {
            $meta[$item->name] = $item->content; 
        }

        //In the revised statutes, the meta tags contain the law title; in 
        //the others, there is no such tag.  So parse the <title> tag
        $title = array();
        foreach ($law->find('title') as $item) {
            $title = $item->innertext;
        }

        //Get the entire body of the law; will use later when applying diff
        //to see if there has been a change
        foreach ($law->find('body') as $b) {
            $body_html = $b->innertext;
        }

        //Parse law body into paragraphs
        $body = array();
        $i = 0;
        foreach ($law->find('p') as $para) {
            $body[$i] = $para->innertext;
            $i++;
        }

        //Serialize body array; will use this later in API to retrieve
        //parts of laws by paragraph number
        $body_string = serialize($body);
       
        //generate an alternative description if meta does not have it
        //Having to find the align attribute is a special bit of fun; 99%
        //of the time, the first paragraph has the description; but sometimes,
        //if it's the start of the chapter, you get chapter name instead; these
        //are, however, aligned center, so just find the first paragraph that is
        //aligned justify
        $first_para = $law->find('p[align="justify"]',0);
        if ($first_para)
        {
            $alt_description = explode('&nbsp;',$first_para->innertext);
        }

        if (isset($meta['description']))
        {
            $description = $meta['description'];
        }
        elseif (isset($alt_description[1]))
        {
            $description = $alt_description[1];
        }
        else
        {
            $description = ''; //all else fails
        }

        //Deal with inconsistent case of sortcode meta tag;
        //sometimes it's capitalized, sometimes not
        if (isset($meta['sortcode']))
        {
            $sortcode = clean_sortcodes($meta['sortcode']);    
        }
        else
        {
            $sortcode = clean_sortcodes($meta['Sortcode']);    
        }

        //Put data in db
        $q = $dbh->prepare("INSERT INTO `lalaws`.`laws` (`id`, `docid`, `title`,
        `description`, `sortcode`, `law_text`, `law_text_elements`, `last_scraped`) 
        VALUES (NULL, :docid, :title, :description, :sortcode, :law_text, 
        :law_text_elements, CURRENT_TIMESTAMP);");
 
        $data = array(
            ':docid' => $min,
            ':title' => $title,
            ':description' => $description,
            ':sortcode' => $sortcode,
            ':law_text' => $body_html,
            ':law_text_elements' => $body_string
            );

        $q->execute($data);

        $error = $q->errorInfo();
        if ($error[1])
        {
            print_r($error);$errors++;
        }
        else
        {
            $counter++;
        }

        $law->clear(); 
        unset($law);
    }
}

//Find execution time
$time_end = microtime(true);
$execution_time = ($time_end - $time_start)/60;

echo "\nScraping complete in " . round($execution_time,2) . " minutes.
$docs urls scanned, $counter statutes added, $errors errors";

