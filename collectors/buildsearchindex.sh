#!/bin/bash

set -euo pipefail

mp3path=$(tools/setting Mp3Path)
file=index.new
find "$mp3path/Artists" -iname '*.mp3' > $file
mv $file "$mp3path/index"
