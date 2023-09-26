#!/bin/bash
shopt -s extglob

FILE=mnca-wp.zip

if [ -f "./$FILE" ]; then
    rm $FILE
fi

zip -r $FILE . -x@.distignore -q

if [ -d "./vendor/composer" ]; then
	zip -r $FILE vendor/composer vendor/autoload.php -q
fi

echo "Plugin zip $FILE created !"

shopt -u extglob
