<?php
/**
 * script d'interrogation de l'api de hal, afin de récupérer :
 * * les articles de revues publiés en 2020 qui sont des références avec fichier ou qui sont des références sans fichiers mais qui ont un accès externe au texte
 * * les articles de revues publiés en 2020 qui sont des références sans fichiers et qui n'ont pas d'accès externe au texte
 */


require ('functions.php');
$baseUrl = 'https://'.$_SERVER[HTTP_HOST]. dirname($_SERVER['PHP_SELF']);
// $url = $baseUrl.'/getArticles.php';
$apiUrl = "https://api.archives-ouvertes.fr/crac/hal?q=*:*&fq=submitType_s:file&fq=submittedDate_tdate:[2021-03-01T00:00:00Z%20TO%202021-03-21T00:00:00Z]&rows=0&facet=true&facet.field=status_i&facet.mincount=1&wt=json";
$results = getApi($apiUrl);
$jsonResults = json_decode($results);
// $nbOnlineDocs = $jsonResults->facet_counts->facet_fields->status_i[1];
$nbOnlineDocs = $jsonResults->response->numFound;
// $nbOnlineDocs = 2000;
?>

<style>
	#casuhalathon-counters{
		border: 2px solid #515151;
		border-radius: 10px;
		box-shadow: 2px 2px 4px #888;
	}

	@media screen and (max-width: 500px){
		#casuhalathon-counters{
				margin: 0 auto;
				width: 350px;
				height: 250px;
				font-size: 8px;
		}
	}

	@media screen and (min-width: 501px){
		#casuhalathon-counters{
			margin: 0 auto;
			width: 500px;
			height: 400px;
			font-size: 12px;
		}
	}

	@media screen and (min-width: 768px){
	    #casuhalathon-counters{
			margin: 0 auto;
			width: 600px;
			height: 500px;
			font-size: 12px;
		}
	}

	

	@media screen and (min-width: 1200px){
		#casuhalathon-counters{
			margin: 0 auto;
			width: 700px;
			height: 600px;
			font-size: 16px;
		}
	}
	
</style>


