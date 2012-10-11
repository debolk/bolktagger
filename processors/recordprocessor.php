<?php
include_once('classes/acoustid.class.php');
include_once('classes/musicbrainz.class.php');
include_once('classes/tagger.class.php');

print "Bolk Record Processor\n";
Settings::EnsureOnlyRunning();

$prefixes = scandir(Settings::SystemRecordPath);
foreach($prefixes as $prefix)
{
	if($prefix[0] == '.')
		continue;

	$records = scandir(Settings::SystemRecordPath . $prefix . '/');
	foreach($records as $record)
	{
		if($record[0] == '.')
			continue;

		// Get metadata
		$info = MusicBrainz::ParseRecordInfo(MusicBrainz::GetRecordMetadata($record));

		$title = Settings::CleanString($info->title);

		// Place in internal album folders
		foreach($info->releases as $release)
		{
			$relinfo = MusicBrainz::ParseReleaseInfo((MusicBrainz::GetReleaseMetadata($release->id)));

			$ambid = $relinfo->releaseGroup->id;
			$dir = Settings::SystemAlbumPath . substr($ambid, 0, 2) . '/' . $ambid . '/';
			if(!is_dir($dir))
				mkdir($dir, 0775, true);

			@symlink(Settings::SystemRecordPath . $prefix . '/' . $record . '/record', $dir . str_replace('/','',$title));
		}

		// Place in All folder
		$artist = Settings::CleanString($info->artistCredit[0]->name);
		$album = Settings::CleanString($info->releases[0]->title);

		$dir = Settings::AllAlbumsPath . str_replace('/','',$artist) . '/' . str_replace('/','',$album) . '/';
		$file = $dir . str_replace('/','',$title);

		if(!is_file($file))
		{
			if(!is_dir($dir))
				mkdir($dir, 0775, true);
			symlink(Settings::SystemRecordPath . $prefix . '/' . $record . '/record', $file);
			print $record . ': ' . $artist . ' - ' . $title . '[' . $album . "]: done\n";
		}
	}
}
