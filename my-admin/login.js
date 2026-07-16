document.addEventListener("DOMContentLoaded", () => {

const login = document.querySelector("#login");
	const footer = document.querySelector(".login-footer");

	if (login && footer) {
		login.appendChild(footer);
	}

});