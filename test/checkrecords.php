<?php
include_once('classes/settings.class.php');
include_once('classes/record.class.php');


$autofix = false;
$damage = false;
if($argc >= 2 && $argv[1] == 'true')
{
	$autofix = true;
	Settings::EnsureOnlyRunning();
	print "Bolk Record Checker\n";
	print "Running in Autofix mode\n";
}

Record::ForAll(function($record) use ($autofix, &$damage) {

	if(!is_file($record->file))
	{
		$damage = true;
		print $record->mbid . ": no audio file present\n";
		if($autofix)
		{
			unlink($record->path . '.mbinfo');
			rmdir($record->path);
			print " - removed record\n";
		}
	}

	if(!$record->info)
	{
		$damage = true;
		print $record->mbid . ": no info loaded\n";
		if($autofix)
		{
			unlink($record->path . '.mbinfo');
			$record->GetInfo();
			if(!$record->info)
				print " - cannot correct\n";
			else
				print " - fixed\n";
		}
	}
});

if($damage)
{
	if($autofix)
		print "Fix attemted, please rerun without autofix\n";
	else
		print "Damage detected, please run 'php " . $argv[0] . " true' to autocorrect. (This might remove some data)\n";
}
