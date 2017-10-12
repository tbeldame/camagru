var usr = document.getElementsByClassName('rainbow')[0];
if (usr && typeof usr !== 'undefined')
{
	chars = usr.textContent.split('');
	length = chars.length;
	usr.innerHTML = '';
	step = 360 / length;
	pos = 1;

	for (char of chars)
		usr.innerHTML = usr.innerHTML + '<span>' + char + '</span>';
	spans = usr.children;

	function rainbow()
	{ 
		if (pos > 360 && pos % length === 0)
			pos = 1;
		for (i = 0; i < length; i++)
			spans[i].style.color = 'hsl(' + (pos + (i * step)) + ', 70%, 60%)';
		pos++;
	}
	setInterval(rainbow, 5);
}
