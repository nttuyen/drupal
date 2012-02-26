<?php

class JoomlaPackager {
    public function __construct() {
        ;
    }
    
    public static function genIndexFile($folder) {
        if(!file_exists($folder)) {
			return false;
		}
		
        if(!is_dir($folder)) {
            return true;
        }
		
		$result = true;
        
        $dirs = array($folder);
        while($dir = array_shift($dirs)) {
            //add index file
            if(strcmp($dir, $folder) != 0) {
                $indexFile = fopen($dir . DIRECTORY_SEPARATOR . 'index.html', 'w');
                fwrite($indexFile, '<html><head></head><body></body></html>');
                fclose($indexFile);
            }
            
            $dirObject = opendir($dir);
            while($file = readdir($dirObject)) {
                if($file == '.' || $file == '..') {
                    continue;
                }
                $subFile = $dir . DIRECTORY_SEPARATOR . $file;
                if(is_dir($subFile)) {
                    array_push($dirs, $subFile);
                }
            }
            closedir($dirObject);
        }
        
		return $result;
    }
    
    public static function zip($folder, $zipFileName = '') {
        if(!file_exists($folder)) {
			return false;
		}
		
		if(!is_dir($folder)) {
            //If not folder, process other
			return false;
		}
        
        if(empty($zipFileName)) {
            $zipFileName = $folder . '.zip';
        }
        
        if(file_exists($zipFileName) && is_file($zipFileName)) {
            @unlink($zipFileName);
        }
        
        $result = true;
        
		$zip = new ZipArchive();
		if($zip->open($zipFileName) !== true) {
			if($zip->open($zipFileName, ZipArchive::OVERWRITE) !== true) {
				return false;
			}
		}
		
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder));
        foreach ($iterator as $key => $value) {
            $length = strlen($key);
            if($key[$length - 1] == '.') {
                continue;
            }
            $zipPath = substr($key, strlen($folder) + 1);
            $zip->addFile(realpath($key), $zipPath);
        }
		$zip->close();
        
		return $result;
    }
}



echo "joomla packager";

$folder = '/home/nttuyen/tmp/test';
$genIndex = JoomlaPackager::genIndexFile($folder);
var_dump($genIndex);
$zip = JoomlaPackager::zip($folder);
var_dump($zip);
