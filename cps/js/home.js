<!--=============== Start Hero Offset Adjustment ===============-->
	/*<!--<script>
	function adjustHeroOffset() {
	const bars = document.querySelectorAll('.announcement-bar, .cps-topbar, .cps-header');
	const hero = document.querySelector('.hero');
	let total = 0;
	bars.forEach(el => total += el.offsetHeight);
	hero.style.marginTop = total + 'px';
	}
	window.addEventListener('load', adjustHeroOffset);
	window.addEventListener('resize', adjustHeroOffset);
	</script>-->*/
	<!--=============== End Hero Offset Adjustment ===============-->
    <!--=============== Start of Toggle Menu  ===============-->
    <script>
	function toggleMenu(){
  	const nav = document.getElementById("cpsNav");
  	nav.classList.toggle("active");
	}
	</script>
    <!--=============== End of Toggle Menu  ===============-->
    <!--=============== Start of admission Popup  ===============-->
    <script>
	function closePopup(){
  	document.getElementById("admissionPopup").style.display="none";
	}
	</script>
    <!--=============== End of admission Popup  ===============-->
	
    <!--=============== Start of Animated Counting  ===============-->
    <script>
	document.addEventListener("DOMContentLoaded", function () {
	const counters = document.querySelectorAll('.stat-number');
	const duration = 5000; // total animation time in ms (3 seconds)
	
	counters.forEach(counter => {
		const target = +counter.getAttribute('data-target');
		const start = 0;
		const startTime = performance.now();
	
		function update(currentTime) {
		const elapsed = currentTime - startTime;
		const progress = Math.min(elapsed / duration, 1);
		const value = Math.floor(progress * target);
	
		counter.innerText = value;
	
		if (progress < 1) {
			requestAnimationFrame(update);
		} else {
			counter.innerText = target;
		}
		}
	
		requestAnimationFrame(update);
	});
	});
	</script>
	<!--=============== End of Animated Counting  ===============-->
    
    <!--=============== Start of Topper & News Section  ===============-->
    <script>
document.addEventListener("DOMContentLoaded", function () {

  const slider = document.getElementById('studentSlider');
  if (!slider) return;

  const cardWidth = 270; // 220 + 50 gap

  slider.innerHTML += slider.innerHTML;

  let position = 0;

  function slideRight() {
    position -= cardWidth;
    if (Math.abs(position) >= slider.scrollWidth / 2) {
      position = 0;
    }
    slider.style.transform = `translateX(${position}px)`;
  }

  function slideLeft() {
    position += cardWidth;
    if (position > 0) {
      position = -slider.scrollWidth / 2 + cardWidth;
    }
    slider.style.transform = `translateX(${position}px)`;
  }

  window.slideRight = slideRight;
  window.slideLeft = slideLeft;

  setInterval(slideRight, 3000);
});
</script>

	
	<!--=============== End of Topper & News Section  ===============-->