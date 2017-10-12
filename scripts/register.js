function register()
{
	var formData = new FormData(document.getElementById('reg_form'));
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			grecaptcha.reset();
			var response = JSON.parse(this.responseText);
			if (response['status'] == 'done')
			{
				displayInfo(response['value']);
				for (field of fields)
					field.value = '';
			}
			else if (response['status'] == 'error')
			{
				displayError(response['value']);
			}
		}
	};
	xmlhttp.open("POST","register_script.php" ,true);
//	xmlhttp.setRequestHeader("Content-type", "multipart/form-data");
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xmlhttp.send(formData);
}

var regBtn = document.getElementById('register');
regBtn.addEventListener('click', register);
var fields = document.getElementsByClassName('user_field');
for (field of fields)
	field.addEventListener('keydown', function(event) {
											if (event.code === 'Enter' || event.keyCode === 13)
											{
												event.preventDefault();
												register();
											}
											});
