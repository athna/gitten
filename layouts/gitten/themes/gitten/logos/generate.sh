#!/bin/sh
for SIZE in 48 64 128
do
    convert gitten.xcf \
        -background transparent \
        -layers merge \
        -trim \
        -resize 1024x$SIZE \
        gitten-$SIZE.png
done
