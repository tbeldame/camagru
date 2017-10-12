function login()
{
	var formData = new FormData(document.getElementById('log_form'));
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var response = JSON.parse(this.responseText);
			if (response['status'] == 'done')
			{
				window.location.href = '/index.php';
			}
			else if (response['status'] == 'error')
				displayError(response['value']);
		}
	};
	xmlhttp.open("POST","login_script.php" ,true);
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xmlhttp.send(formData);
}

var logBtn = document.getElementById('login');
logBtn.addEventListener('click', login);
var fields = document.getElementsByClassName('user_field');
for (field of fields)
	field.addEventListener('keydown', function(event) {
											if (event.code === 'Enter' || event.keyCode === 13)
											{
												event.preventDefault();
												login();
											}
											});
