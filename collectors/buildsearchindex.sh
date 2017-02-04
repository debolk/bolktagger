#!/bin/bash

set -euo pipefail

file=index.new
find Artists -iname '*.mp3' > $file
mv $file index
