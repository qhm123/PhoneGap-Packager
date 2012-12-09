<?php
// php create.php project_path

$path = $argv[1];

if (!$path)
{
  echo "usage: php create.php project_path\n";
  return;
}

system( 'cp -r ./libs/project ' . $path );

echo 'created project in ' . $path . "\n";
?>
