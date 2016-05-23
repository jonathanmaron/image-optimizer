
# image-optimizer.sh

Image optimization / compression CLI tool. This tool optimizes PNG and JPEG files from the CLI, using `pngout`, `pngcrush` and `jpegtran` and can usually reduce the filesize by 10% to 70%.

As optimzing images is CPU intensive and it may take several minutes for a larger PNG file, `image-optimizer.sh` keeps track of which image files it has optimized. Each image file will only be optimize once.

`image-optimizer.sh` is intended to be run unattended in a cronjob.

## Example usage

### Optimize images in ~/web-project/public

    image-optimizer.sh ~/web-project/public
    
### Set all images in ~/web-project/public as optimized

    image-optimizer.sh ~/web-project/public --index-only


## Installation

Installation is via composer:
    
    cd ~/install-path
    
    composer create-project jonathanmaron/image-optimizer
    
It is recommended to include `~/bin` in your `PATH` variable:

    PATH=$PATH:~/install-path/image-optimizer/bin
    
so that `image-optimizer.sh` is available to the logged in user globally. 


### Dependencies

`image-optimizer.sh` depends upon `pngout`, `pngcrush` and `jpegtran` to perform its work. These tools must be installed, otherwise `image-optimizer.sh` will return an error.

Download and install `pngout` at:

    http://static.jonof.id.au/dl/kenutils/pngout-20150319-linux-static.tar.gz (or newer)

then unpack, then copy to:

    /usr/bin/pngout
    
Download and install other dependencies:

    apt-get install pngcrush libjpeg-progs jpegoptim
    
    
