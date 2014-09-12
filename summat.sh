#!/bin/bash
#===============================================================================
#          FILE:  summat.sh
#         USAGE:  ./summat.sh 
#   DESCRIPTION:  Just a background script that outputs "summat" at 
#                 regular intervals
##===============================================================================

n=1 while [[ $n -lt 51 ]]; do echo summat; sleep 60; ((n++)); done &

