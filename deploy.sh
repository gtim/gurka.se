rsync --archive --verbose --recursive \
	build/gurka/ /srv/http/gurka.se/public_html \
	--delete
