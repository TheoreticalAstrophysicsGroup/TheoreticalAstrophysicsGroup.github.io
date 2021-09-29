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
# - Note sometimes, 'date' items are strings, sometimes they are datetitme objects.
#   If in the yaml file, there were quotes around the dates, then they are treated as strings.
#   This could cause some bugs.
#
# Last tested with 2020-11-06-tentaikeisei.[ja|en].html
#

# _________________________________________________________________________
# Main bit

import re
import subprocess as sp
import sys
import os.path as osp
from ruamel.yaml import YAML
from datetime import datetime, date, time
# TODO: Make it work with ruamel.yaml

# File basename
if len(sys.argv) > 0:
    fbase = re.sub('\.html', '', sys.argv[1])
else:
    fbase = '2020-10-06-tentaikeisei'

# Choose LaTeX engine ('platex', 'lualatex')
latex_engine = 'lualatex'
# latex_engine = 'platex'

# Language to process ('en', 'ja')
language = 'en'

# Show positions (like M1, or B4) in the program?
show_positions = True

# Get language from file, though if it contains that information
if '.en' in fbase:
    lang = 'en'
elif '.ja' in fbase:
    lang = 'ja'
else:
    lang = language
    fbase += f'.{lang}'


# Some helper functions for translations
def gwdj(d):
    wd = ['月', '火', '水', '木', '金', '土', '日']
    return wd[d.weekday()]


def date_format(d):
    if lang == 'ja':
        return f'%-m月 %-d日（{gwdj(d)}）'
    else:
        return '%A, %b %-d'


def day_str(d):
    if lang == 'ja':
        return f'{d}日目'
    else:
        return f'Day {d}'


def talk_disc_str(t, d):
    if lang == 'ja':
        return f'{t}分 + {d}分'
    else:
        return f'{t} min + {d} min'


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
        yaml_data = True
        yaml_once = True

# Close files
fh.close()
fo.close()

yaml = YAML()
yaml.preserve_quotes = False
yaml.explicit_start = False

# Reopen yml file, and open latex file
fh = open(fbase + '.yml', 'r')
fl = open(fbase + '.tex', 'w')
yd = yaml.load(fh)

# Write LaTeX header
fl.writelines([
    '\\documentclass[10pt, a4paper]{ltjsarticle}\n' if latex_engine == 'lualatex' else '\\documentclass[10pt, a4paper]{article}\n',
    '\\addtolength{\\oddsidemargin}{-0.2cm}\n',
    '\\addtolength{\\topmargin}{-1.2cm}\n',
    '\\addtolength{\\textwidth}{1.4cm}\n',
    '\\addtolength{\\textheight}{2.0cm}\n',
    '\\usepackage[usenames,dvipsnames]{color}\n',
    '\\usepackage{luatexja-preset}\n' if latex_engine == 'lualatex' else '',
    '\\usepackage{fontspec}\n',
    '\\usepackage{amsmath}\n',
    '\\usepackage{amssymb}\n',
    '\\usepackage[T1]{fontenc}\n',
    '\\usepackage{kpfonts}',
])

# Write LaTeX title
fl.write('\\title{' + yd['title'] + '\\\\[-0.5cm]}\n')
fl.write('\\date{' + yd['from'].strftime(date_format(yd['from'])) + ' -- ' + yd['to'].strftime(date_format(yd['to'])) + '}\n')
fl.write('\\begin{document}\n')
fl.write('\\maketitle\n')

# Write contribution types in LaTeX
if 'contribution_types' in yd:

    # Some translations
    if lang == 'ja':
        ct_h1 = '講演時間'
        ct_h2 = '講演'
        ct_h3 = '質疑応答'
    else:
        ct_h1 = 'Contributions'
        ct_h2 = 'talk'
        ct_h3 = 'Q\&{}A'


