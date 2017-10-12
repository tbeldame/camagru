function likePic(pic)
{
	var elem = pic.parentNode;
	var counter = elem.querySelector('[class=like_nb]');
	var val = parseInt(counter.innerHTML);
	var list = elem.querySelector('[name=like_list]');
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var response = JSON.parse(this.responseText);
			if (response['status'] == 'done')
			{
				if (pic.getAttribute("liked") == "0")
				{
					list.innerHTML += '<p>' + logged + '</p>';
					pic.className += " liked";
					pic.setAttribute("liked", "1");
					if (list.className === 'hidden')
						list.className = 'likers';
					counter.innerHTML = val + 1;
				}
				else
				{
					for (liker of list.children)
					{
						if (liker.innerText === logged)
						{
							list.removeChild(liker);
							break;
						}
					}
					if (val === 1)
						list.className = 'hidden';
					pic.className = "like_btn";
					pic.setAttribute("liked", "0");
					counter.innerHTML = parseInt(counter.innerHTML) - 1;
				}
			}
			else if (response['status'] == 'error')
				displayError(response['value']);
		}
	};
	if (pic.getAttribute("liked") == "0")
		xmlhttp.open("GET","likepic.php?id=" + elem.id,true);
	else
		xmlhttp.open("GET","unlikepic.php?id=" + elem.id,true);
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xmlhttp.send();
}
