window.kfm_downloadSelectedFiles_addIframe=function(wrapper,id){
	var iframe=document.createElement('iframe');
	iframe.src='getfile.php?id='+id+'&forcedownload=1';
	kfm.addEl(wrapper,iframe);
}
