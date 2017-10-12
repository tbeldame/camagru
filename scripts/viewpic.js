function getDelBtns()
{
	var com_del_btns = document.getElementsByClassName('del_com');
	for (i = 0; i < com_del_btns.length; i++)
		com_del_btns[i].addEventListener('click', function() {deleteComment(this);});
}

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
				window.location.href = '/gallery.php';
			else if (response['status'] == 'error')
				displayError(response['value']);
		}
	};
	xmlhttp.open("GET","deletepic.php?id=" + elem.id,true);
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xmlhttp.send();
}

var likeBtn = document.querySelector('[liked]');
	likeBtn.addEventListener('click', function() {likePic(this);});

var del_btns = document.getElementsByClassName('del_btn');
if (del_btns[0])
	del_btns[0].addEventListener('click', function() {deletePic(this);});
getDelBtns();

