var page = 1;
var loading = false;
var per_page = 20;

function getDelBtns()
{
	var likeBtns = document.querySelectorAll('[liked]');
	for (likeBtn of likeBtns)
		likeBtn.addEventListener('click', function() {likePic(this);});
}

function getLikeBtns()
{
	var delBtns = document.getElementsByClassName('del_btn');
	for (delBtn of delBtns)
		delBtn.addEventListener('click', function() {deletePic(this);});
}

function getLatestLoaded()
{
	return (document.querySelector('[class="gallery"]').lastElementChild);
}

function isElementInViewport (el)
{
    var rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
        rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
    );
}

function insertAfter(newNode, referenceNode)
{
	referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

function infiniteScroll()
{
	if (isElementInViewport(getLatestLoaded()) && !loading)
	{
		loading = true;
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{
				try
				{
					var response = JSON.parse(this.responseText)
				}
				catch (e)
				{
					displayError('Une erreur est survenue');
					return;
				}
				if (response['status'] === 'done')
				{
					for (entry of response['value'])
					{
						if (entry['likers'])
						{
							var likers = entry['likers'].split(".");
							var like_nb = likers.length;
						}
						else
						{
							var likers = [];
							var like_nb = 0;
						}
						var newEntry = document.createElement("div");
						newEntry.className = 'gallery_entry';
						newEntry.id = entry['id'];

						var thumbContainer = document.createElement('div');
						thumbContainer.className = 'thumbcontainer';
						thumbContainer.innerHTML = '<a href="' + window.location.origin + '/viewpic.php?pic=' + entry['id'] + '">' +
														'<img src="images/pictures/thumb_' + entry['filename'] + '">' +
													'</a>';
						newEntry.appendChild(thumbContainer);
						
						var creator = document.createElement('div');
						creator.className = 'creator_name';
						creator.innerHTML = entry['creator'];
						newEntry.appendChild(creator);

						var cDate = document.createElement('div');
						cDate.className = 'creation_date';
						cDate.title = entry['date'];
						cDate.innerHTML = entry['elapsed'];
						newEntry.appendChild(cDate);
						if (typeof logged !== 'undefined' && logged == entry['creator'])
						{
							var delBtn = document.createElement('div');
							delBtn.className = 'del_btn';
							delBtn.innerHTML = '<i class="material-icons">delete</i>';
							delBtn.addEventListener('click', function() {deletePic(this);});
							newEntry.appendChild(delBtn);
						}

						var likeBtn = document.createElement('div');
						if (typeof logged !== 'undefined' && likers.indexOf(logged) != -1)
						{
							likeBtn.className = 'like_btn liked';
							likeBtn.setAttribute('liked', '1');
						}
						else
						{
							likeBtn.className = 'like_btn';
							likeBtn.setAttribute('liked', '0');
						}
						likeBtn.innerHTML =  '<i class="material-icons">thumb_up</i>';
						likeBtn.addEventListener('click', function() {likePic(this);});
						newEntry.appendChild(likeBtn);

						var likeCountDiv = document.createElement('div');
						likeCountDiv.className = 'like_counter';
						likeCountDiv.setAttribute('value',  like_nb);
						likeCountDiv.setAttribute('name', 'likecount');
						newEntry.appendChild(likeCountDiv);
						
						var likeNbDiv = document.createElement('div');
						likeNbDiv.className = 'like_nb'
						likeNbDiv.innerHTML = like_nb;
						likeCountDiv.appendChild(likeNbDiv);
						var likersDiv = document.createElement('div');
						likersDiv.setAttribute('name', 'like_list');
						if (likers.length > 0)
						{
							likersDiv.className = 'likers';
							for (liker of likers)
								likersDiv.innerHTML = likersDiv.innerHTML + '<p>' + liker + '</p>';
						}
						else
							likersDiv.className = 'hidden';
						likeCountDiv.appendChild(likersDiv);

						var comIcnDiv = document.createElement('div');
						comIcnDiv.className = 'comment_icon';
						comIcnDiv.innerHTML = '<i class="material-icons">comment</i>';
						newEntry.appendChild(comIcnDiv);

						var comCount = document.createElement('div');
						comCount.className = 'comment_counter';
						comCount.innerHTML = entry['com_nb'];
						newEntry.appendChild(comCount);

						insertAfter(newEntry, getLatestLoaded());
					}
					page++;
					if (response['value'].length < per_page)
					{
						window.removeEventListener('scroll', infiniteScroll);
						window.removeEventListener('load', infiniteScroll);
						window.removeEventListener('resize', infiniteScroll);
						window.removeEventListener('DOMContentLoaded', infiniteScroll);
					}
					loading = false;
				}
				else if (response['status'] === 'error')
					displayError(response['value']);
			}
		};
		xmlhttp.open("POST","get_elements.php" ,true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xmlhttp.send('page=' + page + '&nb=' + per_page);
	}
}

window.addEventListener('scroll', infiniteScroll);
window.addEventListener('load', infiniteScroll);
window.addEventListener('resize', infiniteScroll);
window.addEventListener('DOMContentLoaded', infiniteScroll);
getDelBtns();
getLikeBtns();
