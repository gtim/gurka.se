window.dataLayer = window.dataLayer || [];

function gtag() { dataLayer.push(arguments); }
gtag('js', new Date());
gtag('config', 'G-S6QQ0640WP');

const q = s => document.querySelector(s),
	qa = s => Array.from(document.querySelectorAll(s)),
	settings = {
		maxSpeed: 50,
		idleSpeed: 1,
		minSpeed: -5,
		frameRate: 30, // number of updates every second
		clickSpeed: 1, // multiplies the speed every click
		brakeSpeed: .1, // multiplies the speed every frame
	},
	frameDuration = 1000 / settings.frameRate
footer = {
		turns: q('#turns'),
		tpm: q('#tpm')
	},
	darkmodeButton = q('#darkmodeButton'),
	darkmode = false; // if darkmode is enabled

let gurka = q('#gurka'),
	angle = 0, // current angle
	speed = settings.idleSpeed, // angle-increase per frame
	turns = 0, // number of completed turns
	tpm = 0; // turns per minute if current speed the entire minute

document.body.style.setProperty('--framerate', frameDuration + 'ms');

setInterval(() => {
	angle += speed;

	turns = Math.floor(angle / 360)
	if (footer.turns.innerHTML != turns)
		footer.turns.innerHTML = turns;

	gurka.style.setProperty('--angle', Math.round(angle) + 'deg');

	if (speed.toFixed(2) == settings.idleSpeed)
		speed = settings.idleSpeed
	else {
		if (speed > settings.idleSpeed)
			speed -= settings.brakeSpeed
		else
			speed += settings.brakeSpeed
	}

	tpm = Math.round(speed * settings.frameRate / 6);
	if (footer.tpm.innerHTML != tpm) footer.tpm.innerHTML = tpm;
}, frameDuration);

gurka.addEventListener('click', () => {
	speed += settings.clickSpeed;
	if (speed > settings.maxSpeed) speed = settings.maxSpeed
});
gurka.addEventListener('contextmenu', e => {
	e.preventDefault();
	speed -= settings.clickSpeed;
	if (speed < settings.minSpeed) speed = settings.minSpeed
});

darkmodeButton.addEventListener('click', () => {
	darkmode = !darkmode;
	document.body.classList.toggle('darkmode', darkmode);
	if (darkmode) darkmodeButton.src = 'img/sun.png'
	else darkmodeButton.src = 'img/moon.png';
});