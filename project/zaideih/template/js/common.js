$(function(event) {
	$('form[name="search"] input[name="search"]').on("click", function(){
		this.form.submit();
	});
});