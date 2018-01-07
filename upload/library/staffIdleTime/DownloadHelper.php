<?php
class staffIdleTime_DownloadHelper{
	public static function sendAsDownload($downloadable='',
	  $fname='download_file.txt',$mime='application/octetstream',
	  $forceDownload = true){
		$dtype = (($forceDownload)?'attachment':'inline');
		$mime = (($forceDownload)?$mime:'text/plain');
		$fsize = strlen($downloadable);
		header('Content-Type: '.$mime);
		header('Content-Disposition: '.$dtype.'; filename="'.$fname.'"');
		header('Content-Length: ' . $fsize);
		header('Connection: close');
		die($downloadable);
	}
}

