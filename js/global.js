function $(x) {
	if (x.charAt(0) == '#') return document.getElementById(x.substr(1));
	else if (x.charAt(0) == '.') return document.getElementsByClassName(x.substr(1));
	else return document.getElementsByTagName(x);
};
