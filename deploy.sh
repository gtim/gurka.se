#!/bin/bash

declare -A domains=(
	["gurka"]="beta.gurka.se"
	["persimon"]="persimon.se"
)

for id in "${!domains[@]}"; do
	rsync --archive --verbose --recursive \
		build/${id}/ /srv/http/${domains[$id]}/public_html \
		--delete
done
