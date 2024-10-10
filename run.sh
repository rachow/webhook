#!/bin/bash

# $rachow - grab the path to PHP executable or just php should locate it.
# get `which php` then start the development server!

# or php="which php"
# path=`pwd`

start chrome http://localhost:9090
php -S localhost:9090 -t public/
