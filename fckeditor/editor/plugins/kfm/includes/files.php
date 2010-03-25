<?php
function _copyFiles($files,$dir_id){
	$copied=0;
	$dir=kfmDirectory::getInstance($dir_id);
	foreach($files as $fid){
		$file=kfmFile::getInstance($fid);
		if(!$file)continue;
		if($dir->addFile($file))$copied++;
	}
	kfm_addMessage(kfm_lang('filesCopied',$copied));
}
function _createEmptyFile($cwd,$filename){
	global $kfm_session;
	$dir=kfmDirectory::getInstance($cwd);
	$path=$dir->path;
	if(!kfm_checkAddr($path.$filename))return 'error: '.kfm_lang('illegalFileName',$filename);
	return(touch($path.$filename))?kfm_loadFiles($cwd):'error: '.kfm_lang('couldNotCreateFile',$filename);
}
function _downloadFileFromUrl($url,$filename){
	global $kfm_session,$kfm_default_upload_permission;
	$cwd_id=$kfm_session->get('cwd_id');
	$dir=kfmDirectory::getInstance($cwd_id);
	$cwd=$dir->getPath();
	if(!kfm_checkAddr($cwd.'/'.$filename))return kfm_lang('error: filename not allowed');
	if(substr($url,0,4)!='http')return kfm_lang('error: url must begin with http');
	$file=file_get_contents(str_replace(' ','%20',$url));
	if(!$file)return kfm_lang('failedDownloadFromUrl');
	if(!file_put_contents($cwd.'/'.$filename,$file))return kfm_lang('failedWriteToFile',$filename);
	chmod($to, octdec('0'.$kfm_default_upload_permission));
	return kfm_loadFiles($cwd_id);
}
function _extractZippedFile($id){
	global $kfm_session;
	$cwd_id=$kfm_session->get('cwd_id');
	if(!$GLOBALS['kfm_allow_file_create'])return 'error: '.kfm_lang('permissionDeniedCreateFile');
	$file=kfmFile::getInstance($id);
	$dir=$file->directory.'/';
	{ # try native system unzip command
		$res=-1;
		$arr=array();
		exec('unzip -l "'.$dir.$file->name.'"',$arr,$res);
		if(!$res){
			for($i=3;$i<count($arr)-2;++$i){
				$filename=preg_replace('/.* /','',$arr[$i]);
				if(!kfm_checkAddr($filename))return kfm_lang('errorZipContainsBannedFilename');
			}
			exec('unzip -o "'.$dir.$file->name.'" -x -d "'.$dir.'"',$arr,$res);
		}
	}
	if($res){ # try PHP unzip command
		return kfm_lang('error: unzip failed');
		# TODO: fix this
		$zip=zip_open($dir.$file->name);
		while($zip_entry=zip_read($zip)){
			$entry=zip_entry_open($zip,$zip_entry);
			$filename=zip_entry_name($zip_entry);
			$target_dir=$dir.substr($filename,0,strrpos($filename,'/'));
			$filesize=zip_entry_filesize($zip_entry);
			if(is_dir($target_dir)||mkdir($target_dir)){
				if($filesize>0){
					$contents=zip_entry_read($zip_entry,$filesize);
					file_put_contents($dir.$filename,$contents);
				}
			}
		}
	}
	return kfm_loadFiles($cwd_id);
}
function _getFileAsArray($filename){
	return explode("\n",rtrim(file_get_contents($filename)));
}
function _getFileDetails($fid){
	$file=kfmFile::getInstance($fid);
	if(!is_object($file))return kfm_lang('failedGetFileObject');
	$fpath=$file->path;
	if(!file_exists($fpath))return;
	$details=array(
		'id'=>$file->id,
		'name'=>$file->name,
		'filename'=>$file->name,
		'mimetype'=>$file->mimetype,
		'filesize'=>$file->size2str(),
		'tags'=>$file->getTags(),
		'ctime'=>$file->ctime,
		'writable'=>$file->isWritable()
	);
	if($file->isImage()){
		$details['caption']=$file->caption;
		$details['width']=$file->width;
		$details['height']=$file->height;
		$details['thumb_url']=$file->thumb_url;
	}
	return $details;
}
function _getFileUrl($fid,$x=0,$y=0){
	$file=kfmFile::getInstance($fid);
	return $file->getUrl($x,$y);
}
function _getFileUrls($fArr){
	$rArr=array();
	foreach($fArr as $f)$rArr[]=_getFileUrl($f);
	return $rArr;
}
function _getTagName($id){
	global $kfmdb;
	$r=db_fetch_row("select name from ".KFM_DB_PREFIX."tags where id=".$id);
	if(count($r))return array($id,$r['name']);
	return array($id,kfm_lang('UNKNOWN TAG',$id));
}
function _getTextFile($fid){
	$file=kfmFile::getInstance($fid);
	if(!kfm_checkAddr($file->name))return;
	$ext=$file->getExtension();
	if(!$file->isWritable())return 'error: '.kfm_lang('isNotWritable',$file->name);
	/**
	 * determine language for Codepress
	 */
	switch($ext){
		case 'html':
		case 'tpl':
			$language='html';
			break;
		case 'php':
			$language = 'php';
			break;
		case 'css':
			$language = 'css';
			break;
		case 'js':
			$language = 'javascript';
			break;
		case 'j':
			$language = 'java';
			break;
		case 'pl':
			$language = 'perl';
			break;
		case 'ruby':
			$language = 'ruby';
			break;
		case 'sql':
			$language = 'sql';
			break;
		case 'tex':
			$language = 'tex';
			break;
		case 'txt':
			$language = 'text';
			break;
		default:
			$language = 'generic';
			break;
	}
	return array('content'=>$file->getContent(),'name'=>$file->name,'id'=>$file->id,'language'=>$language);
}
function _loadFiles($rootid=1){
	global $kfm_session;
	$dir=kfmDirectory::getInstance($rootid);
	$oFiles=$dir->getFiles();
	if($dir->hasErrors())return $dir->getErrors();
	$files=array();
	foreach($oFiles as $file)$files[]=_getFileDetails($file);
	$root='/'.str_replace($GLOBALS['rootdir'],'',$dir->path);
	$kfm_session->set('cwd_id',$rootid);
	
	/// Addition		pBaran		11/07/2008		Foliovision
	setcookie( 'kfm_last_opened_dir', $rootid, time()+60*60*24*30 );
	/// End of addition		pBaran		11/07/2008
	
	return array('reqdir'=>$root,'files'=>$files,'uploads_allowed'=>$GLOBALS['kfm_allow_file_upload']); 
}
function _moveFiles($files,$dir_id){
	global $kfmdb,$kfm_session;
	$cwd_id=$kfm_session->get('cwd_id');
	foreach($files as $fid){
		$file=kfmFile::getInstance($fid);
		if(!$file)continue;
		$file->move($dir_id);
	}
	return kfm_loadFiles($cwd_id);
}
function _renameFile($fid,$newfilename,$refreshFiles=true){
	global $kfm_session;
	$file=kfmFile::getInstance($fid);
	$file->rename($newfilename);
	if($file->hasErrors())return $file->getErrors();
	if($refreshFiles)return kfm_loadFiles($kfm_session->get('cwd_id'));
}
function _renameFiles($files,$template){
	global $kfm_session;
	$cwd_id=$kfm_session->get('cwd_id');
	if(!$GLOBALS['kfm_allow_file_edit'])return 'error: '.kfm_lang('permissionDeniedEditFile');
	$prefix=preg_replace('/\*.*/','',$template);
	$postfix=preg_replace('/.*\*/','',$template);
	$precision=strlen(preg_replace('/[^*]/','',$template));
	for($i=1;$i<count($files)+1;++$i){
		$num=str_pad($i,$precision,'0',STR_PAD_LEFT);
		$ret=_renameFile($files[$i-1],$prefix.$num.$postfix,false);
		if($ret)return $ret; # error detected
	}
	return kfm_loadFiles($cwd_id);
}
function _resize_bytes($size){
	$count=0;
	$format=array("B","KB","MB","GB","TB","PB","EB","ZB","YB");
	while(($size/1024)>1&&$count<8){
		$size=$size/1024;
		++$count;
	}
	$return=number_format($size,0,'','.')." ".$format[$count];
	return $return;
}
function _rm($id){
	if(is_array($id)){
		$counter=0;
		foreach($id as $fid){
			$file=kfmFile::getInstance($fid);
			if($file->delete())$counter++;
		}
		if($counter>1)kfm_addMessage(kfm_lang(' files deleted',$counter));
	}
	else{
		$file=kfmFile::getInstance($id);
		$file->delete();
	}
	return $id;
}
function _saveTextFile($fid,$text){
	if(!$GLOBALS['kfm_allow_file_edit'])return 'error: '.kfm_lang('permissionDeniedEditFile');
	$f=kfmFile::getInstance($fid);
	$f->setContent($text);
	return $f->hasErrors()?$f->getErrors():kfm_lang('file saved');
}

