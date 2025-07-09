<?php

/**
 * custom template file for Rock Stars theme.
 *
 * Template Name: Page Template Custom 2
 *
 * @package Rock_Star
 */



get_header(); ?>

<div id="stars" class="earth-wrapp-bg">

<div class="earth_wrap" bis_skin_checked="1">
  	<div id="myearth" class="earth-container earth-ready" bis_skin_checked="1">
		<canvas width="891" height="812" style="display: block; width: 712.875px; height: 650px;"></canvas>
	</div>
</div>

</div>

<script>

function createStars(i) {
  for (var i; i; i--) {
    drawStars();
  }
}

function drawStars(){
  var tmpStar = document.createElement('figure')
  tmpStar.className = "star";
  tmpStar.style.top = 100*Math.random()+'%';
  tmpStar.style.left = 100*Math.random()+'%';
  document.getElementById('stars').appendChild(tmpStar);
}

function selectStars() {
    stars = document.querySelectorAll(".star");
  console.log(stars)
}

function animateStars() {
      Array.prototype.forEach.call(stars, function(el, i){
      TweenMax.to(el, Math.random() * 0.5 + 0.5, {opacity: Math.random(), onComplete: animateStars});
    });
}

createStars(200);
selectStars();
animateStars();

</script>

<?php get_footer(); ?>