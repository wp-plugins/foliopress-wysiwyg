<?php

define( 'MY_LOG_FILE', 'debug.txt' );

function my_log( $strText, $strFile = MY_LOG_FILE ){
	$file = fopen( $_SERVER['DOCUMENT_ROOT']."/".$strFile , 'a' );
	if( $file == false ) return false;
	$ret = fwrite( $file, $strText );
	fclose( $file );
	return ( $ret ? true : false );
}

function mylog_array( $aArray, $strPrefix = '', $strFile = MY_LOG_FILE ){
	if( !is_array( $aArray ) ){
		my_log( "Variable is not an array\n" );
		return false;
	}
	
	if( strlen( $strPrefix ) > 0 ) my_log( $strPrefix, $strFile );
	my_log( "Array:\t", $strFile );
	foreach( $aArray as $objItem ){
		if( is_array( $objItem ) ){
			mylog_array( $objItem, $strPrefix."\t", $strFile );
		}else{
			my_log( "$objItem\t", $strFile );
		}
	}
	my_log( "\n", $strFile );
	
	return true;
}

?>
