<?php
/**
 * Huey - An API for Louisiana Statutory Laws
 *
 * This is the scraper which retrieves the laws
 * from the Legislature's database. It has been
 * updated to reflect changes to the Legislature's 
 * html in 2015
 * 
 * Uses PHP Simple HTML DOM Parser:
 * http://simplehtmldom.sourceforge.net/
 *
 * @author Judson Mitchell <judsonmitchell@gmail.com>
 * @copyright 2012, 2015 Judson Mitchell, Three Pipe Problem, LLC
 * @url https://github.com/judsonmitchell/huey
 * @license MIT
 */

//ini_set('default_socket_timeout',300); //5 minutes
ini_set("log_errors", 1);
ini_set("error_log", "error.log");
error_log( "Huey scraper error log:" );

require_once('../db.php');
require_once('simple_html_dom.php');


function make_sort_code($val){

    $parts = explode(' ', $val);
    $book = $parts[0];
    $dot = strrpos($parts[1], '.'); 
    $colon = strrpos($parts[1], ':');
    $subsubsection = null;
    if ($dot){
        if (substr_count($parts[1],'.') === 2){ //eg LA RS 14:35.5.1
            $first_dot = strpos($parts[1], '.');
            $length = $dot - $first_dot -1;
            $subsection = substr($parts[1], $first_dot + 1, $length); 
            $subsubsection = substr($parts[1],$dot + 1);
        } else {
            $subsection = substr($parts[1],$dot + 1); 
        }
    } else {
        $subsection = null;
    }

    if ($colon && $dot){
        $title = substr($parts[1],0, $colon); 
        if (substr_count($parts[1],'.') === 2){ //eg LA RS 14:35.5.1
            $first_dot = strpos($parts[1], '.');
            $length = $first_dot - $colon -1;
            $section = substr($parts[1], $colon + 1, $length);
        } else {
            $length = $dot - $colon -1; 
            $section = substr($parts[1], $colon + 1, $length);
        }
    } else if ($colon){
        $title = substr($parts[1],0, $colon); 
        $section = substr($parts[1], $colon +1);  
    } else if ($dot){
        $title = substr($parts[1],0, $dot); 
        $section = null; 
    } else {
        if (substr_count($val, ' ') === 2){ //CONST is different
            $title = $parts[1];
            $section = $parts[2];
        } else {
            $title = $parts[1]; 
            $section = null;
        }
    }

    $sortcode = $book . ' ' . 
    str_pad($title, 6, '0', STR_PAD_LEFT); 
    if ($section){
        $sortcode .= ' ' . str_pad($section, 6, '0', STR_PAD_LEFT); 
    }
    if ($subsection){
        $sortcode .= ' ' . str_pad($subsection, 6, '0', STR_PAD_LEFT);
    }

    if ($subsubsection){
        $sortcode .= ' ' . str_pad($subsubsection, 6, '0', STR_PAD_LEFT);
    }
    return $sortcode;
}
echo "Scraping...this could take a while....";
$time_start = microtime(true);
$counter = 0; //number of laws successfully scraped
$errors = 0; //db errors
$docs = 0; //number of urls touched

//Define the ranges of document ids we are requesting; State does not
//appear to have any logic to assigning these ids, but as far as I can
//tell the lowest id is around 66000 and the highest around 750000 
//$min = 66000;
//$max = 750000;
$min = 66000;
$max = 750000;

for ($min; $min <= $max; $min++) {

    $law = file_get_html('https://www.legis.la.gov/legis/LawPrint.aspx?d=' . $min);

    if (is_object($law)) {

        $docs++; //url has been hit

        //Legislature's version of a 404 ;) 
        if ($law->find('div[id=divError]') || $law->find('a[id=ctl00_MainMenu_SkipLink]')) {
            $law->clear(); 
            unset($law);
        } else {

            if ($law->find('span[id=LabelName]')){
                $get_title = $law->find('span[id=LabelName]');
                $title = $get_title[0]->innertext;
            }

            if ($law->find('span[id=LabelDocument]')){
                $get_body = $law->find('span[id=LabelDocument]');
                $body = $get_body[0]->innertext;
            }

            //Strip all class and align attributes from p http://stackoverflow.com/a/3026111/49359
            if ($body){
                $body_html = str_get_html($body);
                foreach ( $body_html->find('p') as $value ){
                    $value->class = null;
                    $value->align = null;
                }
            } else {
                $body_html = '';
            }

            if ($law->find('span[id=LabelDocument] p',0)){
                //Turns out the first paragraph does not always
                //contain the law description. Sometimes, it has the Title and
                //chapter name in all caps. So loop through paragraphs until
                //we find one that is not all caps.
                for ($i = 0; $i < 10; $i++) {
                    $el =  $law->find('span[id=LabelDocument] p',$i);
                    if (is_object($el)) {
                        if (strtoupper(str_replace('&nbsp;','', $el->plaintext)) === str_replace('&nbsp;','',$el->plaintext)) {
                            continue;
                        } else {
                            $get_description = $law->find('span[id=LabelDocument] p',$i);
                            $description = $get_description->plaintext;
                            break;
                        }
                    }
                }

            } else {
                $description = '';
            }

            $sortcode = make_sort_code($title);
            
            //Only save the laws we are interested in
            $whitelist = array('CC', 'CCP', 'CHC', 'CJP', 'CA', 'CCRP', 'CE', 'RS', 'CONST');
            
            $b = explode(' ', $sortcode);
            if (!in_array($b[0], $whitelist)){
                echo "\nSortcode $sortcode was skipped \n";
                $law->clear(); 
                unset($law);
            } else {
                //Put data in db
                $q = $dbh->prepare("INSERT INTO `laws` (`id`, `docid`, `title`,
                `description`, `sortcode`, `law_text`,`last_scraped`) 
                VALUES (NULL, :docid, :title, :description, :sortcode, :law_text, 
                CURRENT_TIMESTAMP);");
        
                $data = array(
                    'docid' => $min,
                    'title' => $title,
                    'description' => $description,
                    'sortcode' => $sortcode,
                    'law_text' => $body_html
                    );

                $q->execute($data);

                $error = $q->errorInfo();
                if ($error[1]) {
                    print_r($error);$errors++;
                } else {
                    $counter++;
                }

                $law->clear(); 
                unset($law);
            }
        }
    } 
}

//If it's a fresh db remove duplicates;
//$q = $dbh->prepare('ALTER IGNORE TABLE laws ADD UNIQUE (title, id)');
//$q->execute();

//Find execution time
$time_end = microtime(true);
$execution_time = ($time_end - $time_start)/60;

echo "\nScraping complete in " . round($execution_time,2) .
" minutes.  $docs urls scanned, $counter statutes added, $errors errors"; 
