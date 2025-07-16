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


// FAQ


function wpToggleFaq(index) {
    const currentAnswer = document.getElementById(`wp-answer-${index}`);
    const currentArrow = document.getElementById(`wp-arrow-${index}`);

    // Получаем все открытые элементы и закрываем их
    document.querySelectorAll('.wp-faq-answer.open').forEach(answer => {
        answer.classList.remove('open');
    });

    document.querySelectorAll('.wp-faq-arrow.wp-faq-rotate-180').forEach(arrow => {
        arrow.classList.remove('wp-faq-rotate-180');
    });

    // Если текущий элемент не открыт — открыть его
    if (!currentAnswer.classList.contains('open')) {
        currentAnswer.classList.add('open');
        currentArrow.classList.add('wp-faq-rotate-180');
    }
}




//Earth script

const home_url = window.location.origin;

if ( location.protocol == 'file:' ) {
	alert( 'This demo does not work with the file protocol due to browser security restrictions.' );
}

var myearth;
var sprites = [];

window.addEventListener( 'load', function() {
	myearth = new Earth( 'myearth', {
		location : { lat: 20, lng : 20 },
		light: 'none',
		mapImage: home_url + '/wp-content/uploads/2025/07/hologram-map.svg',
		transparent: true,
		autoRotate : true,
		autoRotateSpeed: 1.2,
		autoRotateDelay: 100,
		autoRotateStart: 2000,			
		
	} );
	
	myearth.addEventListener( "ready", function() {
		this.startAutoRotate();
		// connections
		var line = {
			// color : '#009CFF',
			color : '#4A6CF7',
			opacity: 0.35,
			hairline: true,
			offset: -0.9
		};
		
		for ( var i in connections ) {			
			line.locations = [ { lat: connections[i][0], lng: connections[i][1] }, { lat: connections[i][2], lng: connections[i][3] } ];
			this.addLine( line );
		}
		
		// add 8 shine sprites
		for ( var i=0; i < 8; i++ ) {
			sprites[i] = this.addSprite( {
				image: home_url + '/wp-content/uploads/2025/07/hologram-shine.svg',
				scale: 0.01,
				offset: -0.9,
				opacity: 0.9
			} );
			pulse( i );
		}
	} );
} );

function getRandomInt(min, max) {
	min = Math.ceil(min);
	max = Math.floor(max);
	return Math.floor(Math.random() * (max - min)) + min;
}

function pulse( index ) {
	var random_location = connections[ getRandomInt(0, connections.length-1) ];
	sprites[index].location = { lat: random_location[0] , lng: random_location[1] };
	
	sprites[index].animate( 'scale', 0.5, { duration: 320, complete : function(){
		this.animate( 'scale', 0.01, { duration: 320, complete : function(){
			setTimeout( function(){ pulse( index ); }, getRandomInt(100, 400) );
		} });
	} });
}



// locations conntected by lines and places where hologram shines appear

var connections = [
	[59.651901245117,17.918600082397,	41.8002778,12.2388889],
	[59.651901245117,17.918600082397,	51.4706,-0.461941],
	
	[13.681099891662598,100.74700164794922,	-6.1255698204,106.65599823],
	[13.681099891662598,100.74700164794922,	28.566499710083008,77.10310363769531],
	
	[30.12190055847168,31.40559959411621, -1.31923997402,36.9277992249],
	[30.12190055847168,31.40559959411621, 25.2527999878,55.3643989563],
	[30.12190055847168,31.40559959411621, 41.8002778,12.2388889],

	[28.566499710083008,77.10310363769531,	7.180759906768799,79.88410186767578],
	[28.566499710083008,77.10310363769531,	40.080101013183594,116.58499908447266],
	[28.566499710083008,77.10310363769531,	25.2527999878,55.3643989563],

	[-33.9648017883,18.6016998291, -1.31923997402,36.9277992249],
	
	[-1.31923997402,36.9277992249, 25.2527999878,55.3643989563],
	
	[41.8002778,12.2388889, 51.4706,-0.461941],
	[41.8002778,12.2388889, 40.471926,-3.56264],

	[19.4363,-99.072098,	25.79319953918457,-80.29060363769531],
	[19.4363,-99.072098,	33.94250107,-118.4079971],
	[19.4363,-99.072098,	-12.0219,-77.114304],
	
	[-12.0219,-77.114304,	-33.393001556396484,-70.78579711914062],
	[-12.0219,-77.114304, -34.8222,-58.5358],
	[-12.0219,-77.114304, -22.910499572799996,-43.1631011963],
	
	[-34.8222,-58.5358, -33.393001556396484,-70.78579711914062],
	[-34.8222,-58.5358, -22.910499572799996,-43.1631011963],
	
	[22.3089008331,113.915000916, 13.681099891662598,100.74700164794922],
	[22.3089008331,113.915000916, 40.080101013183594,116.58499908447266],
	[22.3089008331,113.915000916, 31.143400192260742,121.80500030517578],
	
	[35.552299,139.779999, 40.080101013183594,116.58499908447266],
	[35.552299,139.779999, 31.143400192260742,121.80500030517578],
	
	[33.94250107,-118.4079971,	40.63980103,-73.77890015],
	[33.94250107,-118.4079971,	25.79319953918457,-80.29060363769531],
	[33.94250107,-118.4079971,	49.193901062,-123.183998108],
	
	[40.63980103,-73.77890015, 25.79319953918457,-80.29060363769531],
	[40.63980103,-73.77890015, 51.4706,-0.461941],
	
	[51.4706,-0.461941, 40.471926,-3.56264],
	
	[40.080101013183594,116.58499908447266,	31.143400192260742,121.80500030517578],
	
	[-33.94609832763672,151.177001953125,	-41.3272018433,174.804992676],
	[-33.94609832763672,151.177001953125,	-6.1255698204,106.65599823],
	
	[55.5914993286,37.2615013123, 59.651901245117,17.918600082397],
	[55.5914993286,37.2615013123, 41.8002778,12.2388889],
	[55.5914993286,37.2615013123, 40.080101013183594,116.58499908447266],
	[55.5914993286,37.2615013123, 25.2527999878,55.3643989563],
];


