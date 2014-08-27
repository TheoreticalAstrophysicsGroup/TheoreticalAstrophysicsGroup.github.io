import requests
import urllib, urllib2
import subprocess as sp
from collections import OrderedDict
import re
import time

# List of authors
authors = [
    #'umemura, m.', 'umemura, masayuki',
    #'masao, m.', 'masao, mori',
    #'yoshikawa, k.', 'yoshikawa, kohji',
    #'okamoto, t.', 'okamoto, takashi',
    #'hasegawa, k.', 'hasegawa, kenji',
    #'ishiyama, t.', 'ishiyama, tomoaki',
    #'namekata, d.', 'namekata, daisuke',
    #'shibuya, t.', 'takatoshi, shibuya',
    #'kawakatsu, n.', 'kawakatsu, nozomu', 'kawakatu, n.', 'kawakatu, nozomu',
    #'kawaguchi, t.', 'kawaguchi, toshihiro', 'kawaguti, t.', 'kawaguti, toshihiro',
    #'tanikawa, a.', 'tanikawa, ataru',
    #'wagner, a. y.', 
    #'miki, y.', 'miki, yohei',
    #'ogiya, g.', 'ogiya, go',
    #'komatsu, y.', 'komatsu, yu',
    #'tanaka, s.', 'tanaka, satoshi',
    #'abe, m.', 'abe, makito',
    #'igarashi, a.', 'igarashi, asuka',
    #'kirihara, t.', 'kirihara, takanobu',
    #'suzuki, h.', 'suzuki, hiroyuki',
]

# Problems with a few entries by "kawakatu" - the abstract had undeciphrable characters.
# I saw a fix for this online.

# Use bibdesk to get rid of duplicates and discard false items, e.g., with searches of
# - "itokawa"
# - "laboratory"
# - "projectile"
# - names that produce lots of duplicates, like "abe, m", "okamoto, t", and "suzuki, h"

# Date ranges
start_year = '1999'
start_mon = '04'
end_year = ''
end_mon = ''

#Select journal. jou_pick=ALL returns all references (default)
#                jou_pick=NO return only refereed journals
#                jou_pick=EXCL return only non-refereed journals
#                jou_pick=YES return only journals specified in ref_stems
jou_picks = ['NO', 'EXCL']


# Loop over authors 
for author in authors:

    # Loop over journal picks
    for jou_pick in jou_picks:

        # The query parameteres
        url = 'http://adsabs.harvard.edu/cgi-bin/nph-abs_connect'
        query = OrderedDict({
            'db_key':['AST'],# 'PRE'],
            'qform':'AST',
            # 'cond-mat', 'cs', 'gr-qc', 'hep-ex', 'hep-lat', 'hep-ph', 'hep-th', 
            # 'math', 'math-ph', 'nlin', 'nucl-ex', 'nucl-th', 'physics', 'quant-ph', 'q-bio',
            'arxiv_sel':['astro-ph'],
            'sim_query':'YES', 'ned_query':'YES', 'adsobj_query':'YES',
            'author': author,
            'aut_xct':'YES', 'aut_req':'YES', 'aut_logic':'OR',
            'obj_logic':'OR',
            'object':'',
            'start_mon':start_mon, 'start_year':start_year,
            'end_mon':'', 'end_year':end_year,
            'ttl_logic':'OR', 'title':'',
            'txt_logic':'OR', 'text':'',
            'nr_to_return':'200', 'start_nr':'1',
            'article_sel':'YES', 'jou_pick':'NO',
            'ref_stems':'',
            'data_and':'ALL', 'group_and':'ALL',
            'start_entry_day':'', 'start_entry_mon':'', 'start_entry_year':'',
            'end_entry_day':'', 'end_entry_mon':'', 'end_entry_year':'',
            'min_score':'',
            'sort':'SCORE',
            'data_type':'BIBTEXPLUS',
            'aut_syn':'YES', 'ttl_syn':'YES', 'txt_syn':'YES',
            'aut_wt':'1.0', 'obj_wt':'1.0', 'ttl_wt':'0.3', 'txt_wt':'3.0',
            'aut_wgt':'YES', 'obj_wgt':'YES', 'ttl_wgt':'YES', 'txt_wgt':'YES',
            'ttl_sco':'YES', 'txt_sco':'YES',
            'version':'1',
        })


        # Send the request
        r = requests.get(url, params=query)

        # Pause so as not to overload the server
        time.sleep(3)

        # Write the obtained bibtex text to file
        author = re.sub(', ','_', author)
        author = re.sub(' ','_', author)
        author = re.sub('\.','', author)
        with open('bib/'+author+'-'+start_year+'-'+start_mon+'-'+jou_pick+'.bib', 'w') as fh:
            fh.write(r.text)


