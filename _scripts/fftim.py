import matplotlib.pyplot as pl
import numpy as np
import seaborn


# This is a script to look at the 1D Fourier spectra of images
# and see if one can differentiate between failed pdf image
# extractions and successful ones.

im = pl.imread('img/kawakatu2003a-00.png')
#im = pl.imread('example.png')
#im = pl.imread('figure_1.png')

pl.ion()

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

x = np.linspace(1, f.size, f.size)

#pl.plot(np.log10(x), np.log10(f), 'k')

fit = np.polyfit(np.log10(x), np.log10(f), 1)

pl.plot(np.log10(x), np.log10(f)/np.log10(f[0]))

average = np.ma.average(np.ma.masked_invalid(np.log10(f))/np.log10(f[0]))
