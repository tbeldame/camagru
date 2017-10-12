function insertAfter(newNode, referenceNode)
{
	referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

function updateRatio()
{
	ratio = parseFloat(overlayImg.naturalWidth) / parseFloat(overlayImg.width);
}

var overlayImg;
var overlayContainer = document.getElementsByClassName("overlay_container")[0];
var uploadBtn = document.getElementById("file");
var overlays = document.getElementsByClassName('overlay_thumb');
var mirToggle;
var mirLabel;
var webcamStream;
var selectedOverlay = '';
overlaySelected = false;
loaded = false;
angle = 0;
var streaming = false,
video		= document.querySelector('#video'),
canvas		= document.createElement('canvas');
create		= document.querySelector('#create_btn'),
width = 500,
height = 0;

function streamStart(stream)
{
	webcamStream = stream;
	if (navigator.mozGetUserMedia) {
		video.mozSrcObject = stream;
	} else {
		var vendorURL = window.URL || window.webkitURL;
		video.src = vendorURL.createObjectURL(stream);
	}
	video.play();
	mirToggle = document.createElement('input');
	mirToggle.type = 'checkbox';
	mirToggle.id = 'mirror_chk';
	mirToggle.value = 'false';
	mirToggle.addEventListener('click', function() {toggleMirror(this);});
	insertAfter(mirToggle, document.getElementById('visual_preview'));
	mirLabel = document.createElement('label');
	mirLabel.htmlFor = 'mirror_chk';
	mirLabel.className = 'mirror_label';
	mirLabel.title = 'Mirroir';
	mirLabel.innerHTML = '<i class="material-icons md-30">swap_horiz</i>';
	insertAfter(mirLabel, mirToggle);
}

function streamFailed(err)
{
	video.remove();
	video = null;
	var cantPlay = document.createElement('div');
	cantPlay.className = 'webcam_error';
	cantPlay.innerHTML = '<i class="material-icons md-48">videocam_off</i>';
	document.getElementById("visual_preview").insertBefore(cantPlay, overlayContainer);
}

if (navigator.mediaDevices)
{
	navigator.mediaDevices.getUserMedia(
			{
				audio: false,
				video: true
			}).then(streamStart).catch(streamFailed);
}
else
{
	navigator.getMedia = ( navigator.getUserMedia ||
				navigator.webkitGetUserMedia ||
				navigator.mozGetUserMedia ||
				navigator.msGetUserMedia);

	navigator.getMedia(
			{
				video: true,
				audio: false
			}, streamStart, streamFailed);
}

video.addEventListener('canplay', function(ev){
		if (!streaming)
		{
			height = video.videoHeight / (video.videoWidth/width);
			video.setAttribute('width', width);
			video.setAttribute('height', height);
			canvas.setAttribute('width', width);
			canvas.setAttribute('height', height);
			streaming = true;
		}
	}, false);

function takepicture()
{
	if (selectedOverlay === '')
	{
		displayError('Aucun element sperposable choisi');
		return;
	}
	if (!loaded)
	{
		displayError('Element non charge');
		return;
	}
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var response = JSON.parse(this.responseText);
			if (response['status'] == 'done')
			{
					var newPic = document.createElement('div');
				newPic.className = 'prev_pic';
				newPic.innerHTML = '<a href="viewpic.php?pic=' + response['value']['id'] + '">' +
									'<img src="images/pictures/thumb_' + response['value']['filename'] + '"/>' +
									'</a>';
				var side = document.getElementsByClassName('side')[0];
				side.insertBefore(newPic, side.firstChild);
				displayInfo('Montage cree');
			}
			if (response['status'] == 'error')
			{
				displayError(response['value']);
				return;
			}
		}
	}
	xmlhttp.open("POST","process_image.php" ,true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	var context = canvas.getContext('2d');
	var uplPic = document.getElementById("uploaded_pic");
	if (uplPic !== null)
	{
		canvas.width = uplPic.width;
		canvas.height = uplPic.height;
		context.drawImage(uplPic, 0, 0);
		var imgData = canvas.toDataURL('image/png');
		var x = ratio * parseInt(overlayImg.style.left);
		var y = ratio * parseInt(overlayImg.style.top);
		var w = ratio * parseInt(overlayImg.width);
		var h = ratio * parseInt(overlayImg.height);
		xmlhttp.send('imgpath=' + uplPic.src + '&overlay=' + selectedOverlay + '&x=' + x + '&y=' + y + '&w=' + w + '&h=' + h + '&angle=' + angle);
	}
	else if (streaming)
	{
		canvas.width = width;
		canvas.height = height;
		if (mirToggle.value == 'true')
		{
			context.translate(canvas.width, 0);
			context.scale(-1, 1);
		}
		context.drawImage(video, 0, 0, width, height);
		var x = ratio * parseInt(overlayImg.style.left);
		var y = ratio * parseInt(overlayImg.style.top);
		var w = ratio * parseInt(overlayImg.width);
		var h = ratio * parseInt(overlayImg.height);
		var imgData = canvas.toDataURL('image/png');
		xmlhttp.send("imgdata=" + imgData + "&overlay=" + selectedOverlay + '&x=' + x + '&y=' + y + '&w=' + w + '&h=' + h + '&angle=' + angle);
	}
	else
	{
		displayError('Pas de webcam ou d\'image envoyee');
	}
}
	create.addEventListener('click', function(ev){
	takepicture();
	ev.preventDefault();
}, false);

function rotateOv(change)
{
	overlayImg.style.transform = 'rotate(' + (angle + change) + 'deg)';
	angle += change;
}