<script type="text/javascript">
window.onload = function()
{
	/**
     *  fonction d'affichage du texte sous le décompte
     */
	function drawText(nbDocs, text1, text2){
		ctx.textAlign = 'left';
        ctx.fillStyle = orangeColor;
        ctx.font = "bold " + 2.4*fontSize + "px Arial ";
        ctx.fillText(nbDocs, xpos, ypos + 1.1*fontSize);

        //ajout du texte à droite du nombre de docs
        ctx.font = " bold "+ 1.1*fontSize +"px Arial";
        ctx.fillText(text1, xpos + 7.5*fontSize, ypos );
        ctx.fillText(text2, xpos + 7.5*fontSize, ypos + 1.1*fontSize);
     }


	/**
	*	fonction de traçage de la jauge
	*/
	 function drawJauge(){
			ctx.beginPath();
			ctx.strokeStyle = darkColor;

			//coin inférieur gauche
			ctx.moveTo(xpos, ypos);

			ctx.shadowColor = shadowColor;
			ctx.shadowBlur = 6;
			ctx.shadowOffsetX = 2;
			ctx.shadowOffsetY = 2;

			//coin inférieur droit
			ctx.lineTo(xpos + jaugeWidth , ypos);

			//coin supérieur droit
			ctx.lineTo(xpos + jaugeWidth , ypos - jaugeHeight + radius);
			
			//coin arrondi
			ctx.lineWidth = 2;
			ctx.arcTo(xpos + jaugeWidth , ypos - jaugeHeight,   xpos + jaugeWidth - radius , ypos - jaugeHeight, radius);
			ctx.arcTo(xpos, ypos - jaugeHeight, xpos, ypos - jaugeHeight + radius, radius);

			//coint supérieur gauche
			ctx.lineTo(xpos , ypos - jaugeHeight + radius);

			//fermeture du rectangle
			ctx.lineTo(xpos, ypos);
			
			//traçage de la ligne
			ctx.stroke();

			//reset de l'ombre pour ne pas en avoir sur le prochain traçage
			resetShadow();

			//traçage du remplissage
			var grd = ctx.createLinearGradient(xpos, ypos+jaugeWidth/2, xpos, ypos-(jaugeLevel*jaugeHeight/maxJaugeLevel));
			grd.addColorStop(0, "#1F8288");
			var jaugePercent = jaugeLevel/maxJaugeLevel;
			// console.log(jaugeLevel);
			if (jaugePercent >= 0 && jaugePercent<.28){
				grd.addColorStop(1, "#4EA145");
			}
			else if (jaugePercent >= .28 && jaugePercent<.49){
				grd.addColorStop(.5, "#4EA145");
				grd.addColorStop(1, "#D6AF29");
			}
			else if (jaugePercent >= .49 && jaugePercent<.84){
				grd.addColorStop(.5, "#4EA145");
				grd.addColorStop(.84, "#D6AF29");
				grd.addColorStop(1, "#D44227");
			}
			else if (jaugePercent >= .84 && jaugePercent<=1){
				grd.addColorStop(.28, "#4EA145");
				grd.addColorStop(.49, "#D6AF29");
				grd.addColorStop(.84, "#D44227");
				grd.addColorStop(1, "#AF4227");
			}
			ctx.fillStyle = grd;			
			// ctx.fillRect(xpos, ypos, jaugeWidth, -jaugeLevel*(99*jaugeHeight/100)/maxJaugeLevel);
			ctx.fillRect(xpos, ypos, jaugeWidth, -jaugeLevel*jaugeHeight/maxJaugeLevel);
			
			//tracer des graduations et des pourcentages
			ctx.strokeStyle = darkColor;

			
			for (i=1 ; i<15 ; i++){
				//placement au bon echelon
				ctx.moveTo(xpos, ypos - (yStep*i));

				//reinit de la couleur du texte la plus courante
				ctx.fillStyle = darkColor;

				//si on est l'échelon 5, 7 ou 10 ou 14 on a un tracé différent
				if (i==5 || i==7 || i==10 || i==14){
					//pour ajuster les différentes largeurs des chiffres de la graduation
					var textAlign = "right";
					var fontType = "bold";

					// pour ajuster la taille de la police
					var fontSizeRate = 1;

					//traçage des graduations
					if (i==5){
						ctx.lineTo(xpos + 20 , ypos - (yStep*i));
					}
					if (i==7){
						//affichage du text à droite de la jauge
						ctx.font =  "normal " + (fontSize * 0.6) +"px Arial ";
						ctx.textAlign = 'left'; 
						ctx.fillText(firstLimitText1, xpos + jaugeWidth + 10, ypos - (yStep*i) - 5);
						ctx.fillText(firstLimitText2, xpos + jaugeWidth + 10, ypos - (yStep*i) + 5);
						
						//Variables pour l'affichage du chiffre de graduation à gauche de la jauge
						fontSizeRate = .8;
						fontType = "normal";

						
						
					}
					if (i==10){
						//affichage du text à droite de la jauge
						ctx.font =  "bold " + (fontSize * 1.2) +"px Arial ";
						if (jaugeLevel>=10000){
							ctx.fillStyle = orangeColor;
						}
						ctx.textAlign = 'left'; 
						ctx.fillText(secondLimitText1, xpos + jaugeWidth + 10, ypos - (yStep*i) - fontSize*0.1);
						ctx.fillText(secondLimitText2, xpos + jaugeWidth + 10, ypos - (yStep*i) + fontSize*1.2);
						
						ctx.lineTo(xpos + jaugeWidth/3 , ypos - (yStep*i));
						ctx.moveTo(xpos + jaugeWidth*2/3, ypos - (yStep*i));
						ctx.lineTo(xpos + jaugeWidth , ypos - (yStep*i));
						fontSizeRate = 1.5;
						
					}
					if (i==14){
					}

					//Affichage des pourcentages à gauche des graduations
					
					ctx.font =  fontType + " " + (fontSize * fontSizeRate) +"px Arial ";
					ctx.textAlign = textAlign; 
					ctx.fillText(1000*i, xpos-(.5*fontSize), ypos - (yStep*i) + 5);
				}
				else{
					//traçage de la ligne
					ctx.lineTo(xpos + 10 , ypos - (yStep*i));
				}
			}

			ctx.stroke();


			//traçage de la ligne pointillée qui ne peut pas être tracée en même temps que les autres
			ctx.setLineDash([5, 5]);
			ctx.moveTo(xpos, ypos - (yStep*7));
			ctx.lineTo(xpos + jaugeWidth , ypos - (yStep*7));
			ctx.stroke();
			ctx.setLineDash([]);
	}

	function resetShadow(){
		//reset de l'ombre pour ne pas en avoir sur le prochain traçage
		ctx.shadowOffsetX = 0;
		ctx.shadowOffsetY = 0;
		ctx.shadowBlur = 0;
	}

	/* fonction de traçage d'un rectangle aux coins arrondis */
	function drawRect(borderWidth, borderHeight, color, shadow=false){
		//init des coordonnées pour le traçage du cadre avec coins arrondis
		if (shadow){
		ctx.shadowColor = shadowColor;
		ctx.shadowBlur = 4;
		ctx.shadowOffsetX = 2;
		ctx.shadowOffsetY = 2;

		}
		ctx.strokeStyle = color;
		

		//début du tracé en bas à gauche
        // xpos = xpos + offset;
        // ypos = ypos - offset;
        
		//largeur et hauteur du cadre
		// var borderWidth =  W - (2*offset);
		// var borderHeight = H - (2*offset);

        //traçage de la bordure
		ctx.beginPath();
		
		//coin inférieur gauche
		ctx.moveTo(xpos + radius, ypos);
		
		//ligne basse
		ctx.lineTo( xpos + borderWidth - radius , ypos);

		//arrondi en bas à droite
		ctx.arcTo( xpos + borderWidth  , ypos,  xpos + borderWidth  , ypos - radius , radius);

		//ligne côté droit
		ctx.lineTo(xpos + borderWidth , ypos - borderHeight + radius);

		//arrondi en haut à droite
		ctx.arcTo(xpos + borderWidth, ypos - borderHeight, xpos + borderWidth - radius, ypos - borderHeight, radius);

		//ligne haute
		ctx.lineTo(xpos + radius  , ypos - borderHeight);

		//coin arrondi en haut à gauche
		ctx.arcTo(xpos, ypos - borderHeight, xpos, ypos - borderHeight + radius, radius);

		//ligne gauche
		ctx.lineTo(xpos, ypos  - radius);
		
		//coin arrondi en bas à gauche
		ctx.arcTo(xpos, ypos, xpos + radius, ypos, radius);

		//traçage de la ligne
		ctx.stroke();

		resetShadow();

	}


	function drawCountDown(){
		//init des coordonnées pour le traçage du cadre avec coins arrondis
		ctx.shadowColor = shadowColor;
		ctx.shadowBlur = 4;
		ctx.shadowOffsetX = 2;
		ctx.shadowOffsetY = 2;
		ctx.strokeStyle = darkColor;

		//largeur et hauteur du rectangle
		var borderWidth =  W/100*40;
		var borderHeight = H/100*25;

		//traçage de la bordure
		ctx.beginPath();

		//coin inférieur gauche
		ctx.moveTo(xpos + radius, ypos);

		//ligne basse
		ctx.lineTo( xpos + borderWidth - radius , ypos);

		//arrondi en bas à droite
		ctx.arcTo( xpos + borderWidth  , ypos,  xpos + borderWidth  , ypos - radius , radius);

		//ligne côté droit
		ctx.lineTo(xpos + borderWidth , ypos - borderHeight + radius);

		//arrondi en haut à droite
		ctx.arcTo(xpos + borderWidth, ypos - borderHeight, xpos + borderWidth - radius, ypos - borderHeight, radius);

		//ligne haute 1ere partie
		// ctx.lineTo(xpos + radius  , ypos - borderHeight);
		ctx.lineTo(xpos + (3*borderWidth/4)+ radius  , ypos - borderHeight);

		


		//ligne haute 2eme partie
		ctx.moveTo(xpos + borderWidth/4, ypos-borderHeight);
		ctx.lineTo(xpos + radius  , ypos - borderHeight);

		//coin arrondi en haut à gauche
		ctx.arcTo(xpos, ypos - borderHeight, xpos, ypos - borderHeight + radius, radius);

		//ligne gauche
		ctx.lineTo(xpos, ypos - radius);
		
		//coin arrondi en bas à gauche
		ctx.arcTo(xpos, ypos, xpos + radius, ypos, radius);



		//traçage de la ligne
		ctx.stroke();

		resetShadow();
		
		//traçage du texte
		ctx.textAlign = 'left';
		ctx.font = " bold "+ 1.1*fontSize +"px Arial";
        ctx.fillText(textTime, xpos + borderWidth/4 + 15, ypos - borderHeight + 5 );
		
		//tracé des chiffres du compteurs
		//largeur et hauteur du rect de chaque chiffre
		var rectWidth =	borderWidth/100*20;
		var rectHeight = borderHeight/100*40;
		
		//params du texte (jour, mois, heure, secondes)
		ctx.textAlign = 'center';
		var labelFontSize = " bold "+ 0.8*fontSize +"px Arial";
		var digitFontSize = " bold "+ 2*fontSize +"px Arial";
		
        
		xpos = xpos + borderWidth/100*4;
        ypos = ypos - borderHeight/100*30;
		drawRect(rectWidth, rectHeight, orangeColor);
		ctx.font = labelFontSize;
		ctx.fillText('Jours', xpos + rectWidth/2, ypos + rectHeight/3 );
		ctx.font = digitFontSize;
		ctx.fillText(days, xpos + rectWidth/2, ypos - rectHeight/2 + 3*fontSize/5);


		xpos = xpos + borderWidth/100*24;
		drawRect(rectWidth, rectHeight, orangeColor);
		ctx.font = labelFontSize;
		ctx.fillText('Heures', xpos + rectWidth/2, ypos + rectHeight/3  );
		ctx.font = digitFontSize;
		ctx.fillText(hours, xpos + rectWidth/2, ypos - rectHeight/2 + 3*fontSize/5 );

		xpos = xpos + borderWidth/100*24;
		drawRect(rectWidth, rectHeight, orangeColor);
		ctx.font = labelFontSize;
		ctx.fillText('Minutes', xpos + rectWidth/2 , ypos + rectHeight/3 );
		ctx.font = digitFontSize;
		ctx.fillText(minutes, xpos + rectWidth/2, ypos - rectHeight/2 + 3*fontSize/5 );
		
		xpos = xpos + borderWidth/100*24;
		drawRect(rectWidth, rectHeight, orangeColor);
		ctx.font = labelFontSize;
		ctx.fillText('Secondes', xpos + rectWidth/2, ypos + rectHeight/3 );
		ctx.font = digitFontSize;
		ctx.fillText(seconds, xpos + rectWidth/2, ypos - rectHeight/2 + 3*fontSize/5);


	}


	/*
	 * Initialise le canvas en créant le texte avec l'affichage des compteurs et le rectangle gradué avec la jauge
	 */
	function init()
	{
		//effacement du canvas
        ctx.clearRect(0, 0, W, H);

		//drawCanvasBorder();
				
        /**
        *   affichage des textes
         */
        //calcul des coordonnées
        xpos = W/100*48;
        ypos = H/100*75;

        //traçage du texte
        drawText(nbOnlineDocsLevel, docsText1, docsText2);

		       		
		/**
        *   traçage de la jauge
         */
        xpos = W/100*15;
        ypos = H-(H/100*9);

		//traçage de la jauge
		drawJauge();
		
		
		// affichage du logo openaccess
		if (jaugeLevel < 10000){
			ctx.drawImage(cadenas, xpos + jaugeWidth/2 - .75*fontSize + cadenasPosX , ypos - (yStep*10) - 1.2*fontSize, 1.5*fontSize, 2*fontSize);
		}
		//affichage du logo succes
		else if (jaugeLevel >=10000){
			ctx.drawImage(success, xpos + jaugeWidth*2+10 - .75*fontSize , ypos - (yStep*10) - 1.1*fontSize, 2*fontSize, 2.5*fontSize);
		}
		

		/**
		*  affichage du compteur
		 */
		xpos = W/100*57;
        ypos = H-(H/100*47);
		drawCountDown();
	
	}

	/*
	 * Calcul du nouveau taux à afficher, rafraîchit les valeurs avec une requête AJAX
	 */
	function draw()
	{
		if (typeof animation_loop != undefined) clearInterval(animation_loop);


		var request = new XMLHttpRequest();
		request.onreadystatechange = function() {
			if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
				var response = JSON.parse(this.responseText);
				
				//récupération des résultats et affectation aux variables de l'animation
				nbOnlineDocs = response.response.numFound;
				nbOnlineDocs = 15000;
				
				//on réinitialise la jauge et le nombre d'animations du cadenas et le nombre de docs dans l'api
				jaugeLevel = 0;
				nbOnlineDocsLevel = 0;
				cadenasCountAnimate = 0;

				//déclenchement de l'animation toutes les 30 millisecondes
				animation_loop = setInterval(animate_jauge, 40 );
			}
		};
		request.open("GET", "<?= $apiUrl ?>");
		request.send();
	}

	/*
	 * Crée l'animation du cercle
	 */
	function animate_jauge()
	{
		//if (jaugeLevel > nbOnlineDocs) clearInterval(animation_loop);
		if (jaugeLevel < nbOnlineDocs){
		 	jaugeLevel += 100;
		}

		if (jaugeLevel >= maxJaugeLevel)
			jaugeLevel = maxJaugeLevel;
			
		if(nbOnlineDocsLevel < nbOnlineDocs)
			nbOnlineDocsLevel = nbOnlineDocsLevel + 100;
		else
			nbOnlineDocsLevel = nbOnlineDocs;


			
		// console.log(jaugeLevel);
		/**
		*  Calcul et affichage des valeurs du countDown
		*/
		// Get today's date and time
		now = new Date().getTime();

		// Find the distance between now and the count down date
		distance = countDownDate - now;

		// Time calculations for days, hours, minutes and seconds
		days = Math.floor(distance / (1000 * 60 * 60 * 24));
		hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		seconds = Math.floor((distance % (1000 * 60)) / 1000);

		// // If the count down is finished, write some text
		// if (distance < 0) {
		// clearInterval(x);
		// document.getElementById("demo").innerHTML = "EXPIRED";
		// }
		// }, 1000);
		if (jaugeLevel >8000 && jaugeLevel <=10000 && cadenasCountAnimate<15){
			if (cadenasMvment){
				cadenasPosX += 3;
				cadenasMvment = false;
			}
			else{
				cadenasPosX -= 3;
				cadenasMvment = true;
			}
			cadenasCountAnimate++;
		}

		init();
	}

	var container = document.getElementById('casuhalathon-counters');
	var canvas = document.getElementById("casuhalathon-canvas");
	
	if (canvas.getContext){
		var ctx = canvas.getContext("2d");
	
		//récupération des dimensions css du conteneur du canvas
		var cssWidth = window.getComputedStyle(container).getPropertyValue('width');
		var cssHeight = window.getComputedStyle(container).getPropertyValue('height');
		var cssFontSize = window.getComputedStyle(container).getPropertyValue('font-size');
		
		var W = cssWidth.split('px')[0];
		var H = cssHeight.split('px')[0];
		var fontSize = cssFontSize.split('px')[0];
		
		canvas.width  = W;
		canvas.height = H; 
			
		var animation_loop, redraw_loop;


		var nbOnlineDocs = <?= $nbOnlineDocs ?>;
		var docsText1 = 'fichiers déposés';
		var docsText2 = "dans HAL";
		var textTime = 'Temps restant';
		var firstLimitText1 = 'dépôts moyens dans HAL';
		var firstLimitText2 = 'sur une période de 3 semaines';
		var secondLimitText1 = 'OBJECTIF';
		var secondLimitText2 = 'HALATHON';

		//déclage de la bordure par rapport au cadre pour l'ombre et les coins arrondis
		var offset = 8;
		var radius = 10;

		//cordonnées d'origine d'un tracé
		var xpos = 0;
		var ypos = 0;

		var orangeColor = "#dd7226";
		var darkColor = "#515151";
		var shadowColor = "#777";

		//init valeur de remplissage de la jauge
		var jaugeLevel = 0;
		var maxJaugeLevel = 14000;
		// var finaljaugeLevel = Math.round(nbOnlineDocs * 100 / nbArticles);
		
		var cadenasMvment = false;
		var cadenasPosX = 0;
		var cadenasCountAnimate = 0;

		//taille de la jauge
		var jaugeHeight = H/100*80;
        var jaugeWidth = W/100*18;

		// Date de fin pour le décompte du compteur
		var countDownDate = new Date("Jun 11, 2021 24:00:00").getTime();
		var now = new Date().getTime();

		// Find the distance between now and the count down date
		var distance = countDownDate - now;

		// Time calculations for days, hours, minutes and seconds
		var days = 0;
		var hours = 0;
		var minutes = 0;
		var seconds = 0;

		

		//calcul du pas entre les graduations		
		var yStep = jaugeHeight/14;

		//images
		var cadenas = new Image();
		cadenas.src = "<?= $baseUrl ?>" + '/images/cadenas_ferme.png';
		cadenas.onload = function () {
        };

		var success = new Image();
		success.src = "<?= $baseUrl ?>" + '/images/succes.png';
		success.onload = function () {
        };

		draw();
		
		/* Crée une boucle sur la fonction draw() avec un intervalle de 30 secondes */
		redraw_loop = setInterval(draw, 30000);

		// init();
	}


}
</script>

<?php	if (empty($nbOnlineDocs)): ?>
			<p>Aucune donnée disponible pour le moment</p>
<?php 	else : ?>
			<div id="casuhalathon-counters">
			    <canvas id="casuhalathon-canvas">Graphique HAL non disponible</canvas>
            </div>
<?php   endif ?>

	

