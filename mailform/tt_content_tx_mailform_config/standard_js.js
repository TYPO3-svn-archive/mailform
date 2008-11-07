function changeVisibility(elementId) {
	if(document.getElementById(elementId).style.display == "none") {
		document.getElementById(elementId).style.display = "block";
	} else {
		document.getElementById(elementId).style.display = "none";
	}
}