# Image Optimizer

Image optimization and compression tool for the console. This tool optimizes PNG, JPEG and GIF files, using `gifsicle`, `jpegoptim`, `jpegtran`, `pngcrush` and `pngout` and can usually reduce the filesize by 10% to 70%.

As optimzing images is CPU intensive and it may take several minutes for a larger PNG file, `image-optimizer` keeps track of which image files it has optimized. Each image file will only be optimize once, unless the `--force` flag is set.

`image-optimizer` is intended to be run unattended in a cronjob.

## Example Usage

### Optimize Images In `/path/to/images`

    image-optimizer --path="/path/to/images"

### Optimize Images In `/path/to/images`, Ignoring History

    image-optimizer --path="/path/to/images" --force

### Set All Images In `/path/to/images` As Optimized

    image-optimizer --path="/path/to/images" --index-only


## Installation

Installation is via composer:

    cd ~/install-path

    composer create-project jonathanmaron/image-optimizer

It is recommended to include `~/bin` in your `PATH` variable:

    PATH=$PATH:~/install-path/image-optimizer/bin

so that `image-optimizer` is available to the logged in user globally.


## Upgrading From Version 1

Between v1 and v2, the structure of the history directory was updated. Therefore, please remove your current history directory. A new one, with the new structure, will be created when `image-optimizer` is executed for the first time:

    rm -fr ~/.image_optimizer


## Dependencies

`image-optimizer` depends upon `gifsicle`, `jpegoptim`, `jpegtran`, `pngcrush` and `pngout` to perform its work. These tools must be installed, otherwise `image-optimizer` will return an error.

Download and install `pngout` at:

    http://static.jonof.id.au/dl/kenutils/pngout-20150319-linux-static.tar.gz (or newer)

then unpack, then copy to:

    /usr/bin/pngout

Download and install other dependencies:

    apt install pngcrush libjpeg-progs jpegoptim gifsicle

