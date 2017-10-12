function forgot()
{
	var formData = new FormData(document.getElementById('forgot_form'));
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var response = JSON.parse(this.responseText);
			if (response['status'] == 'done')
			{
				displayInfo(response['value']);
				for (field of fields)
					field.value = '';
			}
			else if (response['status'] == 'error')
				displayError(response['value']);
		}
	};
	xmlhttp.open("POST","forgot_script.php" ,true);
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xmlhttp.send(formData);
}

var forgotBtn = document.getElementById('forgot');
forgotBtn.addEventListener('click', forgot);
var fields = document.getElementsByClassName('user_field');
for (field of fields)
	field.addEventListener('keydown', function(event) {
											if (event.code === 'Enter' || event.keyCode === 13)
											{
												event.preventDefault();
												forgot();
											}
											});
