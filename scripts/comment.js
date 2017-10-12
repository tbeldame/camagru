function getDateTime() {
	var now     = new Date(); 
	var year    = now.getFullYear();
	var month   = now.getMonth() + 1; 
	var day     = now.getDate();
	var hour    = now.getHours();
	var minute  = now.getMinutes();
	if(month.toString().length == 1)
		var month = '0' + month;
	if(day.toString().length == 1)
		var day = '0' + day;
	if(hour.toString().length == 1)
		var hour = '0' + hour;
	if(minute.toString().length == 1)
		var minute = '0' + minute;
	var dateTime = day + '-' + month + '-' + year + ' a '+hour+':'+minute;
	return dateTime;
}

function escapeHTML(str)
{
	return str.replace(/&/g, '&amp;')
		.replace(/"/g, '&quot;')
		.replace(/'/g, '&#39;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;');
}

function adjustTextArea(txt)
{
	txt.style.height = "1px";
	txt.style.height = (18 + txt.scrollHeight) + "px";
}


function postComment(comment)
{
	if (comment.value.length > 2000)
	{
		return;
	}
	var elem = comment.parentNode;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var response = JSON.parse(this.responseText);
			if (response['status'] == 'done')
			{
				var currentdate = new Date();
				var datetime = currentdate.getDate() + "/"
					+ (currentdate.getMonth()+1)  + "/"
					+ currentdate.getFullYear() + " "
					+ currentdate.getHours() + ":"  
					+ currentdate.getMinutes() + ":";
				var newCom = document.createElement("div");
				newCom.className = "comment";
				newCom.setAttribute("comid", response['value']);
				var content = "<div class=\"com_author\">" + logged + "</div>"
							+ "<div class=\"com_date\" title=\"" + datetime + "\">A l'instant</div>"
							+ "<div class=\"com_txt\">" + escapeHTML(comment.value) + "</div>"
							+ "<div class=\"del_com\"><i class=\"material-icons\">delete</i><\div>";
				newCom.innerHTML = content;
				var comDiv = document.getElementById("comments");
			    comDiv.insertBefore(newCom, comDiv.firstChild);
				comment.value = "";
				adjustTextArea(comTxtArea);
				commentLength(comment);
				newCom.getElementsByClassName('del_com')[0].addEventListener('click', function() {deleteComment(this);});
			}
			else if (response['status'] == 'error')
				displayError(response['value']);
		}
	};
	xmlhttp.open("POST","post_comment.php" ,true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xmlhttp.send("comment=" + comment.value + "&id=" + id);
}

function deleteComment(comment)
{
	var elem = comment.parentNode;
	var id = elem.getAttribute("comid");
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
	xmlhttp.open("GET","deletecomment.php?id=" + id,true);
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xmlhttp.send();
}

function commentLength(text)
{
	var rem = 2000 - parseInt(text.value.length);
	remChars.innerHTML = rem;
	if (rem < 0)
		remChars.className = remChars.className + ' too_long';
	else if (remChars.className !== 'com_length')
		remChars.className = 'com_length';
}

var post_btn = document.getElementById('post_comment');
var comment_text = document.getElementById('comment_box');
var remChars = document.getElementById("rem_chars");
if (post_btn)
	post_btn.addEventListener('click', function() {postComment(comment_text);});
if (comment_text)
{
	comment_text.addEventListener('input', function() {commentLength(this);});
	comment_text.addEventListener('focusin', function() {remChars.style.opacity = 1;});
	comment_text.addEventListener('focusout', function() {remChars.style.opacity = 0;});
}
var comTxtArea = document.getElementById('comment_box');
if (comTxtArea)
{
	comTxtArea.addEventListener('input', function() {adjustTextArea(this);});
	comTxtArea.addEventListener('keydown', function(event) {
											if (event.code === 'Enter' || event.keyCode === 13)
											{
												event.preventDefault();
												postComment(comment_text);
											}
											});
}
