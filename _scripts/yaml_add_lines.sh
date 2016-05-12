#!/bin/bash
for i in $(ls *.html); do sed '2i\
layout:      profile\
lang:        en\
' $i; done
