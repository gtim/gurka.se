#!/bin/bash

declare -A domains=(
	["gurka"]="gurka.se gurak.se guka.se"
	["persimon"]="persimon.se"
	["champinjon"]="champinjon.se"
	["potatis"]="potat.is"
)

for id in "${!domains[@]}"; do
	for domain in ${domains[$id]}; do
		rsync --archive --verbose --recursive \
			build/${id}/ /srv/http/${domain}/public_html \
			--delete
	done
done
