reg = document.getElementById('reg');
log = document.getElementById('log');
if (reg && typeof reg !== 'undefined')
	reg.addEventListener('click', function() {window.location.href = "register.php";});
if (log && typeof log !== 'undefined')
	log.addEventListener('click', function() {window.location.href = "login.php";});
