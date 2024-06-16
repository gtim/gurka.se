use 5.30.0;
use warnings;
use File::Path qw/make_path remove_tree/;
use File::Copy::Recursive qw/dircopy/;

my @sites = (
	{ id => 'gurka', domain => 'gurka.se' },
	#{ id => 'persimon', domain => 'persimon.se' },
);

# clean build dir
if ( -d 'build' ) {
	remove_tree( 'build' ) or die "could not remove build dir: $!";
}
make_path('build') or die "could not create build dir: $!";

# build each site
for my $site ( @sites ) {
	my $out_dir = 'build/' . $site->{id} . '/';
	make_path($out_dir) or die "could not create site build dir: $!";
	dircopy( 'static/common', $out_dir ) or die "copy failed: $!";
	if ( -d 'static/'.$site->{id} ) {
		dircopy( 'static/'.$site->{id}, $out_dir ) or die "copy failed: $!";
	}
}
