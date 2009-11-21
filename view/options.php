<script type="text/javascript" src="<?php print( $this->strPluginPath . self::FVC_OPTIONS_JS_PATH ); ?>"></script>
<script type="text/javascript" src="<?php print( $this->strPluginPath . self::FVC_FV_REGEX_PATH ); ?>"></script>

<?php if( isset( $strMessage ) ) : ?>
<div class="wrap">
	<?php echo $strMessage; ?>
</div>
<?php endif; ?>

<div class="wrap">
    <div id="icon-tools" class="icon32"><br /></div>
	<form id="main_form" method="post" action="<?php print( $_SERVER["REQUEST_URI"] );?>">
		<h2><?php print( FV_FCK_NAME ); ?></h2>
		<h3>Basic Options</h3>
		<table class="form-table"> 
            <tr valign="top"> 
                <th scope="row"><label for="ImagesPath">Path to images on your web server</label></th>
                <td><input type="text" name="ImagesPath" value="<?php print( $this->aOptions[self::FVC_IMAGES] ); ?>" class="regular-text" /></td>
            </tr>
            <tr valign="top"> 
                <th scope="row"><label for="FCKSkins">FCKEditor skin</label></th>
				<td><?php
					print( '<select name="FCKSkins">' );
					
					try{
						$aFCKSkins = fp_wysiwyg_load_fck_skins( realpath( $strPath . self::FVC_SKINS_RELATIVE_PATH ) );
						fp_wysiwyg_output_options( $aFCKSkins, $this->aOptions[self::FVC_SKIN] );
						
						print( '</select>' );
						
					}catch( Exception $ex ){
						$bError = true;
						print( '</select>' );
						print( ' ERROR: ' . $ex->getMessage() );
					}
                ?></td>
			</tr>
			<tr valign="top"> 
                <th scope="row"><label for="FCKToolbar">FCKEditor toolbar</label></th> 
				<td><?php
					print( '<select name="FCKToolbar">' );
					
					try{
						$aToolbarSets = fp_wysiwyg_load_fck_toolbars( realpath( $strPath . '/' . self::FVC_FCK_CONFIG_RELATIVE_PATH ) );
						fp_wysiwyg_output_options( $aToolbarSets, $this->aOptions[self::FVC_TOOLBAR] );

						print( '</select>' );
						
					}catch( Exception $ex ){
						$bError = true;
						print( '</select>' );
						print( ' ERROR: ' . $ex->getMessage() );
					}
				?></td>
			</tr>
			<tr valign="top"> 
                <th scope="row"><label for="FCKWidth">Width of FCKEditor</label></th>
				<td><input type="text" name="FCKWidth" value="<?php print( $this->aOptions[self::FVC_WIDTH] ); ?>" class="small-text" /><span class="description">(0 is default, for unlimited width)</span></td>
			</tr>
			<tr valign="top"> 
                <th scope="row"><label for="HideMediaButtons">Disable Wordpress uploader buttons</label></th>
                <td><input id="chkHideMediaButtons" type="checkbox" name="HideMediaButtons" value="checkbox" <?php if($this->aOptions[self::FVC_HIDEMEDIA]) echo 'checked="checked"'; ?> /></td>
			</tr>
			<tr valign="top"> 
                <th scope="row"><label for="HideMediaButtons">Multiple image posting</label></th>
                <td><input id="chkMultipleImagePosting" type="checkbox" name="MultipleImagePosting" value="checkbox" <?php if($this->aOptions['multipleimageposting']) echo 'checked="checked"'; ?> /><span class="description">Disable if you want image management window to close automatically after posting a single image.</span></td>
			</tr>
			<tr valign="top"> 
                <th scope="row"><label for="listKFMThumbs">Thumbnail sizes</label></th>
				<td>
                    <select id="listKFMThumbs" style="width: 100px;"></select>
                        <input type="text" style="width: 80px;" name="AddThumbSize" id="txtAddThumbSize" value="Add new" onclick="this.value=''" />
                        <input type="button" class="button" value="Add" onclick="KFMAddThumbnail()" />
                        <input type="hidden" value="0" name="KFMThumbCount" id="hidThumbCount" />
                    <input type="button" class="button" value="Remove selected" onclick="KFMRemoveThumbnail()" />
                    <br />
				</td>
			</tr>
			<tr valign="top"> 
                <th scope="row">WYSIWYG CSS styles</th>
                <td><fieldset>
                        <label for="bodyid"><input id="bodyid" type="text" name="bodyid" value="<?php echo $this->aOptions['bodyid']; ?>" class="regular-text" /> Post ID</label><br />
                        <label for="bodyclass"><input id="bodyclass" type="text" name="bodyclass" value="<?php echo $this->aOptions['bodyclass']; ?>" class="regular-text" /> Post class</label>
                        <br />
                        <span class="description">Enter the name of the class used for styling of the articles on the front page. If necessary, use multiple classes (separated by blank spaces) or add the ID of the container element.</span>
                    </fieldset></td>
            </tr>
		</table>
		<br />
		
		<p><input type="button" name="advanced_options" class="button" value="Advanced Options" onclick="ShowAdvancedOptions()" /></p>
		<div id="divAdvanced" style="visibility: hidden; position: absolute; top: 10px;">
			<h3>Advanced Options</h3>
			<table class="form-table">
                <tr valign="top">
                    <th scope="row">Thumbnails</th>
                    <td><fieldset>
                        <label for="KFMLink"><input id="chkKFMLink" type="checkbox" name="KFMLink" value="yes" onclick="KFMLink_change()" /> Thumbnail image should link to the full-sized image</label><br />
                        <label for="KFMLightbox"><input id="chkKFMLightbox" type="checkbox" name="KFMLightbox" value="yes" /> Allow full-sized images to be opened with the lightbox effect</label>
                    </fieldset></td>
                </tr>
                
				<tr valign="top"> 
                    <th scope="row"><label for="listFPClean">FPClean</label></th>
					<td>
                        <select id="listFPClean" style="width: 250px;"></select>
                            <input type="text" name="AddSpecialText" id="txtAddFPClean" class="regular-text" value="Add new" onclick="this.value=''" />
                            <input type="button" class="button" value="Add" onclick="FPCleanAddText()" />
                            <input type="hidden" value="0" name="FPCleanCount" id="hidFPCleanCount" />
                        <input type="button" class="button" value="Remove selected" onclick="FPCleanRemoveText()" /><br />
                        <span class="description">Matching text will be striped of '&lt;p&gt;' and '&lt;div&gt;' tags</span>
					</td>
				</tr>
				<tr valign="top"> 
                    <th scope="row">JPEG Images</th>
                    <td><label for="JPEGQuality">Quality <input type="text" name="JPEGQuality" value="<?php echo $this->aOptions[self::FVC_JPEG]; ?>" class="small-text" /></label></td>
                </tr>
				<tr valign="top"> 
                    <th scope="row">PNG Images</th>
                    <td><fieldset>
                        <label for="PNGTransform"><input id="chkPNGTransform" type="checkbox" name="PNGTransform" value="yes" onclick="KFM_CheckPNG( !bPNGTransform );"<?php if( $this->aOptions[self::FVC_PNG] ) echo ' checked="checked"'; ?> /> Transform not colorful true-color PNG images to 8-bit color PNG</label><br />
                        <label for="PNGLimit">Limit of colorful true-color PNG <input type="text" id="txtPNGLimit" name="PNGLimit" value="<?php echo $this->aOptions[self::FVC_PNG_LIMIT]; ?>" class="small-text" /></label>
                    </fieldset></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="customtoolbar">Custom Toolbar</label></th>
                    <td><textarea rows="8" cols="80" name="customtoolbar"><?php echo $this->aOptions['customtoolbar']; ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="customdropdown">Dropdown Customization</label></th>
                    <td><textarea rows="8" cols="80" name="customdropdown"><?php echo $this->aOptions['customdropdown']; ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Compatibility</th>
                    <td><fieldset>
                        <label for="PreWPAutop"><input id="chkPreWPAutop" type="checkbox" name="PreWPAutop" value="yes" <?php if($this->aOptions['autowpautop']) echo 'checked="checked"'; ?> /> Do wpautop before editing a post if there are no <code>&lt;p&gt;</code> tags in the post.</label><br /><span class="description">If your posts were created with the default Wordpress editor (TinyMCE), you need to leave this on, as TinyMCE is storing posts without any HTML markup for the paragraphs. You may want to disable it if some of your special posts are destroyed after opening with Foliopress WYSIWYG.</span><br />
                    </fieldset></td>
                </tr>
                                
			</table>
			<br />
			<p><input type="button" name="expert_options" class="button" value="Expert Options" onclick="ShowExpertOptions()" /></p>
			<div id="divExpert" style="visibility: hidden; position: absolute; top: 10px;">
				<h3>Expert Options</h3>
				<table class="form-table">
					<tr>
                        <td>
                            <?php if( is_writable( $strPath.'/'.self::FVC_FCK_CONFIG_RELATIVE_PATH ) ) : ?>
                                <input type="button" class="button" name="edit" value="Edit WYSIWYG config" class="input" onClick="javascript:window.open('<?php echo $_SERVER['REQUEST_URI'].'&edit='.urlencode( self::FVC_FCK_CONFIG_RELATIVE_PATH ); ?>');">
                        	<?php else : ?>
                        		Foliopress WYSIWYG config file is not writable.
                        	<?php endif; ?>
                            
                        	<span class="description">Edit custom FCK config file to suit your own purposes.</span><br />
                            
                            <h3 style="display: inline;">Be aware that editing this file may cause serious malfunctions in FCK behaviour and other problems.</h3> <br />
                        	<a href="http://docs.fckeditor.net/FCKeditor_2.x/Developers_Guide/Configuration/Configuration_File" target="_blank">Documentation 
                        	of FCK config file</a>.
                        	
                        </td>
                        <td align="right" style="width: 100px; font-size: large;">
                        	
                        </td>
                    </tr>
					<tr>
                        <td><input type="submit" name="recreate" class="button"  value="Recreate thumbnails" /> <span class="description">All thumbnails, even special thumbnails will be recreated!</span></td>
                    </tr>
				</table>
			</div>
		</div>
		<br /><br />
		<p>
			<!--<input type="submit" name="options_reset" class="button" value="Reset Changes" />-->
			<input type="submit" class="button-primary"  
				<?php 
					if( $bError ) print( 'name="options_reload" value="Reload Page"' ); 
					else print( 'name="options_save" value="Save Changes"' );
				?>
			 />
		</p>
	</form> 
