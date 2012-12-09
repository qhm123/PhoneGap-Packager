<?php
// php build.php project_path

// ini_set( 'display_errors' , true );
// error_reporting(E_ALL);

define( 'KEYSTORE_FILE' , 'libs/android-release-key.keystore' );
define( 'KEY_STORE_ALIAS' , 'android-release-key.keystore' );
define( 'KEY_PASS' , '123456' );
define( 'STORE_PASS' , '123456' );

define( 'DEFAULT_PACKAGE_NAME', 'com.example.phonegap' );
define( 'DEFAULT_APP_NAME', 'phonegap app' );
define( 'DEFAULT_APP_VERSION', '1.0.0' );

$path = $argv[1];

if (!$path)
{
  echo "usage: php build.php project_path package_name app_name app_version\n";
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

require_once( 'libs/parse_android_permissions.php' );
require_once( 'libs/phonegap_tag_replace.php' );

$path = $path . '/';
echo "prepairing...\n";
prepair_package( $path );
echo "changing app infos...\n";
echo " - package name: " . $package_name . "\n";
echo " - app name: " . $app_name . "\n";
echo " - app version: " . $app_version . "\n";
change_appinfo( $path, $package_name, $app_name, $app_version, array(1,2,3,6) );
echo "replace phoengap tags and add phonegap js...\n";
add_phonegap( $path );
echo "building...\n";
build( $path );
echo "done\n";

function add_phonegap( $path )
{
  $html_dir = $path;
  $android_dir = $path . 'android/';

  system( 'cp -Rf ' . $html_dir . 'www  ' .  $android_dir .  'assets/' );
  system( 'cp -Rf libs/phonegap.android.js  ' .  $android_dir .  'assets/www/phonegap.js' );
  replace_phonegap_tag($android_dir . 'assets/www');
}

function get_version_code_by_version_name( $version_name )
{
  $version_code = 0;
  // versionCode
  $ret = preg_match_all("/^\d{1,2}\.\d{1,2}(\.\d{1,2})?$/", $version_name, $out);
  // 不匹配x.x.x的形式
  if( $ret != 1 ) 
  {
    $version_code = 10; 
    return $version_code;
  }
  $version_codes = preg_split("/\./", $version_name);
  $version_max_digit = 10000;
  for($i = 0; $i < count($version_codes); $i++)
  {
    $version_code += ($version_max_digit * $version_codes[$i]);
    $version_max_digit /= 100;
  }

  return $version_code;
}

function change_appinfo( $path, $package_name, $app_name, $app_version, $permissions )
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

  $android_dir = $path . 'android/';
  $old_content = file_get_contents( $android_dir . 'AndroidManifest.xml' );
  $new_content = str_replace( '{{packagename}}' , $package_name , $old_content );
  $new_content = str_replace( '{{versionname}}' , $app_version , $new_content );
  $version_code = get_version_code_by_version_name( $app_version );
  $new_content = str_replace( '{{versioncode}}' , $version_code , $new_content );

  $perm_string = '';
  $perm_dict = parse_android_permissions('libs/android.permissions');
  for($i = 0; $i < sizeof($permissions); $i++)
  {
    $perms = $perm_dict[strval($permissions[$i])];
    for($j = 0; $j < sizeof($perms); $j++)
    {
      $perm_string .= "<uses-permission android:name=\"" . $perms[$j] . "\" />\n";
    }
  }
  $new_content = str_replace( '{{permissions}}' , $perm_string , $new_content );

  file_put_contents(  $android_dir . 'AndroidManifest.xml' , $new_content );

  $old_content = file_get_contents( $android_dir . '/res/values/strings.xml' );
  $new_content = str_replace( '{{appname}}' , $app_name , $old_content );
  file_put_contents( $android_dir . 'res/values/strings.xml' , $new_content );
}

function build( $path )
{
  system( './libs/tools/apktool b ' . $path . 'android 2>/dev/null' );
  system( 'jarsigner -sigalg MD5withRSA -digestalg SHA1 -keystore ' . KEYSTORE_FILE . ' -storepass ' . STORE_PASS . ' -keypass ' . KEY_PASS . ' ' . $path . 'android/dist/MyApp-release.apk ' . KEY_STORE_ALIAS );
  @mkdir( $path . 'dist/', 0777, true );
  system( 'cp ' . $path . 'android/dist/MyApp-release.apk ' . $path . 'dist/MyApp-release.apk' );
}

function prepair_package( $path )
{
  $android_dir = $path . 'android/';
  system( 'cp -Rf ./libs/android ' . $path );

  if( file_exists( $path . './www' ) )
  {
    $check = array( 'icon.72x72.png' , 'icon.48x48.png' , 'icon.36x36.png' );
    foreach( $check as $item )
    {
      if( !file_exists( $path . $item ) )
      {
        return false;
      }
    }

    // copy
    if( file_exists( $path . 'icon.72x72.png' ) )
      copy( $path . 'icon.72x72.png' , $android_dir . 'res/drawable-hdpi/icon.png' );
    if( file_exists( $path . 'icon.48x48.png' ) )
      copy( $path . 'icon.48x48.png' , $android_dir . 'res/drawable-mdpi/icon.png' );
    if( file_exists( $path . 'icon.36x36.png' ) )
      copy( $path . 'icon.36x36.png' , $android_dir . 'res/drawable-ldpi/icon.png' );

    //'android_big.png' , 'android_middle.png' , 'android_small.png'  
    if( file_exists( $path . 'android_big.png' ) )
      copy( $path . 'android_big.png' , $android_dir . 'res/drawable-hdpi/splash.png' );
    else if( file_exists( $path . 'android_big.9.png' ) )
      copy( $path . 'android_big.9.png' , $android_dir . 'res/drawable-hdpi/splash.9.png' );
    else
      return false;
    if( file_exists( $path . 'android_middle.png' ) )
      copy( $path . 'android_middle.png' , $android_dir . 'res/drawable-mdpi/splash.png' );
    else if( file_exists( $path . 'android_middle.9.png' ) )
      copy( $path . 'android_middle.9.png' , $android_dir . 'res/drawable-mdpi/splash.9.png' );
    else
      return false;
    if( file_exists( $path . 'android_small.png' ) )
      copy( $path . 'android_small.png' , $android_dir . 'res/drawable-ldpi/splash.png' );
    else if( file_exists( $path . 'android_small.9.png' ) )
      copy( $path . 'android_small.9.png' , $android_dir . 'res/drawable-ldpi/splash.9.png' );
    else
      return false;

    return true;
  }
  else
  {
    return false;
  }
}
