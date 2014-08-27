# This script first extracts all images and text from a pdf
# then finds the images that is most cited.
# It also creates a thumbnail.

import re
from bibtexparser.bparser import BibTexParser
import bibtexparser.customization as cm
import bibtexparser.bwriter as bw
import yaml
import configparser
import subprocess as sp
import glob
from matplotlib.pyplot import imread
import numpy as np

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

# bibfile
bibfile = 'bib/all_papers.bib'

# log file 
logfile = 'bib2yaml.log'

# Start entry
ientry = 0

# Exclude (these have image extraction issues and have not been amended yet)
exclude = ['kino2013a','tanikawa2013a','ishiyama2014a']

# Exclude (these have image extraction issues and have been amended )
exclude.extend([])

# Read and parse bibtex file
with open(bibfile, 'r') as bf:
    bp = BibTexParser(bf.read(), customization=customizations)
    entries = bp.get_entry_dict().items()[ientry:]


# Months dictionary to convert dates
mon_dict = { 
    'jan': '01', 'feb': '02', 'mar': '03',
    'apr': '04', 'may': '05', 'jun': '06',
    'jul': '07', 'aug': '08', 'sep': '09',
    'oct': '10', 'nov': '11', 'dec': '12',
    'january': '01', 'february': '02', 'march': '03',
    'april': '04', 'may': '05', 'june': '06',
    'july': '07', 'august': '08', 'september': '09',
    'october': '10', 'november': '11', 'december': '12',
    'January': '01', 'February': '02', 'March': '03',
    'April': '04', 'May': '05', 'June': '06',
    'July': '07', 'August': '08', 'September': '09',
    'October': '10', 'November': '11', 'December': '12',
    '1': '01', '2': '02', '3': '03',
    '4': '04', '5': '05', '6': '06',
    '7': '07', '8': '08', '9': '09',
    '10': '10', '11': '11', '12': '12'
}

# Journal abbreviations
ja = configparser.ConfigParser()
ja.read('journal_abbreviations.ini')


# open logfile and write header
fl = open(logfile, 'w')
fl.write(format('#','>7s')+format('key','>25s')+
         format('fnum','>7s')+format('nfig','>7s')+
         format('ngood','>7s')+format('main','>30s')+'\n')

