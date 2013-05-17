
function bootstrapAlert(msg) {
	return $('<div>').addClass('alert alert-error').append(
		$('<button>').addClass('close').attr('type','button').attr('data-dismiss','alert').html('&times;'),
		$('<strong>').text('Error:'),
		$('<span>').text(' '+msg)
	);
}

$('form.config').submit(function() {
	var that=this;
	/*
	* I know the way of `$.ajax({context:this,...})'.
	* But it messes up my code. :(
	*/
	$.post(
		$(this).attr('action'),
		$(this).serializeArray(),
		function(error) {
			if (error!='') {
				$(that).before(bootstrapAlert(error));
			} else {
				location.reload(true);
			}
		}
	);
	return false;
});

