document.addEventListener('DOMContentLoaded', () => {
  const track = document.getElementById('sliderTrack');
  if (!track) return; // Если нет элемента — прекратить выполнение

  const slides = Array.from(track.children);
  const gapPercent = 2; // gap 2%
  let slideWidthPercent = 38.46; // по умолчанию для десктопа
  let visibleSlidesCount = 2.6;

  let index = 0;
  let isDragging = false;
  let startPos = 0;
  let currentTranslate = 0;
  let prevTranslate = 0;
  let animationID = 0;

  function updateSizes() {
    if(window.innerWidth <= 640) {
      slideWidthPercent = 100;
      visibleSlidesCount = 1;
      track.style.gap = '0';
    } else {
      slideWidthPercent = 38.46;
      visibleSlidesCount = 2.6;
      track.style.gap = '2%';
    }
  }

  updateSizes();

  function cloneSlides() {
    const clones = track.querySelectorAll('.clone');
    clones.forEach(c => c.remove());

    for(let i = 0; i < Math.ceil(visibleSlidesCount); i++){
      const clone = slides[i].cloneNode(true);
      clone.classList.add('clone');
      track.appendChild(clone);
    }
  }
  cloneSlides();

  function updatePosition(animate = true) {
    const slideWithGap = slideWidthPercent + (window.innerWidth <= 640 ? 0 : gapPercent);
    const shift = index * slideWithGap;
    if(animate) {
      track.style.transition = 'transform 0.6s ease';
    } else {
      track.style.transition = 'none';
    }
    track.style.transform = `translateX(${-shift}%)`;
  }

  function nextSlide() {
    index++;
    if (index > slides.length) {
      updatePosition(false);
      index = 0;
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          updatePosition(true);
          index++;
        });
      });
    } else {
      updatePosition();
    }
  }

  function prevSlide() {
    index--;
    if (index < 0) {
      updatePosition(false);
      index = slides.length;
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          updatePosition(true);
          index--;
        });
      });
    } else {
      updatePosition();
    }
  }

  const nextBtn = document.getElementById('nextBtn');
  const prevBtn = document.getElementById('prevBtn');
  const sliderContainer = document.getElementById('sliderContainer');

  if(nextBtn) {
    nextBtn.addEventListener('click', () => {
      nextSlide();
      resetInterval();
    });
  }

  if(prevBtn) {
    prevBtn.addEventListener('click', () => {
      prevSlide();
      resetInterval();
    });
  }

  let slideInterval = setInterval(nextSlide, 6000);
  function resetInterval() {
    clearInterval(slideInterval);
    slideInterval = setInterval(nextSlide, 6000);
  }

  if(sliderContainer) {
    sliderContainer.addEventListener('touchstart', touchStart);
    sliderContainer.addEventListener('touchmove', touchMove);
    sliderContainer.addEventListener('touchend', touchEnd);
    sliderContainer.addEventListener('touchcancel', touchEnd);
  }

  function touchStart(event) {
    startPos = event.touches[0].clientX;
    isDragging = true;
    track.style.transition = 'none';
    resetInterval();
  }

  function touchMove(event) {
    if (!isDragging) return;
    const currentPos = event.touches[0].clientX;
    const diff = startPos - currentPos;
    const containerWidth = sliderContainer.offsetWidth;
    let diffPercent = (diff / containerWidth) * 100;

    let slideWithGap = slideWidthPercent + (window.innerWidth <= 640 ? 0 : gapPercent);

    let movePercent = index * slideWithGap + diffPercent;

    if(movePercent < 0) movePercent = 0;
    if(movePercent > (slides.length) * slideWithGap) movePercent = (slides.length) * slideWithGap;

    track.style.transform = `translateX(${-movePercent}%)`;
  }

  function touchEnd(event) {
    if(!isDragging) return;
    isDragging = false;
    const endPos = event.changedTouches[0].clientX;
    const diff = startPos - endPos;
    const threshold = 50;

    if(Math.abs(diff) > threshold) {
      if(diff > 0) {
        nextSlide();
      } else {
        prevSlide();
      }
    } else {
      updatePosition();
    }
  }

  window.addEventListener('resize', () => {
    updateSizes();
    cloneSlides();
    updatePosition(false);
  });

  updatePosition();
});


// Перехватываем стандартный alert и заменяем его на SweetAlert2

window.alert = function(message) {
  Swal.fire({
    toast: true,
    position: 'bottom-start',
    icon: false,
    title: message,
    showConfirmButton: false,
    timer: 3000,
    customClass: {
      popup: 'custom-toast'
    }
  });
};

