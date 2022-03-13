<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<title>&#x1F952; gurka.se</title>
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
		<style rel="stylesheet" type="text/css">
			html, body {
				height:100%;
				width:100%;
			}
			body {
				overflow-x:hidden;
				overflow-y:hidden;
				background: white;
				color: black;
			}
			body.darkmode {
				background: #121212;
				color: #aaa;
			}
			.darkmode a {
				color: #aaa;
			}
			html, body, div, p, ul, li {
				padding:0;
				margin:0;
			}
			div#gurkburk {
				width:100%;
				height:70%;
				margin-top:10px;
			}
			div#gurka {
				width:100%;
				height:100%;
				background-image:url(/img/gurka.jpg); /* flickr.com/photos/vizzzual-dot-com */
				background-size: contain;
				/*background-size: 400px 300px;*/
				background-position: center;
				background-repeat: no-repeat;
				<? if ( isset($_REQUEST['allergisk']) ) { ?>
					display:none; /* göm gurka för allergiker */
				<? } ?>
			}
			.darkmode div#gurka {
				background-image:url(/img/gurka-transp.png); /* flickr.com/photos/vizzzual-dot-com */
			}
			ul#fot {
				position:absolute;
				left:0;
				bottom:10px;
				width:100%;
				text-align:center;
				font-size:0;
			}
			ul#fot li {
				display:inline;
				font-size:16px;
				padding:0 8px;
				font-family: 'Roboto', sans-serif;
				overflow-wrap: break-word;
			}
			ul#fot li.right_of_center {
				border-left:1px solid #aaa;
			}
			ul#fot li.left_of_center {
				border-right:1px solid #aaa;
			}
			ul#fot a {
				text-decoration:none;
			}
			
			/* darkmode-knapp */

			.darkmode_knapp {
				position: fixed;
				bottom: 10px;
				right: 10px;
			}
			.darkmode_knapp:hover {
				cursor:pointer;
			}
			.darkmode .darkmode_knapp#moon {
				display:none;
			}
			body:not(.darkmode) .darkmode_knapp#sun {
				display:none;
			}

			/* image preload */
			div#preload {
				position:absolute;
				width:0;
				height:0;
				overflow:hidden;
				z-index: -1;
				background:url(/img/gurka-transp.png) no-repeat -9999px -9999px;
			}

		</style>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-S6QQ0640WP"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'G-S6QQ0640WP');
		</script>
		<script type="text/javascript" src="/ext/jquery-3.1.1.min.js"></script>
		<script type="text/javascript" src="/ext/jQueryRotate.js"></script>
		<script type="text/javascript" src="/ext/js.cookie.js"></script>
		<script type="text/javascript" src="/ext/YourJS-2.24.3.OT.min.js"></script>
		<script type="text/javascript">


			var GURKA_AV_HALFLIFE_ms = 5e3;
			var GURKA_AV_R_pms = -Math.log(2)/GURKA_AV_HALFLIFE_ms;
			var GURKA_STABILITY_cps = 4;
			var GURKA_AV_BASE_dpms = 6e-3;
			var GURKA_MIN_CLICK_DELAY_ms = 1000/12;
			var GURKA_CLICK_INC_d = 1;
			var GURKA_CLICK_INC_dpms = 8e-3;
			var GURKA_CLICK_INC_dpmsratio = Math.exp( - GURKA_AV_R_pms * 1e3/GURKA_STABILITY_cps );

			var GURKA_SPRING_CONST_SQRT_pms = 1e-3;
			var GURKA_SPRING_GRACE_ms = 200;
			var GURKA_MIN_AV_dpms = -0.11*360/1e3;
			var GURKA_RCLICK_DEC_dpms = 1e-3;

			var lastClickDate = new Date();
			var lastClickA_d = 0;
			var lastClickAV_dpms = GURKA_AV_BASE_dpms;
			var lastClickTurns = 0;
			var lastClickSpringTwist_d = 0;
			var gurkaAV_dpms = GURKA_AV_BASE_dpms;
			var gurkaA_d = 0;
			var gurkaTurns = 0;
			var gurkaSpringTwist_d= 0;

			var lastPingDate = new Date();
			var lastPingClickDate = new Date();
			var lastPingClickTurns = 0;
			var lastPingClicksSince = 0;
			var lastPingRClicksSince = 0;
			var numClicks = 0;
			var numRClicks = 0;

			var numDarkmodeClicks = 0;
			var lastDarkmodeDate = new Date();

			var UIObjectVisibility = {};

			function hptIntegrate( dt_ms, av0_dpms ) {
				<?/*
				dt_ms: time difference in ms
				av0_dpms: angular velocity at t=0
				returns: [ da_d, av_dpms] = [ degrees difference from t=0 to t=dt_ms, angular velocity at t=dt_ms ]
				*/?>
				var av0_NBL_dpms = av0_dpms - GURKA_AV_BASE_dpms;
				var expRT = Math.exp( GURKA_AV_R_pms * dt_ms );
				var av_NBL_dpms = expRT * av0_NBL_dpms;
				av_dpms = av_NBL_dpms + GURKA_AV_BASE_dpms;
				var da_NBL_d = av0_NBL_dpms / GURKA_AV_R_pms * ( expRT - 1 );
				da_d = da_NBL_d + dt_ms * GURKA_AV_BASE_dpms;
				return [da_d, av_dpms];
			}
			function springIntegrate( dt_ms, av0_dpms, twist0_d ) {
				var da_d, av_dpms, twist_d;
				<?/*
				dt_ms: time difference in ms
				twist0_d: twist angle = "spring displacement" = number of degrees "charged"
				av0_dpms: angular velocity at t=0
				returns: [ da_d, av_dpms, twist_d ] = [ degrees difference from t=0 to t=dt_ms, angular velocity at t=dt_ms, twist angle at t=dt_ms ]

				calc:
				x(t) := angle ("displacement")
				spring eq: x''(t) = -k x(t),  sqrt(k) = GURKA_SPRING_CONST_SQRT_pms
				solution:  x(t) = A cos( sqrt(k)*t + phi )
				boundary conditions: x(0) =  lastClickSpringTwist_d
						     x'(0) = lastClickAV_dpms
				x'(0) / x(0) = -sqrt(k)*tan(phi)
				=> phi = atan( -1/sqrt(k) * x'(0)/x(0) )
				A = x(0) / cos(phi)
				time until twist positive: x(t) = 0 => t = 1/sqrt(k) * ( pi/2 - phi )
				*/?>
				// grace period: no force applied
				if ( dt_ms <= GURKA_SPRING_GRACE_ms ) {
					da_d = av0_dpms * dt_ms;
					av_dpms = av0_dpms;
					twist_d = twist0_d + da_d;
				} else {
					var grace_da_d = av0_dpms * GURKA_SPRING_GRACE_ms;
					dt_ms -= GURKA_SPRING_GRACE_ms;
					twist0_d += grace_da_d;
					// spring function
					var phi = Math.atan( -1/GURKA_SPRING_CONST_SQRT_pms * av0_dpms / twist0_d );
					var A = twist0_d / Math.cos(phi);
					// check if all time in spring mode
					var until_spring_free_ms = 1/GURKA_SPRING_CONST_SQRT_pms * ( Math.PI/2 - phi );
					if ( until_spring_free_ms < dt_ms ) {
						twist_d = 0;
						var hptRes = hptIntegrate( dt_ms - until_spring_free_ms, -GURKA_SPRING_CONST_SQRT_pms*A );
						hpt_da_d = hptRes[0];
						da_d = grace_da_d - twist0_d + hpt_da_d;
						av_dpms = hptRes[1];
					} else {
						twist_d = A * Math.cos( GURKA_SPRING_CONST_SQRT_pms * dt_ms + phi );
						av_dpms = -GURKA_SPRING_CONST_SQRT_pms * A * Math.sin( GURKA_SPRING_CONST_SQRT_pms * dt_ms + phi );
						da_d = grace_da_d + twist_d - twist0_d;
					}
				}
				return [ da_d, av_dpms, twist_d ];
			}
			
			function updateGurka() {
				var now = new Date();
				var since_last_click_ms = ( now.getTime() - lastClickDate.getTime() );

				var since_last_click_d = 0;
				if ( lastClickSpringTwist_d < 0 || lastClickAV_dpms < 0 ) { <?/*spring thing*/?>
					var res = springIntegrate( since_last_click_ms, lastClickAV_dpms, lastClickSpringTwist_d );
					since_last_click_d = res[0];
					gurkaAV_dpms = res[1];
					gurkaSpringTwist_d = res[2];
				} else {
					var res = hptIntegrate( since_last_click_ms, lastClickAV_dpms );
					since_last_click_d = res[0];
					gurkaAV_dpms = res[1];
				}

				gurkaA_d = ( lastClickA_d + since_last_click_d ) % 360;
				var since_last_click_turns;
				if ( lastClickA_d + since_last_click_d > 0 ) {
					since_last_click_turns = Math.floor( ( lastClickA_d + since_last_click_d ) / 360 );
				} else {
					since_last_click_turns = -Math.floor( -( lastClickA_d + since_last_click_d ) / 360 );
				}
				gurkaTurns = lastClickTurns + since_last_click_turns;
				return now;
			}
			function updateUI() {
				$("#gurka").rotate({
					angle:gurkaA_d,
					center: ["50%","50%"]
				});
				// num turns
				updateUIObjectVisibility( 'turns', gurkaTurns, gurkaTurns > 0, false );
				// TPS
				AV_tps = gurkaAV_dpms/360*1000;
				updateUIObjectVisibility( 'tps', AV_tps.toFixed(2), AV_tps > 10, AV_tps < 0.5 );
				// twist
				updateUIObjectVisibility( 'twist', (-gurkaSpringTwist_d/360).toFixed(1), gurkaSpringTwist_d < -2*360, gurkaSpringTwist_d >= 0 );
				// wrong dir
				updateUIObjectVisibility( 'dir', undefined, AV_tps < -0.1, AV_tps >= 0 );
				// num darkmode clicks
				updateUIObjectVisibility( 'darkclick', numDarkmodeClicks, numDarkmodeClicks >= 10, numDarkmodeClicks < 5 );
				if ( numDarkmodeClicks >= 10 ) {
					// f = exp( -a * ( n - 10 ) )
					// exp( -a * 90 ) = 0.5 => a = -ln(0.5)/90 =~ 0.0077
					var side = Math.min( $("#gurkburk").height(), $("#gurkburk").width() );
					var short_side = Math.exp( -0.0077 * ( numDarkmodeClicks-10 ) ) * side;
					$("#gurka").css('background-size', Math.round(side) + 'px ' + Math.round(short_side) + 'px');
				} else {
					$("#gurka").css('background-size', 'contain');
				}
			}
			function updateUIObjectVisibility( name, value, showIfHidden, hideIfShown ) {
				if ( ! ( name in UIObjectVisibility ) )  {
					UIObjectVisibility[name] = 0;
				}
				var UIobj = $("#ui_"+name);
				if ( value !== undefined ) {
					if ( value == 1337 ) {
						UIobj.text( 'l33t' );
					} else {
						if ( name === 'turns' || name === 'tps' ) {
							if(UIobj.text() != YourJS.fullNumber(value))
								UIobj.text( YourJS.fullNumber(value));
						} else {
							if(UIobj.text() != value)
								UIobj.text(value);
						}
					}
				}
				if ( showIfHidden && UIObjectVisibility[name] == 0 ) {
					UIObjectVisibility[name] = 2;
					UIobj.fadeIn(2000, function(){
						UIObjectVisibility[name] = 1;
					});
				} else if ( hideIfShown && UIObjectVisibility[name] == 1 ) {
					UIObjectVisibility[name] = 2;
					UIobj.fadeOut(2000, function(){
						UIObjectVisibility[name] = 0;
					});
				}
			}
			function gurkklick(right) {
				var now = updateGurka();
				if ( now.getTime() - lastClickDate.getTime() < GURKA_MIN_CLICK_DELAY_ms ) {
					return;
				}
				lastClickDate = now;
				lastClickTurns = gurkaTurns;
				lastClickSpringTwist_d = gurkaSpringTwist_d;
				if ( ! right ) {
					lastClickA_d = gurkaA_d + GURKA_CLICK_INC_d;
					lastClickAV_dpms = gurkaAV_dpms + GURKA_CLICK_INC_dpms;
					lastClickAV_dpms *= GURKA_CLICK_INC_dpmsratio;
					lastPingClicksSince++;
					numClicks++;
				} else {
					lastClickA_d = gurkaA_d;
					lastClickAV_dpms = Math.max( gurkaAV_dpms - GURKA_RCLICK_DEC_dpms, GURKA_MIN_AV_dpms );
					lastPingRClicksSince++;
					numRClicks++;
				}
				updateGurka();
				updateUI();
				return false;
			}
			$( document ).ready(function() {
				setInterval(function(){
					var now = updateGurka();
					updateUI();
				},40);
				setInterval(function(){
					// reduce darkmode click counter after 15s of no clicking
					var now = new Date();
					console.log( now - lastDarkmodeDate );
					if ( numDarkmodeClicks > 0 && now - lastDarkmodeDate > 10e3 ) {
						numDarkmodeClicks--;
					}
				},200);
				$("#gurka").click(function(){ return gurkklick(0); });
				$("#gurka").contextmenu(function(){ return gurkklick(1); });
				$('body').keyup(function(e){
					if (e.keyCode == 32) { // space
						gurkklick(0);
						return false;
					}
					if (e.keyCode == 8) { // backspace
						gurkklick(1);
						return false;
					}
				});
				$(".darkmode_knapp").click(function(){
					$('body').toggleClass('darkmode');
					numDarkmodeClicks++;
					lastDarkmodeDate = new Date();
				});
			});
		</script>
	</head>
	<body<?=isset($_REQUEST['darkmode'])?' class="darkmode"':''?>>
		<div id="gurkburk">
			<div id="gurka"></div>
		</div>
		<ul id="fot">
			<li id="ui_darkclick" class="left_of_center" style="display:none;" title="Darkmode-klick"></li>
			<? if ( date('m-d') == '05-01' || isset($_REQUEST['beta']) ) { ?>
				<li style="background-color:#ED1C24;color:#fff;padding:6px;">glad f&ouml;rsta maj!</li>
			<? } else { ?>
				<? if ( ! isset($_REQUEST['mailfobi'])  ) { ?>
					<li><a href="mailto:tim@gurka.se" target="_blank" rel="noopener noreferrer" title="tycker om buggrapporter och fanmail">tim@gurka.se</a></li>
				<? } ?>
			<? } ?>
			<li id="ui_turns" class="right_of_center" style="display:none;" title="Varv snurrade"></li>
			<li id="ui_tps"   class="right_of_center" style="display:none;" title="Varv per sekund"></li>
			<li id="ui_dir"   class="right_of_center" style="display:none;color:#ff0000;font-weight:bold;" title="VARNING">FEL H&Aring;LL!</li>
			<li id="ui_twist" class="right_of_center" style="display:none;" title="Varv uppvridna"></li>
		</ul>
		<img class="darkmode_knapp" id="sun"  src="/img/sun-aaa.png" alt="Light-mode" title="Light-mode"> 
		<img class="darkmode_knapp" id="moon" src="/img/moon-fill.png" alt="Dark-mode"  title="Dark-mode"> 
		<!-- darkmode-ikoner från remixicon.com, Apache 2.0-licens -->
		<div id="preload"></div>
	</body>
</html>
