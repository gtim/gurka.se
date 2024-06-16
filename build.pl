use 5.30.0;
use warnings;
use File::Path qw/make_path remove_tree/;
use File::Copy::Recursive qw/dircopy/;
use File::Slurper qw/write_text/;

my @sites = (
	{ id => 'gurka', title => '&#x1F952; gurka.se' },
	{ id => 'persimon', title => 'persimon.se' },
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
	# plant site config
	write_site_config( $site, $out_dir . 'config.php' );
}

sub write_site_config {
	my ( $site, $config_path ) = @_;
	my $config_contents = <<CONFIG;
<?
	\$config = array(
		"id" => "$site->{id}",
		"title" => "$site->{title}"
	);
	return \$config;
?>
CONFIG
	write_text( $config_path, $config_contents );
}
