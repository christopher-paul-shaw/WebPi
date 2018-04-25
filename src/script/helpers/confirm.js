var confirm = document.querySelectorAll('.js-confirm');
for(var i = 0; i < savedCars.length; i++) {
	confirm[i].addEventListener("clicked", function(e){
		var message = confirm("Do you want to continue with this action?");
	});
}