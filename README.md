#huey - An API for Louisiana Statutory Law
### Overview 
This project, huey, hopes to make Louisiana statutory law more readily accessible  to
developers.  It is inspired by [Eric Mill's work](http://radar.oreilly.com/2012/12/the-united-states-code-is-on-github.html)
on the [U.S. Code] (https://github.com/unitedstates).  Although the State of Louisiana
makes its laws [available on the internet](http://legis.la.gov/lss/toc.htm), it's
now impossible for developers to access these laws in a programmatic way.

### Scraper
The scraper scans the [Louisiana Legislature Site](http://legis.la.gov) and adds the laws
to a mysql or sqlite db.  There is still some polishing to do on this (removing duplicates,
for example), but the scraper generally works well.

Usage:

    php scraper/scraper.php

### RESTful API
This API is still very much in beta.  It takes requests in the following format:

    http://hueylaw.org/api/[book]/[title]/[section]/[subsection]/[searchterm]
    
Sample Requests:

    http://hueylaw.org/api/ce/404 //returns Code of Evidence Art. 404
    http://hueylaw.org/api/rs/15/529/1 //returns La. RS. 15:529.1
    http://hueylaw.org/api/rs/14/habeas //searches title 14 for the word 'habeas'
    http://hueylaw.org/api/succession //searches entire db for 'succession' 

### The name?
The project is dedicated to this guy:

![Image of Huey P. Long](http://upload.wikimedia.org/wikipedia/commons/thumb/9/91/HueyPLongGesture.jpg/220px-HueyPLongGesture.jpg "We gonna 
stick it to Standard Oil!")

>One of these days the people of Louisiana 
>are going to get good government -
>and they aren't going to like it.
-- Huey P. Long

### License (MIT)
Copyright (c) 2012 Judson Mitchell, Three Pipe Problem, LLC

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to 
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
