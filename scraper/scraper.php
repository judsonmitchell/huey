<?php
/**
 * Huey - An API for Louisiana Statutory Laws
 *
 * This is the scraper which retrieves the laws
 * from the Legislature's database.
 * 
 * Uses PHP Simple HTML DOM Parser:
 * http://simplehtmldom.sourceforge.net/
 *
 * @author Judson Mitchell <judsonmitchell@gmail.com>
 * @copyright 2012 Judson Mitchell, Three Pipe Problem, LLC
 * @url https://github.com/judsonmitchell/huey
 * @license MIT
 */

//ini_set('default_socket_timeout',300); //5 minutes
ini_set("log_errors", 1);
ini_set("error_log", "error.log");
error_log( "Huey scraper error log:" );

require_once('../db.php');
require_once('simple_html_dom.php');

$time_start = microtime(true);

//Function to deal with anomalies in sortcode; remove any data
//which is unncessary or which destroys the sort
function clean_sortcodes($val)
{
    //make sure sortcode is six digits
    $parts = explode(" ",$val);
    array_walk($parts, function(&$p){
        if (ctype_digit($p)) {
            $p = str_pad($p, 6, '0', STR_PAD_LEFT);
        }
    });

    //strip out cruft
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
        case  substr_count($val,'CA') > 1: //constit. amends; duplicate "CA" 
            $sortcode = substr($val,3);    
            return $sortcode;
            break;
        case  substr_count($val,'ERC') > 1: //ERC?; duplicate "ERC" 
            $sortcode = substr($val,4);    
            return $sortcode;
            break;
        case  substr_count($val,'CJP') > 1: //duplicate "CJP" 
            $sortcode = substr($val,4);    
            return $sortcode;
            break;
        default:
            return $val;
            break;
    }

}

function make_sort_code($val){

    /*
    Possible parts:
        [book] [title]:[section].[subsection]
    problem is we can also have:
        [book] [title].[subsection]
    */
    $parts = explode(' ', $val);
    $book = $parts[0];
    $dot = strrpos($parts[1], '.'); 
    $colon = strrpos($parts[1], ':');
    if ($dot){
        $subsection = substr($parts[1],$dot + 1); 
    } else {
        $subsection = null;
    }

    if ($colon && $dot){
        $title = substr($parts[1],0, $colon); 
        $length = $dot - $colon -1; 
        $section = substr($parts[1], $colon + 1, $length);
    } else if ($colon){
        $title = substr($parts[1],0, $colon); 
        $section = substr($parts[1], $colon +1);  
        //$length = $dot - $colon -1; 
        //$section = substr($parts[1], $colon + 1, $length);
    } else if ($dot){
        $title = substr($parts[1],0, $dot); 
        $section = null; 
    } else {
        $title = $parts[1]; 
        $section = null;
    }

    $sortcode = $book . ' ' . 
    str_pad($title, 6, '0', STR_PAD_LEFT); 
    if ($section){
        $sortcode .= ' ' . str_pad($section, 6, '0', STR_PAD_LEFT); 
    }
    if ($subsection){
        $sortcode .= ' ' . str_pad($subsection, 6, '0', STR_PAD_LEFT);
    }
    return $sortcode;
}
echo make_sort_code('RS 14:103.1') . "\n";
echo make_sort_code('CC 103.1') . "\n";
echo make_sort_code('CC 15:143') . "\n";
echo make_sort_code('CC 4512');die;
echo "Scraping...this could take a while....";
$counter = 0; //number of laws successfully scraped
$errors = 0; //db errors
$docs = 0; //number of urls touched

//Define the ranges of document ids we are requesting; State does not
//appear to have any logic to assigning these ids, but as far as I can
//tell the lowest id is around 66000 and the highest around 750000 
//$min = 66000;
//$max = 750000;
$min = 108528;
$max = 919602;

for ($min; $min <= $max; $min++) {

    $law = file_get_html('https://www.legis.la.gov/legis/LawPrint.aspx?d=' . $min);

    if (is_object($law)) {

        $docs++; //url has been hit

        if ($law->find('div[id=divError]')) { //Server returns 'file not found'
            $law->clear(); 
            unset($law);
        } else {
            //Parse meta tags
            //$meta = array();
            //foreach($law->find('meta') as $item) {
            //    $meta[$item->name] = $item->content; 
            //}

            //In the revised statutes, the meta tags contain the law title; in 
            //the others, there is no such tag.  So parse the <title> tag
            //$title = array();
            //foreach ($law->find('title') as $item) {
                //$title = $item->innertext;
            //}

            $get_title = $law->find('span[id=LabelName]');
            $title = $get_title[0]->innertext;
            //Get the entire body of the law; will use later when applying diff
            //to see if there has been a change
            //foreach ($law->find('body') as $b) {
                //$body = $b->innertext;
            //}

            $get_body = $law->find('div[id=divLaw]');
            $body = $get_body[0]->innertext;
            //Strip all class and align attributes from p http://stackoverflow.com/a/3026111/49359
            $body_html = str_get_html($body);
            foreach ( $body_html->find('p') as $value ){
                $value->class = null;
                $value->align = null;
            }

            //generate an alternative description if meta does not have it
            //Having to find the align attribute is a special bit of fun; 99%
            //of the time, the first paragraph has the description; but sometimes,
            //if it's the start of the chapter, you get chapter name instead; these
            //are, however, aligned center, so just find the first paragraph that is
            //aligned justify
            //$first_para = $law->find('p[align="justify"]',0);
            //if ($first_para)
            //{
                //$alt_description = explode('&nbsp;',$first_para->innertext);
            //}

            //if (isset($meta['description']))
            //{
                //$description = $meta['description'];
            //}
            //elseif (isset($alt_description[1]))
            //{
                //$description = $alt_description[1];
            //}
            //else
            //{
                //$description = ''; //all else fails
            //}

            ////Deal with inconsistent case of sortcode meta tag;
            ////sometimes it's capitalized, sometimes not
            //if (isset($meta['sortcode']))
            //{
                //$sortcode = clean_sortcodes($meta['sortcode']);    
            //}
            //else
            //{
                //$sortcode = clean_sortcodes($meta['Sortcode']);    
            //}

            $get_description = $law->find('span[id=LabelDocument] p',0);
            $description = $get_description->plaintext;

            $sortcode = make_sort_code($title);
            //Put data in db
            echo "docid: " . $min . "\n";
            echo "title: " . $title . "\n";
            echo "description: " . $description . "\n";
            echo "sortcode: " . $sortcode . "\n";
            echo "======================";
            $q = $dbh->prepare("INSERT INTO `laws` (`id`, `docid`, `title`,
            `description`, `sortcode`, `law_text`,`last_scraped`) 
            VALUES (NULL, :docid, :title, :description, :sortcode, :law_text, 
            CURRENT_TIMESTAMP);");
    
            $data = array(
                ':docid' => $min,
                ':title' => $title,
                ':description' => $description,
                ':sortcode' => $sortcode,
                ':law_text' => $body_html
                );

            //$q->execute($data);

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
}

//Find execution time
$time_end = microtime(true);
$execution_time = ($time_end - $time_start)/60;

echo "\nScraping complete in " . round($execution_time,2) .
" minutes.  $docs urls scanned, $counter statutes added, $errors errors"; 
