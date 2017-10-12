function toggleNotification(s)
{
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var response = JSON.parse(this.responseText);
			if (response['status'] == 'done')
			{
				s.checked = !s.checked;
			}
			else if (response['status'] == 'error')
				displayError(response['value']);
		}
	};
	xmlhttp.open("POST","notification_set.php" ,true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xmlhttp.send('type=' + s.id);
}

function deleteAccount()
{
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var response = JSON.parse(this.responseText);
			if (response['status'] == 'done')
			{
				window.location.href = 'index.php';
			}
			else if (response['status'] == 'error')
				displayError(response['value']);
		}
	};
	xmlhttp.open("POST","deleteaccount.php" ,true);
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xmlhttp.send();
}

function updateColor(send)
{
	rgxpr = new RegExp('#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3}|rnbw)$');
	if (rgxpr.test(colorInput.value))
	{
		if (send)
		{
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function()
			{
				if (this.readyState == 4 && this.status == 200)
				{
					var response = JSON.parse(this.responseText);
					if (response['status'] == 'done')
						displayInfo(response['value']);
					else if (response['status'] == 'error')
					{
						displayError(response['value']);
						return;
					}
				}
			};
			xmlhttp.open("POST","accountcolor.php" ,true);
			xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send('color=' + colorInput.value);
		}
		if (colorInput.value !== '#rnbw')
		{
			colorPrev.style.backgroundColor = colorInput.value;
			colorPrev.className = 'color_preview';
		}
		else
			colorPrev.className = colorPrev.className + ' rainbow_back';
	}
//	else
//		colorPrev.style.backgroundColor = '#FFF';
}

var switches = document.getElementsByClassName('switch_input');
for (s of switches)
	s.addEventListener('click', function(e) {
												toggleNotification(this);
												e.preventDefault();
											});
var delAccount = document.getElementById('del_account');
delAccount.addEventListener('click', deleteAccount);
colorPrev = document.getElementsByClassName('color_preview')[0];
colorInput = document.getElementsByClassName('color_input')[0];
colorInput.addEventListener('input', function() {updateColor(true);});
updateColor(false);