// cards blue line selected


// Функция для переключения выбора карточек
function initCardSelector() {
    const cards = document.querySelectorAll('.pricing-card, [class*="border"][class*="rounded-xl"]');
    
    // Находим карточку с выделением по умолчанию (с border-2 и border-blue-600)
    let selectedCard = document.querySelector('[class*="border-2"][class*="border-blue-600"]');
    
    // Сохраняем только классы выделения (border-2 border-blue-600)
    const selectedBorderClasses = ['border-2', 'border-blue-600'];
    
    // Функция для выбора карточки
    function selectCard(clickedCard) {
        // Убираем выделение с текущей выбранной карточки
        if (selectedCard) {
            selectedCard.classList.remove(...selectedBorderClasses);
            selectedCard.classList.add('border', 'border-gray-200');
            // Добавляем dark классы обратно
            if (selectedCard.classList.contains('dark:border-neutral-800') || 
                document.documentElement.classList.contains('dark')) {
                selectedCard.classList.add('dark:border-neutral-800');
            }
            
            // Убираем синюю кнопку и возвращаем обычную
            const prevButton = selectedCard.querySelector('a');
            if (prevButton) {
                prevButton.className = 'mt-5 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white dark:text-white shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800';
            }
            
            // Убираем плашку с предыдущей карточки
            const prevBadge = selectedCard.querySelector('.custom-color-bage');
            if (prevBadge && prevBadge.parentElement) {
                prevBadge.parentElement.remove();
            }
        }
        
        // Применяем классы выделения к clicked карточке
        clickedCard.classList.remove('border', 'border-gray-200');
        clickedCard.classList.add(...selectedBorderClasses);
        
        // Меняем кнопку на синюю
        const button = clickedCard.querySelector('a');
        if (button) {
            button.className = 'mt-5 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none';
        }
        
        // Добавляем плашку "Your choice"
        const title = clickedCard.querySelector('h4');
        if (title && !clickedCard.querySelector('.custom-color-bage')) {
            const badge = document.createElement('p');
            badge.className = 'mb-3';
            badge.innerHTML = '<span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-lg text-xs uppercase font-semibold bg-blue-100 text-blue-800 dark:bg-blue-600 dark:text-white custom-color-bage">Your choice</span>';
            title.parentNode.insertBefore(badge, title);
        }
        
        // Обновляем ссылку на выбранную карточку
        selectedCard = clickedCard;
    }
    
    // Добавляем обработчики клика на все карточки
    cards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', () => selectCard(card));
    });
    
    return {
        getSelected: () => selectedCard,
        select: (index) => {
            if (cards[index]) {
                selectCard(cards[index]);
            }
        }
    };
}

// Простая версия без возврата объекта
function setupCardSelection() {
    const cards = document.querySelectorAll('.pricing-card, [class*="border"][class*="rounded-xl"]');
    
    // Классы для выделения
    const selectedBorderClasses = ['border-2', 'border-blue-600'];
    
    cards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function() {
            // Убрать выделение со всех карточек
            cards.forEach(c => {
                c.classList.remove(...selectedBorderClasses);
                c.classList.add('border', 'border-gray-200');
                if (c.classList.contains('dark:border-neutral-800')) {
                    c.classList.add('dark:border-neutral-800');
                }
                
                // Убираем синюю кнопку и возвращаем обычную
                const button = c.querySelector('a');
                if (button) {
                    button.className = 'mt-5 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white dark:text-white shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800';
                }
                
                // Убираем плашку с предыдущей карточки
                const badge = c.querySelector('.custom-color-bage');
                if (badge && badge.parentElement) {
                    badge.parentElement.remove();
                }
            });
            
            // Добавить выделение к clicked карточке
            this.classList.remove('border', 'border-gray-200');
            this.classList.add(...selectedBorderClasses);
            
            // Меняем кнопку на синюю
            const button = this.querySelector('a');
            if (button) {
                button.className = 'mt-5 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none';
            }
            
            // Добавляем плашку "Your choice"
            const title = this.querySelector('h4');
            if (title && !this.querySelector('.custom-color-bage')) {
                const badge = document.createElement('p');
                badge.className = 'mb-3';
                badge.innerHTML = '<span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-lg text-xs uppercase font-semibold bg-blue-100 text-blue-800 dark:bg-blue-600 dark:text-white custom-color-bage">Your choice</span>';
                title.parentNode.insertBefore(badge, title);
            }
        });
    });
}

// Инициализация
document.addEventListener('DOMContentLoaded', function() {
    initCardSelector();
    // или просто: setupCardSelection();
});



// Аудио плеер для постов WordPress
