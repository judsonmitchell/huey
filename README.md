#huey - An API for Louisiana Statutory Law
## Overview 
This project, huey, hopes to make Louisiana statutory law more readily accessible  to
developers.  It is inspired by [Eric Mill's work](http://radar.oreilly.com/2012/12/the-united-states-code-is-on-github.html)
on the [U.S. Code] (https://github.com/unitedstates).  Although the State of Louisiana
makes its laws [available on the internet](http://legis.la.gov/lss/toc.htm), it's
now impossible for developers to access these laws in a programmatic way.

For starters, I am developing a scraper, written in php, to retrieve and order all 
the Louisiana laws in a database.  After that, I'll move on to developing a RESTful API.

### Scraper
The scraper is still very much experimental.
Usage:
    php index.php

### RESTful API
Still in the planning stages.

### The name?
The project is dedicated to this guy:
![Huey P. Long](http://upload.wikimedia.org/wikipedia/commons/thumb/9/91/HueyPLongGesture.jpg/220px-HueyPLongGesture.jpg)

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