</div>
<script type="text/javascript">
	
	KFMLinkLightboxStart( <?php
				$iLink = ($this->aOptions[self::FVC_KFM_LINK]) ? 1 : 0;
				$iLight = ($this->aOptions[self::FVC_KFM_LIGHTBOX]) ? 1 : 0;
				printf( "%d, %d", $iLink, $iLight ); 
	?> );
	
	var bPNGTransform = <?php echo ($this->aOptions[self::FVC_PNG]) ? 'true' : 'false'; ?>;
	KFM_CheckPNG( bPNGTransform );
	
	var aKFMThumbs = new Array();
	<?php for( $i=0; $i<count( $this->aOptions[self::FVC_KFM_THUMBS] ); $i++ ) print( 'aKFMThumbs[' . $i . '] = ' . $this->aOptions[self::FVC_KFM_THUMBS][$i] . ";\n" ); ?>
	KFMThumbsStart( aKFMThumbs );
	
	var aFPCleanPHP = new Array();
	<?php for( $i=0; $i<count( $this->aOptions[self::FVC_FPC_TEXTS] ); $i++ ) print( 'aFPCleanPHP['.$i.'] = "'.$this->aOptions[self::FVC_FPC_TEXTS][$i]."\";\n" ); ?>
	FPCleanTextStart( aFPCleanPHP );
	
	<?php
		if( $strCustomError ) print( 'alert("' . addcslashes( $strCustomError, "\\" ) . '");' );
		elseif( $strErrDesc ) print( 'alert("' . addcslashes( $strErrDesc, "\\" ) . '");' );
		else print( '' );
		if( $this->strError ) print( 'alert("Error while loading options: ' . addcslashes( $this->strError, "\\" ) . '");' );
	?>
</script>