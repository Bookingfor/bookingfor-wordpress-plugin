window.bfirecaptcha ={};
window.BFIInitReCaptcha2 = function() {
    "use strict";
	var e = document.getElementsByClassName("bfi-recaptcha"),
        t, n;
    for (var r = 0, i = e.length; r < i; r++){ t = e[r], n = {
            'sitekey': t.getAttribute("data-sitekey"),
            'theme': t.getAttribute("data-theme"),
            'size': t.getAttribute("data-size")
        };
		if (window.bfirecaptcha[t.id]!== undefined) {
				grecaptcha.reset(window.bfirecaptcha[t.id]);
		} else {
				window.bfirecaptcha[t.id] = grecaptcha.render(t, n)
		}
	}
};