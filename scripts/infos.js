function closeBar(bar)
{
	bar.remove();
}

function displayError(error)
{
	var oldErr = document.getElementsByClassName('error_msg');
	if (typeof oldErr !== 'undefined')
	{
		for (err of oldErr)
			err.remove();
	}
	var oldInf = document.getElementsByClassName('info_msg');
	if (typeof oldInf !== 'undefined')
	{
		for (inf of oldInf)
			inf.remove();
	}
	var errDiv = document.createElement('div');
	errDiv.className = 'error_msg';
	errDiv.innerHTML = '<p class="info_txt">' + error + '</p>' +
						'<div class="close_bar"><i class="material-icons">close</i></div>';
	var global = document.getElementsByClassName('global')[0];
	global.insertBefore(errDiv, document.getElementsByClassName('top')[0]);
	document.getElementsByClassName('close_bar')[0].addEventListener('click', function() {closeBar(errDiv)});
	setTimeout(function() {closeBar(errDiv);}, 5000);
}

function displayInfo(info)
{
	var oldErr = document.getElementsByClassName('error_msg');
	if (typeof oldErr !== 'undefined')
	{
		for (err of oldErr)
			err.remove();
	}
	var oldInf = document.getElementsByClassName('info_msg');
	if (typeof oldInf !== 'undefined')
	{
		for (inf of oldInf)
			inf.remove();
	}
	var infDiv = document.createElement('div');
	infDiv.className = 'info_msg';
	infDiv.innerHTML = '<p class="info_txt">' + info + '</p>' +
						'<div class="close_bar"><i class="material-icons">close</i></div>';
	var global = document.getElementsByClassName('global')[0];
	global.insertBefore(infDiv, document.getElementsByClassName('top')[0]);
	document.getElementsByClassName('close_bar')[0].addEventListener('click', function() {closeBar(infDiv)});
	setTimeout(function() {closeBar(infDiv);}, 5000);
}
