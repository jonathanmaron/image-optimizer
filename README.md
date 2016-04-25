
# image-optimizer.sh

Image optimization / compression CLI tool. This tool optimizes PNG and JPEG files from the CLI, using `pngout`, `pngcrush` and `jpegtran`.

Using `image-optimizer.sh`, you can reduce the filesize of the images in your project by 10% to 70%, without loosing any quality.

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
    
    export PATH
    
so that `image-optimizer.sh` is available to the logged in user globally. 


### Dependencies

`image-optimizer.sh` depends upon `pngout`, `pngcrush` and `jpegtran` to perform its work. These tools must be installed, otherwise `image-optimizer.sh` will return an error.

Download and install `pngout` at:

    http://static.jonof.id.au/dl/kenutils/pngout-20150319-linux-static.tar.gz (or newer)

then unpack, then copy to:

    /usr/bin/pngout
    
Download and install other dependencies:

    apt-get install pngcrush libjpeg-progs
    
    