if 'contribution_types' in yd:
    fl.write('\\begin{itemize}\n')
        fl.writelines([
            '\\item[' + ('\\bf ' if lang == 'en' else ' ') + ct_h1 + ':] ',
            ct_h2 + ' + ' + ct_h3 + '\n'
        ])
    for ct in yd['contribution_types']:
        fl.writelines([
                '\\item[' + ('\\bf ' if lang == 'en' else ' ') + ct['type'] + ':] ',
                talk_disc_str(ct['talk'], ct['disc']) + '\n'
        ])
    fl.write('\\end{itemize}\n')

# Some translations
if lang == 'ja':
    chair = '座長'
else:
    chair = 'Chair'

# Special symbols (not used)
ch_sym = '$\\bowtie$'
tk_sym = '\\rotatebox[origin=c]{90}{$\\bowtie$}'

# Write Program in LaTeX
for day in yd['program']:

    # Day info
    thisday = date.fromisoformat(day['date'])
    fl.write('\\section*{' + day_str(day['day']) + ' -- ' + thisday.strftime(date_format(thisday)) + '}\n')

    for session in day['sessions']:

        # Concatenate chairs
        chairs = ', '.join(session['chair'])

        # Session info
        fl.writelines([
            '\\subsection*{',  
            session['title'],
            ' $\quad$ ' + session['from'][1:] if 'from' in session else '', 
            ' -- ' + session['to'][1:] if 'to' in session else '',
            ' \\hspace{\\stretch{1}}' + '\\small ' + chair + ': ' + chairs if 'chair' in session else '',
            ' \\hspace{\\stretch{1}}\\small MC: ' + session['emcee'] if 'emcee' in session else '',
            '}\n'
        ])
        fl.write('\\begin{itemize}\n')
        for contrib in session['contributions']:

            # Position string
            if show_positions:
                if 'position' in contrib and len(contrib['position']) > 0:
                    pos_str = f", {contrib['position']}" if contrib['position'][0] in ['B', 'M', 'D'] else ''
            else:
                pos_str = ''

            # Contributions info
            fl.writelines([
                '\\item[',
                contrib['from'][1:] if 'from' in contrib else '',
                ' -- ' + contrib['to'][1:] if 'to' in contrib else '',
                ']',
                ' {\\color{MidnightBlue}' + contrib['speaker'] + '}' if 'speaker' in contrib else '',
                ' {\\color{MidnightBlue}({\\footnotesize ' + contrib['affil'] + pos_str + '})}' if 'affil' in contrib else '',
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
# TODO: Make it work with LuaLaTeX
# Output needs to be in an output directory below working directory due to tex permissions
if latex_engine == 'platex':
output_dir = 'yml2tex_output'
print('platex' + '--output-directory=' + output_dir + ' ' + fbase + '.tex')
sp.call(['platex', '--output-directory=' + output_dir, fbase + '.tex'])
print('dvipdfmx -o ' + osp.join(output_dir, fbase + '.pdf') + osp.join(output_dir, fbase + '.dvi'))
sp.call(['dvipdfmx', '-o', osp.join(output_dir, fbase + '.pdf'), osp.join(output_dir, fbase + '.dvi')])

elif latex_engine == 'lualatex':
    output_dir = 'yml2tex_output'
    print('latexmk ' + '-lualatex ' + '--output-directory=' + output_dir + ' ' + fbase + '.tex')
    sp.call(['latexmk', '-lualatex', '--output-directory=' + output_dir, fbase + '.tex'])

else:
    print('Unknown LaTeX engine. Nothing outputted.')


# Cleanup
sp.call(['rm', '-f', fbase + '.yml'])
sp.call(['rm', '-f', fbase + '.tex'])
sp.call(['rm', '-f', osp.join(output_dir, fbase + '.log')])
sp.call(['rm', '-f', osp.join(output_dir, fbase + '.dvi')])
sp.call(['rm', '-f', osp.join(output_dir, fbase + '.aux')])

