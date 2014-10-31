import yaml
import re
import subprocess as sp
import sys
import os.path as osp

# This script extracts yaml data from a conference file and 
# Creates a pdf program. It also includes title, dates, and 
# the types of contributions.
# Tested with 2014-11-03-smbh-liason-workshop.html

# Things to edit in this file are
# - fbase, just below
# - LaTeX header addtolengths 
# - The \\\\[<>cm] in the title

# File basename
if len(sys.argv) > 0:
    fbase = re.sub('\.html', '', sys.argv[1])
else:
    fbase = "confereces_file"

# IO files
fh = open(fbase+'.html', 'r')
fo = open(fbase+'.yml', 'w')

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
fh = open(fbase+'.yml', 'r')
fl = open(fbase+'.tex', 'w')
yd = yaml.load(fh)

# Write LaTeX header
fl.writelines([
    '\\documentclass[10pt, a4paper]{article}\n',
    '\\addtolength{\\oddsidemargin}{-1.2cm}\n',
    '\\addtolength{\\topmargin}{-1.4cm}\n',
    '\\addtolength{\\textwidth}{4.4cm}\n',
    '\\addtolength{\\textheight}{2.4cm}\n'
    '\usepackage[usenames,dvipsnames]{color}\n'
])

# Write LaTeX title
fl.write('\\title{'+yd["title"].encode('utf8')+'\\\\[-0.5cm]}\n')
fl.write('\\date{'+yd["from"].strftime('%a, %b %d')+' -- '+yd["to"].strftime('%a, %b %d')+'}\n')
fl.write('\\begin{document}\n')
fl.write('\\maketitle\n')

# Write contribution types in LaTeX
if yd.has_key("contribution_types"):
    fl.write('\\begin{itemize}\n')
    for ct in yd["contribution_types"]:
        fl.writelines([
            '\\item[\\bf '+ct["type"].encode('utf8')+':] ',
            str(ct["talk"])+' min talk + ',
            str(ct["disc"])+' min discussion\n'
        ])
    fl.write('\\end{itemize}\n')

# Write Program in LaTeX
for day in yd["program"]:
    fl.write('\\section*{Day '+str(day["day"])+' -- '+day["date"].strftime('%A, %b %d')+'}\n')
    for session in day["sessions"]:
        fl.writelines([
            '\\subsection*{',  
            session["title"].encode('utf8'),
            ' ' + session["from"], ' -- ' + session["to"] if session.has_key("to") else "",
            ' \\hspace{\\stretch{1}}\\small Chair: ' + session["chair"].encode('utf8') if session.has_key("chair") else "",
            ' \\hspace{\\stretch{1}}\\small MC: ' + session["emcee"].encode('utf8') if session.has_key("emcee") else "",
            '}\n'
        ])
        fl.write('\\begin{itemize}\n')
        for contrib in session["contributions"]:
            fl.writelines([
                '\\item[',
                contrib["from"], ' -- ' + contrib["to"] if contrib.has_key("to") else "",
                ']',
                ' {\\color{MidnightBlue}' + contrib["speaker"].encode('utf8') + '}' if contrib.has_key("speaker") else "",
                ' {\\color{MidnightBlue}({\\footnotesize ' + contrib["affil"].encode('utf8') + '})}' if contrib.has_key("affil") else "",
                ' ' + contrib["title"].encode('utf8') + '',
                '\n'
            ])
        fl.write('\\end{itemize}\n')

# End of LaTeX document
fl.write('\\end{document}\n')


# Close files
fh.close()
fl.close()

# Tex Generation (use platex rather than pdflatex 
# to always include Japanese text)
sp.call(['platex','--output-directory='+osp.dirname(fbase),fbase+'.tex'])
sp.call(['dvipdfmx','-o',fbase+'.pdf', fbase+'.dvi'])

# Cleanup
sp.call(['rm','-f',fbase+'.yml'])
sp.call(['rm','-f',fbase+'.log'])
sp.call(['rm','-f',fbase+'.dvi'])
sp.call(['rm','-f',fbase+'.aux'])
sp.call(['rm','-f',fbase+'.tex'])


