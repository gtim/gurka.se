rsync --archive --verbose --recursive \
	--exclude='*.sh' \
	--cvs-exclude \
	./ /srv/http/gurka.se/public_html \
	--delete
