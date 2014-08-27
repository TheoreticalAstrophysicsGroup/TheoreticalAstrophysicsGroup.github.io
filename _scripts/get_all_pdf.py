from bibtexparser.bparser import BibTexParser
import bibtexparser.customization as cm
import subprocess as sp

# Customization for bibtexparser
def customizations(record):

    record = cm.journal(record)
    record = cm.author(record)
    record = cm.editor(record)
    record = cm.keyword(record)
    record = cm.link(record)
    record = cm.doi(record)
    record = cm.convert_to_unicode(record)
    return record


ientry = 169

bibfile = 'bib/all.bib'

# Read and parse bibtex file
with open(bibfile, 'r') as bf:
    bp = BibTexParser(bf.read(), customization=customizations)
    entry_keys = bp.get_entry_dict().keys()[ientry:]

# Problems with no: 52, 224

# adsbibdesk sometimes hangs with timeouts - just need to relaunch 
# this script with updated initial entry_key index

cmd = ['adsbibdesk']
cmd.extend(entry_keys)
sp.call(cmd)

