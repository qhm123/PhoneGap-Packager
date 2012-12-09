<?php
// php build.io.php project_path

define( 'DEFAULT_PACKAGE_NAME', 'com.example.phonegap' );
define( 'DEFAULT_APP_NAME', 'phonegap app' );
define( 'DEFAULT_APP_VERSION', '1.0.0' );

$path = $argv[1];

if (!$path)
{
  echo "usage: php build.ios.php project_path package_name app_name app_version\n";
  return;
}

if (!file_exists($path))
{
  echo "error: project path not exist\n";
  return;
}

$package_name = $argv[2];
$app_name = $argv[3];
$app_version = $argv[4];

require_once( 'libs/phonegap_tag_replace.php' );

$path = $path . '/';
echo "prepairing...\n";
prepair_package( $path );
echo "changing app infos...\n";
echo " - package name: " . $package_name . "\n";
echo " - app name: " . $app_name . "\n";
echo " - app version: " . $app_version . "\n";
change_appinfo( $path, $package_name, $app_name, $app_version, '1' );
echo "replace phoengap tags and add phonegap js...\n";
add_phonegap( $path );
echo "building...\n";
build( $path );
echo "done\n";

function add_phonegap( $path )
{
  $html_dir = $path;
  $ios_dir = $path . 'ios/';

  system( 'cp -Rf ' . $html_dir . 'www  ' .  $ios_dir .  'Payload/basePackage.app/' );
  system( 'cp -Rf libs/phonegap.ios.js  ' .  $ios_dir .  'Payload/basePackage.app/www/phonegap.js' );
  replace_phonegap_tag($ios_dir . 'Payload/basePackage.app/www');
}

function change_appinfo( $path, $package_name, $app_name, $app_version, $orientation )
{
  if( strlen($package_name) <= 0 )
  {
    $package_name = DEFAULT_PACKAGE_NAME;
  }
  if ( strlen($app_name) <= 0 )
  {
    $app_name = DEFAULT_APP_NAME;
  }
  if ( strlen($app_version) <= 0 )
  {
    $app_version = DEFAULT_APP_VERSION;
  }

  replace_plist_value( $path . 'ios/Payload/basePackage.app/Info.plist', $package_name, $app_version, $app_name, $orientation );
}

function replace_plist_value($path, $client_name, $client_version, $app_name, $orientation)
{
  $content = file_get_contents($path);
  $new_content = str_replace( '{{DisplayName}}', $client_name, $content );
  $new_content = str_replace( '{{Name}}', $client_name, $new_content );
  $new_content = str_replace( '{{Identifier}}', $app_name, $new_content );
  $new_content = str_replace( '{{Version}}', $client_version, $new_content );

  // orientation
  $ori_port = '<string>UIInterfaceOrientationPortrait</string>';
  $ori_portDown = '<string>UIInterfaceOrientationPortraitUpsideDown</string>';
  $ori_landLeft = '<string>UIInterfaceOrientationLandscapeLeft</string>';
  $ori_landRight = '<string>UIInterfaceOrientationLandscapeRight</string>';
  $orientation_array = array(1 => $ori_port . $ori_portDown . $ori_landLeft . $ori_landRight,
    $ori_port,
    $ori_landLeft . $ori_landRight);
  $orientation_string = $orientation_array[$orientation];
  $new_content = str_replace( '{{orientation}}', $orientation_string, $new_content );

  file_put_contents($path, $new_content);
}

function build( $path )
{
  @mkdir( $path . 'dist/', 0777, true );
  $cwd = getcwd();
  chdir( $path . 'ios' );
  system( 'zip -r ' . $cwd . '/'. $path . 'dist/MyApp-release.ipa ' . 'Payload > /dev/null' );
}

function prepair_package( $path )
{
  system( 'cp -Rf ./libs/ios ' . $path );
  if( file_exists( $path . 'www' ) )
  {
    $check = array( 'icon.png' ,
      'icon@2x.png' ,
      'icon-72.png' ,
      'Default.png' ,
      'Default@2x.png' ,
    );
    foreach( $check as $item )
    {
      if( !file_exists( $path . $item ) )
      {
        return false;
      }
      else
      {
        copy( $path . $item , $path . 'ios/Payload/basePackage.app/' . $item );
      }
    }

    return true;
  }
}
