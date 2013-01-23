<script type="text/javascript" src="<?php print( $this->strPluginPath . self::FVC_OPTIONS_JS_PATH ); ?>"></script>
<script type="text/javascript" src="<?php print( $this->strPluginPath . self::FVC_FV_REGEX_PATH ); ?>"></script>

<?php if( $this->checkImageMagick() ) : ?>
<div class="updated fade">ImageMagick <strong>detected</strong>! Foliopress WYSIWYG will use it to provide superior image quality!</div>
<?php endif; ?>

<?php if( isset( $strMessage ) ) : ?>
<div class="wrap">
	<?php echo $strMessage; ?>
</div>
<?php endif; ?>

<div class="wrap">
    <div id="icon-tools" class="icon32"><br /></div>
	<form id="main_form" method="post" action="<?php print( $_SERVER["REQUEST_URI"] );?>">
		<h2><?php print( FV_FCK_NAME ); ?></h2>
		
		<?php
		/*if (!function_exists('curl_init')) { 
        echo('<div class="updated"><p>cURL is not installed, can\'t perform self-checking mechanism!</p></div>');
    }
    else {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->strPluginPath . self::FVC_FCK_CONFIG_RELATIVE_PATH);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      $output = curl_exec($ch);
      curl_close($ch);
      
      $error = 0;
      if( stripos( $output, '200 OK' ) === FALSE ) {
        $error = 1;
      }
      else if ( stripos( $output, 'skins/'.$fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_SKIN] ) === FALSE ) {
        $error = 2;
      }
      
      if( $error == 1 ) {
        echo '<div class="error"><p>There is a problem opening your <a href="'.$this->strPluginPath . self::FVC_FCK_CONFIG_RELATIVE_PATH.'">configuration file</a>. Please check if <code>'.$this->strPluginPath . self::FVC_FCK_CONFIG_RELATIVE_PATH.'</code> is executed in PHP correctly.</p></div>';
      }
      else if( $error == 2 ) {
        echo '<div class="error"><p>Looks like your <a href="'.$this->strPluginPath . self::FVC_FCK_CONFIG_RELATIVE_PATH.'">configuration file</a> is not able to reach your Wordpress installation.</p></div>';
      }
    }*/
    ?>
		
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
						$aFCKSkins = fp_wysiwyg_load_fck_items( realpath( $strPath . self::FVC_SKINS_RELATIVE_PATH ) );
						foreach( $aFCKSkins AS $key => $value ) {
						  if( $value == '_fckviewstrips.html' ) {
						    unset( $aFCKSkins[$key] );
						  }
						}
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
                <th scope="row"><label for="HideMediaButtons">Enable Wordpress uploader buttons</label></th>
                <td><input id="chkHideMediaButtons" type="checkbox" name="HideMediaButtons" value="checkbox" <?php if($this->aOptions[self::FVC_HIDEMEDIA] == false) echo 'checked="checked"'; ?> /></td>
			</tr>
			<tr valign="top"> 
                <th scope="row"><label for="HideMediaButtons">Multiple image posting</label></th>
                <td><input id="chkMultipleImagePosting" type="checkbox" name="MultipleImagePosting" value="checkbox" <?php if($this->aOptions['multipleimageposting']) echo 'checked="checked"'; ?> /><span class="description">Disable if you want image management window to close automatically after posting a single image.</span></td>
			</tr>
			<tr>
				<th scope="row">Max Image Size</th>
				<td>
					<label for="MaxWidth">Width <input type="text" name="MaxWidth" value="<?php echo $this->aOptions[self::FVC_MAXW]; ?>" class="small-text" /></label>
					<label for="MaxHeight">Height <input type="text" name="MaxHeight" value="<?php echo $this->aOptions[self::FVC_MAXH]; ?>" class="small-text" /></label>
					<span class="description">All images with one of dimensions above one of these limits will be sized down when uploading.</span>
				</td>
			</tr>
			<tr valign="top"> 
                <th scope="row"><label for="listKFMThumbs">Thumbnail sizes</label></th>
				<td>
                    <select id="listKFMThumbs" style="width: 100px;"></select>
                        <input type="text" style="width: 80px;" name="AddThumbSize" id="txtAddThumbSize" value="Add new" onclick="if( this.value == 'Add new') this.value=''" />
                        <input type="button" class="button" value="Add" onclick="KFMAddThumbnail()" />
                        <input type="hidden" value="0" name="KFMThumbCount" id="hidThumbCount" />
                    <input type="button" class="button" value="Remove selected" onclick="KFMRemoveThumbnail()" />
                    <br />
				</td>
			</tr>
			<tr valign="top"> 
        <th scope="row">Supported postmeta</th>
        <td><fieldset>
          <label for="postmeta"><input id="postmeta" type="text" name="postmeta" value="<?php echo $this->aOptions['postmeta']; ?>" class="regular-text" /></label>
          <br />
          <span class="description">Comma separated list of keys for postmeta values you want to fill in from SEO Images.</span>
          </fieldset>
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
            
            <tr valign="top"> 
                <th scope="row">Custom WYSIWYG CSS</th>
                <td>
                        <textarea id="wysiwygstyles" type="text" name="wysiwygstyles" value="<?php echo $this->aOptions['wysiwygstyles']; ?>" class="regular-text" rows="8" cols="70" /><?php echo $this->aOptions['wysiwygstyles']; ?></textarea><br />
                        <span class="description">If you use WYSIWYG CSS styles above, you can fix whatever you want here for the editor window.</span>
                </td>
            </tr>
		</table>
		<br />

		<p><input type="button" name="advanced_options" class="button" value="Advanced Options" onclick="jQuery('#divAdvanced').toggle()" /></p>
		<div id="divAdvanced" style="display: none; width: 100%">
			<h3>Advanced Options</h3>
			<table class="form-table">
    			<tr valign="top"> 
                    <th scope="row"><label for="FCKSkins">FCKEditor language</label></th>
    				<td><?php
    					print( '<select name="FCKLang"><option value="auto">Autodetect</option>' );
    
    					try{
    						$aFCKLang = fp_wysiwyg_load_fck_items( realpath( $strPath . self::FVC_LANG_RELATIVE_PATH ) );
    						foreach( $aFCKLang AS $key => $value ) {
    						  if( stripos( $value, '.js' ) === FALSE ) {
    						    unset( $aFCKLang[$key] );
    						  }
    						  else {
    						    $aFCKLang[$key] = str_replace( '.js', '', $aFCKLang[$key] );
    						  }
    						}
    						fp_wysiwyg_output_options( $aFCKLang, $this->aOptions[self::FVC_LANG] );
    						
    						print( '</select>' );
    						
    					}catch( Exception $ex ){
    						$bError = true;
    						print( '</select>' );
    						print( ' ERROR: ' . $ex->getMessage() );
    					}
                    ?><select name="FCKLangDir"><option value="ltr">Left to right</option><option value="rtl" <?php if( $this->aOptions['FCKLangDir'] == 'rtl' ) echo 'selected'; ?>>Right to left</option></select></td>
    			</tr>
    			<tr valign="top"> 
                    <th scope="row"><label for="FCKSkins">SEO Images Language</label></th>
    				<td><?php
    					print( '<select name="kfmlang"><option value="auto">Default</option>' );
    
    					try{
    						$aKfmLang = fp_wysiwyg_load_fck_items( realpath( $strPath . self::KFM_LANG_RELATIVE_PATH ) );
    						foreach( $aKfmLang AS $key => $value ) {
    						  if( stripos( $value, '.js' ) === FALSE ) {
    						    unset( $aKfmLang[$key] );
    						  }
    						  else {
    						    $aKfmLang[$key] = str_replace( '.js', '', $aKfmLang[$key] );
    						  }
    						}
    						fp_wysiwyg_output_options( $aKfmLang, $this->aOptions['kfmlang'] );
    						
    						print( '</select>' );
    						
    					}catch( Exception $ex ){
    						$bError = true;
    						print( '</select>' );
    						print( ' ERROR: ' . $ex->getMessage() );
    					}
                    ?></td>
    			</tr>
          <tr valign="top">
              <th scope="row">Permissions</th>
              <td><input type="button" class="button" value="Default settings" onclick="FVWYSIWYGPermisssionsDefault()" /> <input type="button" class="button" value="My server runs in FastCGI or LiteSpeed" onclick="FVWYSIWYGPermisssionsUser()" />&nbsp;&nbsp;<label for="dirperm">Directories <input type="text" id="dirperm" name="dirperm" value="<?php echo $this->aOptions['dirperm']; ?>" class="small-text" /></label> <label for="fileperm">Files <input type="text" id="fileperm" name="fileperm" value="<?php echo $this->aOptions['fileperm']; ?>" class="small-text" /></label><br /><span class="description">We strongly recommend you to test your new settings by creating a directory, uploading some image into it and inserting it into post.</span></td>
          </tr>
					<tr valign="top">
              <th scope="row">Use Flash Uploader</th>
              <td><fieldset>
                  <label for="UseFlashUploader"><input id="chkUseFlashUploader" type="checkbox" name="UseFlashUploader" value="yes" onclick="KFMLink_change()" <?php if($this->aOptions[self::FVC_USE_FLASH_UPLOADER]) echo 'checked="checked"'; ?> /> Flash uploader will enable you to upload multiple images at once. You might want to disable it for better compatibility.</label>
              </fieldset></td>
          </tr>
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
                            <input type="text" name="AddSpecialText" id="txtAddFPClean" class="regular-text" value="Add new" onclick="if( this.value == 'Add new') this.value=''" />
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
                    <th scope="row">Default directory</th>
                    <td><fieldset>
                        <label for="DIRset"><input id="chkDIRset" type="checkbox" name="DIRset" value="yes" onclick="KFM_CheckDIR( !bDIRset );"<?php if( $this->aOptions[self::FVC_DIR] ) echo ' checked="checked"'; ?> /> Open the Year/Month directory as default.</label><br />
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
                <tr valign="top">
                    <th scope="row"></th>
                    <td><fieldset>
                        <label for="convertcaptions"><input id="chkConvertCaptions" type="checkbox" name="convertcaptions" value="yes" <?php if($this->aOptions['convertcaptions']) echo 'checked="checked"'; ?> /> Convert <code>[caption]</code> shortcodes before editing.</label><br /><span class="description">The captions will be converted to our standard image formating: <code>&lt;h5&gt;&lt;a&gt;&lt;img /&gt;&lt;/a&gt;&lt;br /&gt;{caption}&lt;/h5&gt;</code></span><br />
                    </fieldset></td>
                </tr>
                <tr valign="top">
                    <th scope="row"></th>
                    <td><fieldset>
                        <label for="ProcessHTMLEntities"><input id="chkProcessHTMLEntities" type="checkbox" name="ProcessHTMLEntities" value="yes" <?php if($this->aOptions['ProcessHTMLEntities']) echo 'checked="checked"'; ?> /> Process HTML Entities</label><br /><span class="description">If you are using UTF-8, you should leave this option disabled. If you use foreign languages and your website is not UTF-8 (it should be), then you will want to enable this option.</span><br />
                    </fieldset></td>
                </tr>
                <!--<tr valign="top">
                    <th scope="row"></th>
                    <td><fieldset>
                        <label for="UseWPLinkDialog"><input id="chkUseWPLinkDialog" type="checkbox" name="UseWPLinkDialog" value="yes" <?php if($this->aOptions['UseWPLinkDialog']) echo 'checked="checked"'; ?> /> Use Wordpress Linking Dialog</label><br /><span class="description">New feature of Wordpress 3.1. Allows you to select a post and insert a link to it.</span><br />
                    </fieldset></td>
                </tr>-->              
          				<tr valign="top"> 
                    <th scope="row">KFM Thumbnail size</th>
                    <td><label for="KFMThumbnailSize"><input type="text" name="KFMThumbnailSize" value="<?php echo $this->aOptions[self::FVC_KFM_THUMB_SIZE]; ?>" class="small-text" /> px</label><br /><span class="description">Size of the thumnails in the image uploader.</span></td>
                </tr>
          			<tr valign="top"> 
                  <th scope="row">Image Insert HTML</th>
                  <td><fieldset>
                    <label for="<?php echo self::FV_SEO_IMAGES_IMAGE_TEMPLATE; ?>"><input size="50" id="<?php echo self::FV_SEO_IMAGES_IMAGE_TEMPLATE; ?>" type="text" name="<?php echo self::FV_SEO_IMAGES_IMAGE_TEMPLATE; ?>" value='<?php echo stripslashes( $this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] ); ?>' class="regular-text" /></label>
                    <br />
                    <span class="description">This will be used in JavaScript when inserting images. Use \" to escape " in HTML. Leave empty for defaults!</span>
                    </fieldset>
                  </td>
                </tr>		                
                      
			</table>
			<br />
			<!--<p><input type="button" name="expert_options" class="button" value="Expert Options" onclick="jQuery('#divExpert').toggle()" /></p>
			<div id="divExpert" style="display: none">
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
			</div>-->
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
	var bDIRset = <?php echo ($this->aOptions[self::FVC_DIR]) ? 'true' : 'false'; ?>;
	KFM_CheckDIR( bDIRset );
	
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
		if( isset( $this->strError ) ) print( 'alert("Error while loading options: ' . addcslashes( $this->strError, "\\" ) . '");' );
	?>
</script>