/// Add		pBaran		18/12/2007		Foliovision
/**
 *	Gets all directories and subdirectories, of selected directory, ids into array 
 *
 *	@param int $idDir		ID of curently selected directory
 *
 *	@return array			Array filled with ids of subdirectories of curently selected directory. Also contains id of curently selected directory
 */     
function kfmAdd_get_child_directories( $idDir ){

	$aReturn = array();

	try{
		$aDirs = db_fetch_all( "SELECT id, parent FROM " . KFM_DB_PREFIX . "directories" );
		if( !is_array( $aDirs ) ) return false;
		else{
			foreach( $aDirs as $aRow ){
				if( $aRow['parent'] == $idDir && !in_array( $aRow['parent'], $aReturn ) ) $aReturn[] = $aRow['parent'];
				if( $aRow['parent'] == $idDir || $aRow['id'] == $idDir || in_array( $aRow['parent'], $aReturn ) ) $aReturn[] = $aRow['id'];
			}
		}
	}catch( Exception $ex ){}
	
	return $aReturn;
}
///End of add		pBaran		18/12/2007

//function _search($keywords,$tags){
function _search( $idDir, $keywords, $tags ){
	global $kfmdb;
	$files=array();
	$valid_files=array();
	if($tags){
		$arr=explode(',',$tags);
		foreach($arr as $tag){
			$tag=ltrim(rtrim($tag));
			if($tag){
				$r=db_fetch_row("select id from ".KFM_DB_PREFIX."tags where name='".sql_escape($tag)."'");
				if(count($r)){
					if(count($valid_files))$constraints=' and (file_id='.join(' or file_id=',$valid_files).')';
					$rs2=db_fetch_all("select file_id from ".KFM_DB_PREFIX."tagged_files where tag_id=".$r['id'].$constraints);
					if(count($rs2)){
						$valid_files=array();
						foreach($rs2 as $r2)$valid_files[]=$r2['file_id'];
					}
					else $valid_files=array(0);
				}
			}
		}
	}
	if(($tags&&count($valid_files))||$keywords){ # keywords
		$constraints='';
		if(count($valid_files))$constraints=' and (id='.join(' or id=',$valid_files).')';
		
		/// Change		pBaran		18/12/2007		Foliovision
		/// search will be performed only on curent directory and its subdirectories
		$aDirs = kfmAdd_get_child_directories( $idDir );
		
		//$fs=db_fetch_all("select id from ".KFM_DB_PREFIX."files where name like '%".sql_escape($keywords)."%'".$constraints." order by name");
		$fs=db_fetch_all("SELECT id, directory FROM ".KFM_DB_PREFIX."files WHERE name LIKE '%".sql_escape($keywords)."%'".$constraints." ORDER BY name");
		
		foreach($fs as $f){
			if( !in_array( $f['directory'], $aDirs ) ) continue;
			/// End of change		pBaran		18/12/2007
			
			$file=kfmFile::getInstance($f['id']);
			if(!$file->checkName())continue;
			unset($file->db);
			$files[]=$file;
		}
	}
	return array('reqdir'=>kfm_lang('searchResults'),'files'=>$files,'uploads_allowed'=>0);
}
function _tagAdd($recipients,$tagList){
	if(!$GLOBALS['kfm_allow_file_edit'])return 'error: '.kfm_lang('permissionDeniedEditFile');
	global $kfmdb;
	if(!is_array($recipients))$recipients=array($recipients);
	$arr=explode(',',$tagList);
	$tagList=array();
	foreach($arr as $v){
		$v=ltrim(rtrim($v));
		if($v)$tagList[]=$v;
	}
	if(count($tagList))foreach($tagList as $tag){
		$r=db_fetch_row("select id from ".KFM_DB_PREFIX."tags where name='".sql_escape($tag)."'");
		if(count($r)){
			$tag_id=$r['id'];
			$kfmdb->query("delete from ".KFM_DB_PREFIX."tagged_files where tag_id=".$tag_id." and (file_id=".join(' or file_id=',$recipients).")");
		}
		else{
			$q=$kfmdb->query("insert into ".KFM_DB_PREFIX."tags (name) values('".sql_escape($tag)."')");
			$tag_id=$kfmdb->lastInsertId(KFM_DB_PREFIX.'tags','id');
		}
		foreach($recipients as $file_id)$kfmdb->query("insert into ".KFM_DB_PREFIX."tagged_files (tag_id,file_id) values(".$tag_id.",".$file_id.")");
	}
	return _getFileDetails($recipients[0]);
}
function _tagRemove($recipients,$tagList){
	if(!$GLOBALS['kfm_allow_file_edit'])return 'error: '.kfm_lang('permissionDeniedEditFile');
	global $kfmdb;
	if(!is_array($recipients))$recipients=array($recipients);
	$arr=explode(',',$tagList);
	$tagList=array();
	foreach($arr as $tag){
		$tag=ltrim(rtrim($tag));
		if($tag){
			$r=db_fetch_row("select id from ".KFM_DB_PREFIX."tags where name='".sql_escape($tag)."'");
			if(count($r))$tagList[]=$r['id'];
		}
	}
	if(count($tagList))$kfmdb->exec("delete from ".KFM_DB_PREFIX."tagged_files where (file_id=".join(' or file_id=',$recipients).") and (tag_id=".join(' or tag_id="',$tagList).")");
	return _getFileDetails($recipients[0]);
}
function _zip($filename,$files){
	global $kfm_session;
	$cwd_id=$kfm_session->get('cwd_id');
	$dir=kfmDirectory::getInstance($cwd_id);
	$cwd=$dir->path;
	if(!$GLOBALS['kfm_allow_file_create'])return 'error: '.kfm_lang('permissionDeniedCreateFile');
	global $rootdir;
	if(!kfm_checkAddr($cwd.'/'.$filename))return 'error: '.kfm_lang('illegalFileName',$filename);
	$arr=array();
	foreach($files as $f){
		$file=kfmFile::getInstance($f);
		if(!$file)return 'error: '.kfm_lang('missingFileInSelection');
		$arr[]=$file->path;
	}
	{ # try native system zip command
		$res=-1;
		$pdir=$cwd.'/';
		$zipfile=$pdir.$filename;
		for($i=0;$i<count($arr);++$i)$arr[$i]=str_replace($pdir,'',$arr[$i]);
		exec('cd "'.$cwd.'" && zip -D "'.$zipfile.'" "'.join('" "',$arr).'"',$arr,$res);
	}
	if($res)return 'error: '.kfm_lang('noNativeZipCommand');
	return kfm_loadFiles($cwd_id);
}
?>