# Loop over each bibliography entry
for key, entry in entries:

    if key in exclude:
        continue

    print('Processing Entry '+str(ientry+1)+' of '+str(len(entries))+': '+key)

    fl.write(format(ientry,'>7d')+format(key,'>25s'))


    # Modify some bibtex entries

    # Construct date and id for filename
    mon = mon_dict[entry['month']] 
    date = entry['year'] + '-' + mon + '-01'
    id = entry['id']
    fname = date+'-'+id

    # Get rid of newlines in abstract field
    entry['abstract'] = re.sub('\n',' ',entry['abstract'])

    # Substitute Journal abbreviations
    if entry.has_key('journal'):
        name = entry['journal']['name']
        if re.search('\\\\', name):
            name = re.sub('\\\\', '', name)
            entry['journal']['name'] = entry['journal']['id'] = ja['DEFAULT'][name]




    # Extract images and add to bibtex entries

    # Check if pdf exists
    if glob.glob('pdf/'+key+'.pdf') == []:
        print('No pdf. Moving on')

    else:
        # Convert pdf to text
        cmd_txt = ['pdftotext', 'pdf/'+key+'.pdf', 'txt/'+key+'.txt']
        sp.call(cmd_txt)

        # Find the occurence of a Figure number
        with open('txt/'+key+'.txt', 'r') as tf:
            stream = tf.readlines()
            stream = re.sub('\n','',' '.join(stream))
            s1 = re.findall('[fF]ig[\.]?[ ]?[ ]?[0-9][0-9]?', stream)
            s2 = re.findall('[fF]igure[ ]?[ ]?[0-9][0-9]?', stream)
            s1 = [int(re.sub('[fF]ig[\.]?[ ]?[ ]?', '', s)) for s in s1]
            s2 = [int(re.sub('[fF]igure[ ]?[ ]?', '', s)) for s in s2]
            s1.extend(s2)

            # Hopefully no paper will have more than 100 figures
            count_old = 0
            fnum = 0
            for ic in range(100):
                count = s1.count(ic)
                if count >= count_old:
                    count_old = count
                    fnum = ic

        # Extract all images from the pdf
        cmd_img = ['pdfimages', 'pdf/'+key+'.pdf', key]
        sp.call(cmd_img)

        # List of images
        imgs = []

        # Pre-loop initialization
        iim_ok = 0
        images = glob.glob(key+'-[0-9][0-9][0-9].p[gbp]m')

        # Some logging
        fl.write(format(len(images),'>7d'))
        fl.write(format(fnum,'>7d'))

        # Convert images to png and remove pbms
        for img in images:

            # Commands (must be in loop to be reinitiated every time)

            # Convert pbm to png
            img_png = re.sub('p[gbp]m', 'png', img)
            cmd_png = ['convert', img, img_png]
            sp.call(cmd_png)

            # Delete p[gbp]m
            cmd_rm = 'rm -f'.split()
            cmd_rm.extend([img])
            sp.call(cmd_rm)

            # Check image quality
            im = imread(img_png)
            ok = True

            # Check image size
            if im.shape[0] < 200 or im.shape[1] < 200:
                ok = False

            # Check image Fourier spectrum
            else:
                if np.size(im.shape) > 2:
                    fr = np.fft.rfft2(im[:,:,0])
                    fg = np.fft.rfft2(im[:,:,1])
                    fb = np.fft.rfft2(im[:,:,2])

                    fftimr = np.real(fr*np.conjugate(fr))
                    fftimg = np.real(fg*np.conjugate(fg))
                    fftimb = np.real(fb*np.conjugate(fb))

                    fr = np.sum(fftimr, 0)
                    fg = np.sum(fftimg, 0)
                    fb = np.sum(fftimb, 0)
                   
                    f = np.sqrt(fr*fr + fg*fg + fb*fb)

                else:
                    f = np.fft.rfft2(im)
                    fftim = np.real(f*np.conjugate(f))
                    f = np.sum(fftim, 0)

                # Fit a power-law line to the Fourier spectrum 
                x = np.linspace(1, f.size, f.size)
                fit = np.polyfit(np.log10(x), np.log10(f), 1)

                # If spectrum contains non-finite amplitudes, 
                # fit parameters will contain nans
                if np.isnan(fit[0]) or np.isnan(fit[1]):
                    ok = False

                # If the variation in amplitudes is really strong (especially
                # the difference between that of k0 and other values)
                # then we probably have systematic noise in the extraction.
                elif np.ma.average(np.ma.masked_invalid(np.log10(f))/np.log10(f[0])) < -1:
                    ok = False

            # More checks coud be included here


            # Count as good if png is good and rename, or delete it if it is bad
            if ok:
                img_png_ok = re.sub('[0-9][0-9][0-9]\.png', format(iim_ok,'0>2d')+'.png', img_png)

                cmd_rename  = ['mv', img_png, img_png_ok]
                sp.call(cmd_rename)
                iim_ok += 1

                # Add to list of images
                if iim_ok == fnum:
                    imgs.insert(0, img_png_ok)

                else:
                    imgs.append(img_png_ok)


            else:
                cmd_rm = 'rm -f'.split()
                cmd_rm.extend([img_png])
                sp.call(cmd_rm)


        # Some logging
        fl.write(format(iim_ok,'>7d'))


        # If no good images were created, create png of pdf
        if iim_ok == 0:
            cmd_ppm = 'pdftoppm -png -singlefile -x 0 -y 0 -W 1280 -H 960'.split()
            cmd_ppm.extend(['pdf/'+key+'.pdf', key+'-00'])
            sp.call(cmd_ppm)
            imgs.insert(0, key+'-00.png')


        # Create thumbnails of main image and other formats all images
        for iimg, img in enumerate(imgs):

            # Thumbnails for main image
            if iimg == 0:
                img_thumb = re.sub('\.png','-thumb.png', img)
                cmd_resize = 'convert -thumbnail 150x150^ -gravity center -extent 150x150'.split()
                cmd_resize.extend([img, img_thumb])
                sp.call(cmd_resize)

                # Move it into img/ folder
                cmd_mv = ['mv', img_thumb, 'img/']
                sp.call(cmd_mv)

                # Some logging
                fl.write(format(img,'>30s'))

            # Create other sizes
            img_360 = re.sub('\.png','-360x240.png', img)
            cmd_resize = 'convert -thumbnail 360x240 -gravity center -background white -extent 360x240'.split()
            cmd_resize.extend([img, img_360])
            sp.call(cmd_resize)

            img_800 = re.sub('\.png','-800x533.png', img)
            cmd_resize = 'convert -thumbnail 800x533 -gravity center -background white -extent 800x533'.split()
            cmd_resize.extend([img, img_800])
            sp.call(cmd_resize)

            # Move images into img folder
            cmd_mv = ['mv', img, img_360, img_800, 'img/']
            sp.call(cmd_mv)


        # Add images to entry (only full size images and thumb of the main image)
        entry['img'] = imgs
        entry['img_thumb'] = img_thumb


    # 'if pdf exists' clause ends here


    # Remove bibdesk file entries, if they exist
    for i in range(1, 5):
        if entry.has_key('bdsk-file-'+str(i)):
            entry.pop('bdsk-file-'+str(i))


    # Write yaml file

    # Use existing bp object with entry
    bp.records = [entry,]
    bp.entries_hash = {key: entry}

    # Convert to json, then to yaml
    entry_json = bw.to_json(bp)
    entry_yaml = yaml.load(entry_json)[key]

    # Dump the yaml file. (html file for jekyll)
    with open('yml/en/'+date + '-' + id + '.html', 'w') as ef:
        ef.write('---\n')
        ef.write('lang: en\n')
        yaml.dump(entry_yaml, ef, allow_unicode=True) 
        ef.write('categories:\n')
        ef.write('  - achievements\n')
        ef.write('  - en\n')
        ef.write('---\n')

    with open('yml/ja/'+date + '-' + id + '.html', 'w') as ef:
        ef.write('---\n')
        ef.write('lang: ja\n')
        yaml.dump(entry_yaml, ef, allow_unicode=True) 
        ef.write('categories:\n')
        ef.write('  - achievements\n')
        ef.write('  - ja\n')
        ef.write('---\n')

    # Increment entry number
    ientry +=1

    # New line in logfile
    fl.write('\n')

# Close logfile
fl.close()

