window.kfm_modal_close=function(msg){
	var shader=document.getElementById('shader');
	shader.parentNode.removeChild(shader);
	var formWrapper=document.getElementById('formWrapper');
	formWrapper.parentNode.removeChild(formWrapper);
	if(msg)alert(msg);
}
