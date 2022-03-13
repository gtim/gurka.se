window.dataLayer = window.dataLayer || [];

function gtag() { dataLayer.push(arguments); }
gtag('js', new Date());
gtag('config', 'G-S6QQ0640WP');

const q = s => document.querySelector(s),
	qa = s => Array.from(document.querySelectorAll(s)),
	settings = {
		maxSpeed: 50,
		minSpeed: 1,
		frameRate: 30, // number of updates every second
		clickMultiplier: 1.1, // multiplies the speed every click
		brakeMultiplier: 0.995, // multiplies the speed every frame
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
	speed = settings.minSpeed, // angle-increase per frame
	turns = 0, // number of completed turns
	tpm = 0; // turns per minute if current speed the entire minute

document.body.style.setProperty('--framerate', frameDuration + 'ms');

setInterval(() => {
	angle += speed;

	turns = Math.floor(angle / 360)
	if (footer.turns.innerHTML != turns)
		footer.turns.innerHTML = turns;

	gurka.style.setProperty('--angle', Math.round(angle) + 'deg');

	speed *= settings.brakeMultiplier;
	if (speed < settings.minSpeed) speed = settings.minSpeed


	tpm = Math.round(speed * settings.frameRate / 6);
	if (footer.tpm.innerHTML != tpm) footer.tpm.innerHTML = tpm;
}, frameDuration);

gurka.addEventListener('click', () => {
	speed *= settings.clickMultiplier;
	if (speed > settings.maxSpeed) speed = settings.maxSpeed
})

darkmodeButton.addEventListener('click', () => {
	darkmode = !darkmode;
	document.body.classList.toggle('darkmode', darkmode);
	if (darkmode) darkmodeButton.src = 'img/sun.png'
	else darkmodeButton.src = 'img/moon.png';
})