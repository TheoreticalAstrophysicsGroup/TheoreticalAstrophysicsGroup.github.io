# This script adds all the missing yaml entries
# for members html files and dumps them in a specific order.

import subprocess as sp
from pathlib import Path
from ruamel.yaml import YAML

files = Path('.').glob('*.html')
# files = [Path('wagner_alexander.html')]
do_backups = True

# Use ruamel.yaml
yaml = YAML()
yaml.preserve_quotes = False
yaml.allow_duplicate_keys = False

# Entries and defaults
entries = {
    'lang': 'en',
    'name': None,
    'position': None,
    'order': None,
    'email': None,
    'tel': 3370,
    'homepage': None,
    'research': None,
    'profile_pic': None,
    'footnote': None
}

# Assume all arguments are filenames
for file in files:

    # loader expects more after the ending ---
    # so use load_all and only take first item
    data = list(yaml.load_all(file))[0]

    # Dictionary remains ordered, officially since Python 3.7
    out_dict = {}

    for entry in entries:

        # Use entries in existing data if present
        # Else set the default
        if entry in data and data[entry]:
            out_dict[entry] = data[entry]
        else:
            out_dict[entry] = entries[entry]

    # Backup file
    if do_backups:
        file.rename(f'{file.name}.bak')

    # Dump yaml data and add --- before and after
    yaml.dump(out_dict, file)
    with file.open('r+') as fh:
        yaml_bit = fh.read()
        fh.seek(0)
        fh.write('---\n' + yaml_bit + '---\n')
