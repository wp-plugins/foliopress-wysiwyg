<?php
$imageInstances=array();
class kfmImage extends kfmFile{
	var $caption='';
	var $width;
	var $height;
	var $thumb_url;
	var $thumb_id;
	var $thumb_path;
	var $info=array(); # info from getimagesize
	function kfmImage($file){
		if(is_object($file) && $file->isImage())parent::kfmFile($file->id);
		else if(is_numeric($file))parent::kfmFile($file);
		else return false;
		if(!$this->exists()){
			$this->delete();
			return false;
		}
		$this->image_id=$this->getImageId();
		if($this->getSize()){
			$this->info=getimagesize($this->path);
			$this->type=str_replace('image/','',$this->info['mime']);
			$this->width=$this->info[0];
			$this->height=$this->info[1];
		}
		else{
			$this->type='null';
			$this->width=0;
			$this->height=0;
		}
	}
	
	/// Addition		pBaran		17/07/2008		Foliovision
	
	/**
	 * Checks if image has some colors with semi-alpha value.
	 *
	 * Checks alpha value of color. If this value is not 0 (oblique) or 127(transparent) it returns
	 * true upon finding the first of such colors, or it goes through all pixels in image and returns
	 * false. Order of going through pixels is by columns from 0.th column to last column. <b>This 
	 * method works only with TrueColor images.</b>
	 *
	 * @param pointer &$img Pointer to image resource
	 * @return bool True if TrueColor image is complex (e.g. contains semi-alpha color), false otherwise
	 */
	function IsComplex( &$img ){
		if( !imageistruecolor( $img ) ) return false;
		
		for( $i=0; $i<$this->width; $i++ ){
			for( $j=0; $j<$this->height; $j++ ){
				$iColor = imagecolorat( $img, $i, $j );
				$iAlpha = ($iColor & 0x7F000000) >> 24;
				if( 0 != $iAlpha && 127 != $iAlpha ) return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Counts number of unique colors and gets position of first transparent color.
	 *
	 * Counts number of unique colors used in this image. If $bTransparency is set to 'true' then
	 * this function also tries to get first transparent color (alpha = 127) and record it position.
	 * Order of going through pixels is by columns from 0.th to last. <b>This method works only
	 * with TrueColor images.</b>
	 *
	 * @param pointer &$img Pointer to image resource
	 * @param integer $iWidth Width of area in which to search and count
	 * @param integer $iHeight Height of area in which to search and count
	 * @param boolean $bTransparency If true, then method also tries to get position of first transparency color.
	 * @return array|boolean False if inserted image is not TrueColor, or is damaged in another way. Array keys:
	 * - colors : Number of unique colors used in image found
	 * - transparent : Array with 'x' and 'y' as keys determining position of pixel with fully transparent color	 	 
	 */
	function CountUniqueColors( &$img, $iWidth, $iHeight, $bTransparency = false ){
		if( !imageistruecolor( $img ) ) return false;
		
		$aColors = array();
		$aTrans = array();
		$aTrans['position'] = '';
		
		for( $i=0; $i<$iWidth; $i++ ){
			for( $j=0; $j<$iHeight; $j++ ){
				$iColor = imagecolorat( $img, $i, $j );
				if( isset( $aColors[$iColor] ) ) $aColors[$iColor] += 1;
				else $aColors[$iColor] = 1;
				
				if( $bTransparency ){
					$iAlpha = ($iColor & 0x7F000000) >> 24;
					if( 127 == $iAlpha ){
						$aTrans['position'] = array( 'x' => $i, 'y' => $j );
						$bTransparency = false;
					}
				}
			}
		}
		
		return array( 'colors' => count( $aColors ), 'transparent' => $aTrans['position'] );
	}
	
	/**
	 * Atempts to converts TrueColor image to Indexed image
	 *
	 * The conversion is done by GD function imagetruecolortopalette. In indexed image maximum of 255
	 * colors will be used. If Second parameter ($aColorInfo) is suplied, function will atempt
	 * to preserve transparency.
	 *
	 * @param pointer &$imgSource Pointer to image resource
	 * @param array $aColorInfo Array that contains information about image {@link kfmImage::CountUniqueColors returned by CountUniqueColors}
	 */
	function TrueColorToIndexed( &$imgSource, $aColorInfo = NULL ){
		imagetruecolortopalette( $imgSource, true, 255 );
		
		if( is_array( $aColorInfo ) && isset( $aColorInfo['transparent'] ) && $aColorInfo['transparent'] ){
			$aPosition = $aColorInfo['transparent'];
			$iColorIndex = imagecolorat( $imgSource, $aPosition['x'], $aPosition['y'] );
			imagecolortransparent( $imgSource, $iColorIndex );
		}
	}
	
	/**
	 * Creates resampled copy if PNG image in destination
	 *
	 * This method resaples the image to new size, if set globaly, will try to index-color
	 * the image and saves it to suplied destination. Indexation of colors is done by
	 * {@link kfmImage::TrueColorToIndexed TrueColorToIndexed} and only if PNG is not
	 * {@link kfmImage::IsComplex Complex} and doesn't have to much unique colors. If PNG already
	 * is with indexed colors, it will stay that way.
	 *
	 * @global bool Used as settings if KFM shoud atempt to convert TrueColor images to Indexed images
	 * @global integer Used as settings which tells KFM the limit of unique colors in image, from which KFM should not convert TrueColor image
	 * @param string $strTo Destination path, where to save created image
	 * @param integer $iDestWidth Width of destination image
	 * @param integer $iDestHeight Height of destination image
	 */
	function CreateResizedCopyPNG( $strTo, $iDestWidth, $iDestHeight ){
		global $bTransformTrueColorToPalette, $iTrueColorToPaletteLimit;
		
		$imgSource = imagecreatefrompng( $this->path );
		$bIsTrueColor = imageistruecolor( $imgSource );
		
		$imgDest = imagecreatetruecolor( $iDestWidth, $iDestHeight );
			
		imagealphablending( $imgDest, false );
		imagecopyresampled( $imgDest, $imgSource, 0, 0, 0, 0, $iDestWidth, $iDestHeight, $this->width, $this->height );
		imagesavealpha( $imgDest, true );
		
		if( $bTransformTrueColorToPalette ){
			$aColors = $this->CountUniqueColors( $imgDest, $iDestWidth, $iDestHeight, true );
			
			if( $aColors && $iTrueColorToPaletteLimit > $aColors['colors'] ){
				$bComplex = $this->IsComplex( $imgSource );
				
				if( !$bComplex ) $this->TrueColorToIndexed( $imgDest, $aColors );
			}
		}
		
		imagepng( $imgDest, $strTo, 9 );
		
		imagedestroy( $imgSource );
		imagedestroy( $imgDest );
	}
	
	/**
	 * Creates resampled copy of GIF image in destination
	 *
	 * This function is specialy designed to handle resampling (resizing) of GIF images, since
	 * original KFM wasn't able to properly do this. This function maintains transparency and
	 * palette that has been used.
	 *
	 * @param string $strTo Destination path, where to save created image
	 * @param integer $iDestWidth Width of destination image
	 * @param integer $iDestHeight Height of destination image
	 */
	function CreateResizedCopyGIF( $strTo, $iDestWidth, $iDestHeight ){
		$imgSource = imagecreatefromgif( $this->path );
		$imgDest = imagecreatetruecolor( $iDestWidth, $iDestHeight );

		imagealphablending( $imgDest, false );
		imagecopyresampled( $imgDest, $imgSource, 0, 0, 0, 0, $iDestWidth, $iDestHeight, $this->width, $this->height );
		imagesavealpha( $imgDest, true );
		
		imagegif( $imgDest, $strTo, 9 );
		
		imagedestroy( $imgSource );
		imagedestroy( $imgDest );
	}
	//// End of addition		pBaran		17/07/2008
	
	/// Change		pBaran		17/07/2008		Foliovision
	/// this function has been reformated and changed
	function createResizedCopy( $strTo, $iDestWidth, $iDestHeight ){
		global $iJPGQuality;
		if( !isset( $iJPGQuality ) || !$iJPGQuality ) $iJPGQuality = 70;
		
		$strFunctionLoad = 'imagecreatefrom' . $this->type;
		$strFunctionSave = 'image' . $this->type;
		
		if( !function_exists( $strFunctionLoad ) || !function_exists( $strFunctionSave ) )
			return $this->error( 'server cannot handle image of type "' . $this->type . '"' );
		
		if( 'png' == $this->type ){
			$this->CreateResizedCopyPNG( $strTo, $iDestWidth, $iDestHeight );
			return;
		}
		
		if( 'gif' == $this->type ){
			$this->CreateResizedCopyGIF( $strTo, $iDestWidth, $iDestHeight );
			return;
		}
		
		$imgSource = $strFunctionLoad( $this->path );
		$imgDest = imagecreatetruecolor( $iDestWidth, $iDestHeight );
		
		imagealphablending( $imgDest, false );
		imagecopyresampled( $imgDest, $imgSource, 0, 0, 0, 0, $iDestWidth, $iDestHeight, $this->width, $this->height );
		imagesavealpha( $imgDest, true );
		
		$strFunctionSave( $imgDest, $strTo, ( $this->type == 'jpeg' ? $iJPGQuality : 9 ) );
		
		imagedestroy( $imgDest );
		imagedestroy( $imgSource );
	}
	/// End of change		pBaran		17/07/2008
	
	
	function createThumb($width=64,$height=64,$id=0){
		global $kfm_use_imagemagick;
		if(!is_dir(WORKPATH.'thumbs'))mkdir(WORKPATH.'thumbs');
		$ratio=min($width/$this->width,$height/$this->height);
		$thumb_width=$this->width*$ratio;
		$thumb_height=$this->height*$ratio;
		if(!$id){
			$this->db->exec("INSERT INTO ".KFM_DB_PREFIX."files_images_thumbs (image_id,width,height) VALUES(".$this->id.",".$thumb_width.",".$thumb_height.")");
			$id=$this->db->lastInsertId(KFM_DB_PREFIX.'files_images_thumbs','id');
		}
		$file=WORKPATH.'thumbs/'.$id;
		if(!$kfm_use_imagemagick || $this->useImageMagick($this->path,'resize '.$thumb_width.'x'.$thumb_height,$file))$this->createResizedCopy($file,$thumb_width,$thumb_height);
		return $id;
	}

### Added		pBaran		10/12/2007		Foliovision
	function createSpecialThumb( $iSize, $id=0 ){
		global $kfm_use_imagemagick, $rootdir, $kfm_default_directory_permission, $kfm_default_upload_permission;
		
		$width = $iSize;
		$height = $iSize;
		
		$strPath = $this->directory . $iSize;
		$strPath = str_replace( "\\", "/", $strPath );
		$strPath = str_replace( "//", "/", $strPath );
		
		if( !is_dir( $strPath ) ) mkdir( $strPath );
		
		$ratio = $width / $this->width;
		$thumb_width = $this->width * $ratio;
		$thumb_height = $this->height * $ratio;
		
		if(!$id){
			$this->db->exec("INSERT INTO ".KFM_DB_PREFIX."files_images_thumbs (image_id,width,height) VALUES(".$this->id.",".$thumb_width.",".$thumb_height.")");
			$id=$this->db->lastInsertId(KFM_DB_PREFIX.'files_images_thumbs','id');
		}
		
		$file = $strPath . '/' . $this->name;
		if(!$kfm_use_imagemagick || $this->useImageMagick($this->path,'resize '.$thumb_width.'x'.$thumb_height,$file))$this->createResizedCopy($file,$thumb_width,$thumb_height);

		@chmod( $strPath, octdec( '0' . $kfm_default_directory_permission ) );
		@chmod( $file, octdec( '0' . $kfm_default_upload_permission ) );

		return $id;
	}
### Added		pBaran		10/12/2007		Foliovision

	function delete(){
		if(!$GLOBALS['kfm_allow_file_delete'])return $this->error(kfm_lang('permissionDeniedDeleteFile'));
		if(!parent::delete())return false;
		$this->deleteThumbs();
		$this->db->exec('DELETE FROM '.KFM_DB_PREFIX.'files_images WHERE file_id='.$this->id);
		return !$this->hasErrors();
	}
	function deleteThumbs(){
		$rs=db_fetch_all("SELECT id FROM ".KFM_DB_PREFIX."files_images_thumbs WHERE image_id=".$this->id);
		foreach($rs as $r){
			$icons=glob(WORKPATH.'thumbs/'.$r['id'].'*');
			foreach($icons as $f)unlink($f);
		}
		$this->db->exec("DELETE FROM ".KFM_DB_PREFIX."files_images_thumbs WHERE image_id=".$this->id);
	}
	function getImageId(){
		$row=db_fetch_row("SELECT id,caption FROM ".KFM_DB_PREFIX."files_images WHERE file_id='".$this->id."'");
		if(!$row){ # db record not found. create it
			# TODO: retrieve caption generation code from get.php
			$sql="INSERT INTO ".KFM_DB_PREFIX."files_images (file_id, caption) VALUES ('".$this->id."','".$this->name."')";
			$this->caption=$this->name;
			$this->db->exec($sql);
			return $this->db->lastInsertId(KFM_DB_PREFIX.'files_images','id');
		}
		$this->caption=$row['caption'];
		return $row['id'];
	}
	function getInstance($id=0){
		if(!$id)return false;
		global $imageInstances;
		if(is_object($id)){
			if($id->isImage())$id=$id->id;
			else return false;
		}
		if(!isset($imageInstances[$id]))$imageInstances[$id]=new kfmImage($id);
		return $imageInstances[$id];
	}
	function resize($new_width, $new_height=-1){
		global $kfm_use_imagemagick,$kfm_allow_image_manipulation;
		if(!$kfm_allow_image_manipulation)$this->error(kfm_lang('permissionDeniedManipImage'));
		if(!$this->isWritable())$this->error(kfm_lang('imageNotWritable'));
		if($this->hasErrors())return false;
		$this->deleteThumbs();
		if($new_height==-1)$new_height=$this->height*$new_width/$this->width;
		if($kfm_use_imagemagick && !$this->useImageMagick($this->path,'resize '.$new_width.'x'.$new_height,$this->path))return;
		$this->createResizedCopy($this->path,$new_width,$new_height);
	}
	function rotate($direction){
		global $kfm_use_imagemagick,$kfm_allow_image_manipulation;
		if(!$kfm_allow_image_manipulation)$this->error(kfm_lang('permissionDeniedManipImage'));
		if(!$this->isWritable())$this->error(kfm_lang('imageNotWritable'));
		if($this->hasErrors())return false;
		$this->deleteThumbs();
		if($kfm_use_imagemagick && !$this->useImageMagick($this->path,'rotate -'.$direction,$this->path))return;
		{ # else use GD
			$load='imagecreatefrom'.$this->type;
			$save='image'.$this->type;
			$im=$load($this->path);
			$im=imagerotate($im,$direction,0);
			$save($im,$this->path,($this->type=='jpeg'?100:9));
			imagedestroy($im);
		}
	}
	function crop($x1, $y1, $width, $height, $newname=false){
		global $kfm_use_imagemagick,$kfm_allow_image_manipulation;
		if(!$kfm_allow_image_manipulation)return $this->error(kfm_lang('permissionDeniedManipImage'));
		
		if(!$newname){
			$this->deleteThumbs();
			if(!$this->isWritable())return $this->error(kfm_lang('imageNotWritable'));
		}
		if($kfm_use_imagemagick && $newname && !$this->useImageMagick($this->path,'crop '.$width.'x'.$height.'+'.$x1.'+'.$y1, dirname($this->path).'/'.$newname))return;
		else if($kfm_use_imagemagick && !$this->useImageMagick($this->path,'crop '.$width.'x'.$height.'+'.$x1.'+'.$y1, $this->path))return;
		{ # else use GD
			$load='imagecreatefrom'.$this->type;
			$save='image'.$this->type;
			$im=$load($this->path);
			$cropped = imagecreatetruecolor($width, $height);
			imagecopyresized($cropped, $im, 0, 0, $x1, $y1, $width, $height, $width, $height);
			imagedestroy($im);
			if($newname){
				$save($cropped, dirname($this->path).'/'.$newname, ($this->type=='jpeg'?100:9));
			}else{
				$save($cropped,$this->path,($this->type=='jpeg'?100:9));
			}
			imagedestroy($cropped);
		}
	}
	function setCaption($caption){
		$this->db->exec("UPDATE ".KFM_DB_PREFIX."files_images SET caption='".sql_escape($caption)."' WHERE file_id=".$this->id);
		$this->caption=$caption;
	}
	function setThumbnail($width=64,$height=64){
		$thumbname=$this->id.' '.$width.'x'.$height.' '.$this->name;
		if(!isset($this->info['mime'])||!in_array($this->info['mime'],array('image/jpeg','image/gif','image/png')))return false;
		$r=db_fetch_row("SELECT id FROM ".KFM_DB_PREFIX."files_images_thumbs WHERE image_id=".$this->id." and width<=".$width." and height<=".$height." and (width=".$width." or height=".$height.")");
		if($r){
			$id=$r['id'];
			if(!file_exists(WORKPATH.'thumbs/'.$id))$this->createThumb($width,$height,$id); // missing thumb file - recreate it
		}
		else{
			$id=$this->createThumb($width,$height);
		}
		$this->thumb_url='get.php?type=thumb&id='.$id.GET_PARAMS;
		$this->thumb_id=$id;
		$this->thumb_path=str_replace('//','/',WORKPATH.'thumbs/'.$id);
		if(!file_exists($this->thumb_path)){
			copy(WORKPATH.'thumbs/'.$id.'.'.preg_replace('/.*\//','',$this->info['mime']),$this->thumb_path);
			unlink(WORKPATH.'thumbs/'.$id.'.'.preg_replace('/.*\//','',$this->info['mime']));
		}
		if(!file_exists($this->thumb_path))$this->createThumb();
	}
	
### Added		pBaran		10/12/2007		Foliovision
	function setSpecialThumbnail( $iSize ){
		global $rootdir;
		$width = $iSize;
		$height = $iSize;
		
		$strPath = $this->directory . $iSize;
		$strPath = str_replace( "\\", "/", $strPath );
		$strPath = str_replace( "//", "/", $strPath );
		
		$thumbname=$this->id.' '.$width.'x'.$height.' '.$this->name;
		if(!isset($this->info['mime'])||!in_array($this->info['mime'],array('image/jpeg','image/gif','image/png')))return false;
		$r=db_fetch_row("SELECT id FROM ".KFM_DB_PREFIX."files_images_thumbs WHERE image_id=".$this->id." and width=".$width);
		if($r){
			$id=$r['id'];
			if( !file_exists( $strPath.'/'.$this->name ) ) $this->createSpecialThumb( $iSize, $id ); // missing thumb file - recreate it
		}
		else{
			$id = $this->createSpecialThumb( $iSize );
		}
		$this->thumb_url = 'get.php?type=thumb&id='.$id.GET_PARAMS;
		$this->thumb_id = $id;
		$this->thumb_path = str_replace('//','/', $strPath.'/'.$this->name);
	}
### End of add		pBaran		10/12/2007
	
	function useImageMagick($from,$action,$to){
		if(!file_exists(IMAGEMAGICK_PATH))return true;
		$retval=true;
		$arr=array();
		exec(IMAGEMAGICK_PATH.' "'.$from.'" -'.$action.' "'.$to.'"',$arr,$retval);
		return $retval;
	}
}
?>