function processKey(e)
{
	if (e.key === '-' || e.key === '_')
	{
		var oldW = parseFloat(overlayImg.width);
		var oldH = parseFloat(overlayImg.height);
		var w = parseFloat(overlayImg.style.width);
		overlayImg.style.width = w - (0.03 * w)  + '%';
		overlayImg.style.left = (parseFloat(overlayImg.style.left) + (oldW - parseFloat(overlayImg.width)) / 2) + 'px';
		overlayImg.style.top = (parseFloat(overlayImg.style.top) + (oldH - parseFloat(overlayImg.height)) / 2) + 'px';
	}
	else if (e.key === '+' || e.key === '=')
	{
		var oldW = parseFloat(overlayImg.width);
		var oldH = parseFloat(overlayImg.height);
		var w = parseFloat(overlayImg.style.width);
		overlayImg.style.width = w + (0.03 * w)  + '%';
		overlayImg.style.left = (parseFloat(overlayImg.style.left) + (oldW - parseFloat(overlayImg.width)) / 2) + 'px';
		overlayImg.style.top = (parseFloat(overlayImg.style.top) + (oldH - parseFloat(overlayImg.height)) / 2) + 'px';
	}
	else if (e.key === '[')
		rotateOv(-2);
	else if (e.key === ']')
		rotateOv(2);
}

function selectOverlay(ovrl)
{
	if (ovrl.getAttribute('sel') == '0')
	{
		ovrl.setAttribute('sel', '1');
		selectedOverlay = ovrl.id;
		ovrl.className = ovrl.className + ' selected';
		for (overlay of overlays)
		{
				if (ovrl != overlay && overlay.getAttribute('sel') == '1')
				{
					overlay.setAttribute('sel', '0');
					overlay.className = 'overlay_thumb';
				}
		}
	}
	if (!overlaySelected)
	{
		overlayImg = document.createElement('img');
		overlayImg.id = "overlay_preview";
		overlayImg.setAttribute('draggable', 'false');
	}
	loaded = false;
	overlayImg.onload = function () {updateRatio(); loaded = true;};
	var img = "images/overlays/overlay" + ovrl.id + ".png";
	overlayImg.style.opacity = '0';
	overlayImg.style.left = '0px';
	overlayImg.style.top = '0px';
	overlayImg.style.width = '100%';
	overlayImg.style.transform = 'rotate(0deg)';
	angle = 0;
	overlayImg.src = img;
	overlayImg.style.opacity = '1';
	if (!overlaySelected)
	{
		overlayContainer.appendChild(overlayImg);
		overlayImg.addEventListener('touchstart', function(e) {startDrag(e);});
		overlayImg.addEventListener('touchend', endDrag);
		overlayImg.addEventListener('touchmove', function(e) {moveOverlay(e);});
		overlayImg.addEventListener('mousedown', function(e) {e.preventDefault(); startDrag(e);});
		window.addEventListener('mouseup', endDrag);
		overlayImg.addEventListener('mousemove', function(e) {moveOverlay(e);});
		window.addEventListener('keypress', function(e) {processKey(e);});
		window.addEventListener('resize', updateRatio);
		overlaySelected = true;
		create.className = '';
	}
}

function toggleMirror(mirToggle)
{
	if (mirToggle.value == 'false')
	{
		mirToggle.value = 'true';
		video.className += ' mirror';
	}
	else
	{
		mirToggle.value = 'false';
		video.className = '';
	}
}

function uploadImg()
{
	var fileInput = document.getElementById("file");
	var file = fileInput.files[0];
	if (typeof file === 'undefined')
		return;
	if (file.size > 5242880)
	{
		displayError("Fichier trop volumineux (Maximum 5Mo)");
		return;
	}
	else if (file.type && "image/gif" &&
			file.type != "image/jpeg" &&
			file.type != "image/png")
	{
		displayError("Merci de choisir une image au format jpeg, png ou gif");
		return;
	}
	var formData = new FormData();
	formData.append("userfile", file);
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open('POST', 'upload_file.php');
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			response = JSON.parse(this.responseText);
			if (response['status'] == 'done')
			{
				var uplPic = document.getElementById('uploaded_pic'); 
				if (uplPic)
				{
					uplPic.src = 'images/tmp/' + response['value'];
				}
				else
				{
					uplPic = document.createElement("img");
					uplPic.id = "uploaded_pic";
					uplPic.src = 'images/tmp/' + response['value'];
					document.getElementById("visual_preview").insertBefore(uplPic, overlayContainer);
				}
				if (video && typeof video !== 'undefined')
				{
					webcamStream.getTracks()[0].stop();
					video.remove();
					video = 0;
					mirToggle.remove();
					mirLabel.remove();
				}
			}
			else if(response['status'] == 'error')
				displayError(response['value']);
		}
	};
	xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xmlhttp.send(formData);
}

var xOffset;
var yOffset;
var startX;
var startY;

function startDrag(e)
{
	if (e.type === 'touchstart')
	{
		startX = e.touches[0].pageX;
		startY = e.touches[0].pageY;
	}
	else
	{
		startX = e.pageX;
		startY = e.pageY;
	}
	overlayDrag = true;
	offX = startX - overlayImg.offsetLeft;
	offY = startY - overlayImg.offsetTop;
}

function endDrag()
{
	overlayDrag = false;
}

function moveOverlay(e)
{
	if (overlayDrag)
	{
		if (e.type === 'touchmove')
		{
			var pX = e.touches[0].pageX;
			var pY = e.touches[0].pageY;
			e.preventDefault();		
		}
		else
		{
			var pX = e.pageX;
			var pY = e.pageY;
		}
		overlayImg.style.left = (pX - offX) + 'px';
		overlayImg.style.top = (pY - offY) + 'px';
	}
}

var overlayDrag = false;
uploadBtn.addEventListener('change', uploadImg);
for (overlay of overlays)
	overlay.addEventListener('click', function() {selectOverlay(this);});
