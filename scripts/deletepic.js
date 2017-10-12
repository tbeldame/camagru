function deletePic(pic)
{
	var elem = pic.parentNode;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var response = JSON.parse(this.responseText);
			if (response['status'] == 'done')
				elem.parentNode.removeChild(elem);
			else if (response['status'] == 'error')
				displayError(response['value']);
		}
	};
	xmlhttp.open("GET","deletepic.php?id=" + elem.id,true);
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xmlhttp.send();
}
