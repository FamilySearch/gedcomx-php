<?php

// Create the build directory if it doesn't already exist
if(!is_dir(__DIR__ . '/build')){
    mkdir(__DIR__ . '/build');
}

// Delete the phar and start fresh
if(file_exists(__DIR__ . '/build/Gedcomx.phar')){
    unlink(__DIR__ . '/build/Gedcomx.phar');
}

$phar = new Phar(__DIR__ . '/build/Gedcomx.phar');

// Add all Gedcomx source files to the phar
$srcDir = __DIR__ . '/src';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($srcDir, FilesystemIterator::SKIP_DOTS));
$phar->buildFromIterator($iterator, $srcDir);

// Require all Gedcomx source files to bootstrap
$iterator->rewind();
$stub = '<?php
Phar::mapPhar("Gedcomx.phar");
spl_autoload_register(function($class_name) {
    $class_name = str_replace("\\\\", "/", $class_name);
    $class_name = str_replace("Gedcomx/", "", $class_name);
    include_once("phar://Gedcomx.phar/$class_name.php");
});
';
foreach($iterator as $file){
    $file = str_replace($srcDir . '/', '', $file);
    $stub .= "include_once('phar://Gedcomx.phar/$file');\n";
}
$stub .= '__HALT_COMPILER();';
$phar->setStub($stub);

// Add dependencies
$depDirs = array(
    __DIR__ . '/vendor/guzzle/guzzle/src',
    __DIR__ . '/vendor/ml/iri/',
    __DIR__ . '/vendor/ml/json-ld'
);
foreach($depDirs as $dir){
    $phar->buildFromIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)), $dir);
}

// Compress the phar, otherwise it will be enourmous
$phar->compressFiles(Phar::GZ);
