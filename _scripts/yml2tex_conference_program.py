# _________________________________________________________________________
# This script...
# 
# ... extracts yaml data from a conference file and 
# Creates a pdf program. It also includes title, dates, and 
# the types of contributions.
#
# _________________________________________________________________________
# Things to edit in this file are
#
# - fbase, just below (first argument to script can be html file, though)
# - LaTeX header addtolengths 
# - The \\\\[<>cm] in the title
#
# _________________________________________________________________________
# Troubleshooting
#
# - if yaml cannot be imported:
#       run this script inside ipython rather than from shell if
#
# - If tex compilation doesn't work:
#       use commands at the end of this document manually. 
#
# Last tested with 2018-11-02-tentaikeisei.html
#

# _________________________________________________________________________
# Main bit

import re
import subprocess as sp
import sys
import os.path as osp
import yaml


# File basename
if len(sys.argv) > 0:
    fbase = re.sub('\.html', '', sys.argv[1])
else:
    fbase = '2018-11-02-tentaikeisei'

# IO files
fh = open(fbase + '.html', 'r')
fo = open(fbase + '.yml', 'w')

# Write out yml data in to separate file
yaml_data = False
yaml_once = False
for fl in fh.readlines():
    if re.search('^---', fl) and yaml_once:
        yaml_data = False
    if yaml_data:
        fo.write(fl)
    if re.search('^---', fl) and not yaml_once:
        yaml_data = yaml_once = True

# Close files
fh.close()
fo.close()

# Reopen yml file, and open latex file
fh = open(fbase + '.yml', 'r')
fl = open(fbase + '.tex', 'w')
yd = yaml.load(fh)

# Write LaTeX header
fl.writelines([
    '\\documentclass[10pt, a4paper]{article}\n',
    '\\addtolength{\\oddsidemargin}{-1.2cm}\n',
    '\\addtolength{\\topmargin}{-1.4cm}\n',
    '\\addtolength{\\textwidth}{4.4cm}\n',
    '\\addtolength{\\textheight}{2.4cm}\n'
    '\\usepackage[usenames,dvipsnames]{color}\n'
])

# Write LaTeX title
fl.write('\\title{' + yd['title'] + '\\\\[-0.5cm]}\n')
fl.write('\\date{' + yd['from'].strftime('%a, %b %d') + ' -- ' + yd['to'].strftime('%a, %b %d') + '}\n')
fl.write('\\begin{document}\n')
fl.write('\\maketitle\n')

# Write contribution types in LaTeX
#if yd.has_key('contribution_types'):
if 'contribution_types' in yd:
    fl.write('\\begin{itemize}\n')
    for ct in yd['contribution_types']:
        fl.writelines([
            '\\item[\\bf ' + ct['type'] + ':] ',
            str(ct['talk']) + ' min talk + ',
            str(ct['disc']) + ' min discussion\n'
        ])
    fl.write('\\end{itemize}\n')

# Write Program in LaTeX
for day in yd['program']:
    fl.write('\\section*{Day ' + str(day['day']) + ' -- ' + day['date'].strftime('%A, %b %d') + '}\n')
    for session in day['sessions']:
        fl.writelines([
            '\\subsection*{',  
            session['title'],
            ' $\quad$ ' + session['from'], ' -- ' + session['to'] if 'to' in session else '',
            ' \\hspace{\\stretch{1}}\\small Chair: ' + session['chair'] if 'chair' in session else '',
            ' \\hspace{\\stretch{1}}\\small MC: ' + session['emcee'] if 'emcee' in session else '',
            '}\n'
        ])
        fl.write('\\begin{itemize}\n')
        for contrib in session['contributions']:
            fl.writelines([
                '\\item[',
                contrib['from'], ' -- ' + contrib['to'] if 'to' in contrib else '',
                ']',
                ' {\\color{MidnightBlue}' + contrib['speaker'] + '}' if 'speaker' in contrib else '',
                ' {\\color{MidnightBlue}({\\footnotesize ' + contrib['affil'] + '})}' if 'affil' in contrib else '',
                ' ' + contrib['title'] + '',
                '\n'
            ])
        fl.write('\\end{itemize}\n')

# End of LaTeX document
fl.write('\\end{document}\n')


# Close files
fh.close()
fl.close()

# Tex Generation (use platex rather than pdflatex to always include Japanese text)
# Output needs to be in an output directory below working directory due to tex permissions
output_dir = 'yml2tex_output'
print('platex' + '--output-directory=' + output_dir + ' ' + fbase + '.tex')
sp.call(['platex', '--output-directory=' + output_dir, fbase + '.tex'])
print('dvipdfmx -o ' + osp.join(output_dir, fbase + '.pdf') + osp.join(output_dir, fbase + '.dvi'))
sp.call(['dvipdfmx', '-o', osp.join(output_dir, fbase + '.pdf'), osp.join(output_dir, fbase + '.dvi')])

# Cleanup
sp.call(['rm', '-f', fbase + '.yml'])
sp.call(['rm', '-f', fbase + '.tex'])
sp.call(['rm', '-f', osp.join(output_dir, fbase + '.log')])
sp.call(['rm', '-f', osp.join(output_dir, fbase + '.dvi')])
sp.call(['rm', '-f', osp.join(output_dir, fbase + '.aux')])

