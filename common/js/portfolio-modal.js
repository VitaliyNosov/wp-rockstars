// document.addEventListener("DOMContentLoaded", function () {
//   const slides = document.querySelectorAll(".slide");
//   const modal = document.createElement("div");

//   modal.id = "siteModal";
//   modal.style.cssText = `
//     display: none; 
//     position: fixed; top: 0; left: 0;
//     width: 100%; height: 100%; 
//     background: rgba(0,0,0,0.95);
//     justify-content: center; align-items: center; 
//     z-index: 9999;
//     opacity: 0;
//     transition: opacity 0.3s ease;
//   `;

//   modal.innerHTML = `
//     <div id="modalContent" style="
//       position: relative; 
//       width: 95vw; 
//       height: 90vh; 
//       max-width: 1200px;
//       background: #000; 
//       border-radius: 6px; 
//       overflow: hidden;
//       box-shadow: 0 0 15px rgba(0,0,0,0.7);
//     ">
//       <iframe src="" style="
//         width:100%; height:100%; border:none; 
//         opacity: 0; transition: opacity 0.5s ease;
//       " id="modalIframe" allowfullscreen></iframe>
//     </div>

//     <div id="loadingOverlay" style="
//       position: fixed;
//       top: 50%;
//       left: 50%;
//       transform: translate(-50%, -50%);
//       width: 300px;
//       text-align: center;
//       z-index: 10001;
//       user-select: none;
//       color: white;
//       font-family: Arial, sans-serif;
//       font-weight: 600;
//       font-size: 20px;
//     ">
//       <div id="loadingText" style="margin-bottom: 12px;">Loading</div>
//       <div id="progressContainer" style="
//         width: 100%;
//         height: 8px;
//         background: #333;
//         border-radius: 4px;
//         overflow: hidden;
//       ">
//         <div id="progressBar" style="
//           width: 0%;
//           height: 100%;
//           background: #4A6CF7;
//           border-radius: 4px;
//           transition: width 0.2s ease;
//         "></div>
//       </div>
//     </div>
//   `;

//   document.body.appendChild(modal);

//   // Создаем крестик ЗА пределами модалки и добавляем в body
//   const closeBtn = document.createElement("button");
//   closeBtn.id = "closeModal";
//   closeBtn.textContent = "✖";
//   closeBtn.style.cssText = `
//     position: fixed;
//     top: 5vh;
//     right: 2vw;
//     background: #000;
//     color: #fff;
//     border: none;
//     padding: 10px 15px;
//     font-size: 28px;
//     cursor: pointer;
//     border-radius: 50%;
//     user-select: none;
//     box-shadow: 0 0 8px rgba(0,0,0,0.8);
//     z-index: 10002;
//   `;
//   document.body.appendChild(closeBtn);

//   const iframe = modal.querySelector("#modalIframe");
//   const progressBar = modal.querySelector("#progressBar");
//   const loadingOverlay = modal.querySelector("#loadingOverlay");

//   // Анимация прогресса
//   function animateProgress() {
//     let width = 0;
//     const interval = setInterval(() => {
//       if (width >= 90) {
//         clearInterval(interval);
//       } else {
//         width += 5;
//         progressBar.style.width = width + '%';
//       }
//     }, 200);
//     return interval;
//   }

//   let progressInterval;

//   slides.forEach(slide => {
//     slide.addEventListener("click", function (e) {
//       e.preventDefault();

//       progressBar.style.width = '0%';
//       iframe.style.opacity = '0';
//       loadingOverlay.style.display = 'block';
//       modal.style.display = 'flex';
//       closeBtn.style.display = 'block';

//       requestAnimationFrame(() => {
//         modal.style.opacity = '1';
//         closeBtn.style.opacity = '1';
//       });

//       progressInterval = animateProgress();

//       const targetUrl = this.href;
//       const encodedUrl = encodeURIComponent(targetUrl);
//       iframe.src = `/wp-admin/admin-ajax.php?action=proxy_site&url=${encodedUrl}`;
//     });
//   });

//   iframe.addEventListener("load", () => {
//     clearInterval(progressInterval);
//     progressBar.style.width = '100%';

//     loadingOverlay.style.display = 'none';

//     iframe.style.opacity = '1';
//   });

//   function closeModal() {
//     modal.style.opacity = '0';
//     closeBtn.style.opacity = '0';
//     setTimeout(() => {
//       modal.style.display = 'none';
//       closeBtn.style.display = 'none';
//       iframe.src = "";
//       progressBar.style.width = '0%';
//       loadingOverlay.style.display = 'none';
//     }, 300);
//   }

//   closeBtn.addEventListener("click", closeModal);

//   modal.addEventListener("click", (e) => {
//     if (e.target === modal) {
//       closeModal();
//     }
//   });
// });


document.addEventListener("DOMContentLoaded", function () {
  const slides = document.querySelectorAll(".slide");
  const modal = document.createElement("div");

  modal.id = "siteModal";
  modal.style.cssText = `
    display: none; 
    position: fixed; top: 0; left: 0;
    width: 100%; height: 100%; 
    background: rgba(0,0,0,0.95);
    justify-content: center; align-items: center; 
    z-index: 9999;
    opacity: 0;
    transition: opacity 0.3s ease;
  `;

  // Добавляем CSS с медиа-запросами
  const modalStyles = document.createElement('style');
  modalStyles.textContent = `
    /* Смартфоны (портретная ориентация) */
    @media (max-width: 480px) and (orientation: portrait) {
      #modalContent {
        width: 98vw !important;
        height: 85vh !important;
        border-radius: 4px !important;
      }
      #closeModal {
        top: 10px !important;
        right: 10px !important;
        padding: 8px 12px !important;
        font-size: 20px !important;
      }
      #loadingOverlay {
        width: 280px !important;
        font-size: 16px !important;
      }
    }

    /* Смартфоны (альбомная ориентация) */
    @media (max-width: 768px) and (orientation: landscape) {
      #modalContent {
        width: 96vw !important;
        height: 88vh !important;
        border-radius: 6px !important;
      }
      #closeModal {
        top: 15px !important;
        right: 15px !important;
        padding: 10px 14px !important;
        font-size: 22px !important;
      }
    }

    /* Планшеты */
    @media (min-width: 481px) and (max-width: 1024px) {
      #modalContent {
        width: 94vw !important;
        height: 90vh !important;
        border-radius: 8px !important;
      }
      #closeModal {
        top: 20px !important;
        right: 20px !important;
        padding: 12px 16px !important;
        font-size: 24px !important;
      }
      #loadingOverlay {
        width: 320px !important;
        font-size: 18px !important;
      }
    }

    /* Ноутбуки и небольшие десктопы */
    @media (min-width: 1025px) and (max-width: 1440px) {
      #modalContent {
        width: 90vw !important;
        height: 92vh !important;
        max-width: 1200px !important;
        border-radius: 10px !important;
      }
      #closeModal {
        top: 15px !important;
        right: 15px !important;
        padding: 8px 12px !important;
        font-size: 20px !important;
      }
      #loadingOverlay {
        width: 350px !important;
        font-size: 20px !important;
      }
    }

    /* Большие мониторы 2K */
    @media (min-width: 1441px) and (max-width: 2560px) {
      #modalContent {
        width: 85vw !important;
        height: 90vh !important;
        max-width: 1600px !important;
        border-radius: 12px !important;
      }
      #closeModal {
        top: 20px !important;
        right: 20px !important;
        padding: 10px 14px !important;
        font-size: 22px !important;
      }
      #loadingOverlay {
        width: 400px !important;
        font-size: 22px !important;
      }
    }

    /* 4K и ультра-широкие мониторы */
    @media (min-width: 2561px) {
      #modalContent {
        width: 75vw !important;
        height: 85vh !important;
        max-width: 2000px !important;
        border-radius: 16px !important;
      }
      #closeModal {
        top: 25px !important;
        right: 25px !important;
        padding: 12px 16px !important;
        font-size: 24px !important;
      }
      #loadingOverlay {
        width: 450px !important;
        font-size: 24px !important;
      }
    }

    /* Дополнительные стили для улучшения отображения */
    #modalContent {
      transition: all 0.3s ease;
    }
    
    #closeModal {
      transition: all 0.3s ease, opacity 0.3s ease;
    }
    
    #closeModal:hover {
      background: #333 !important;
      transform: scale(1.1);
    }

    #progressContainer {
      border-radius: 4px;
      overflow: hidden;
    }
  `;
  document.head.appendChild(modalStyles);

  modal.innerHTML = `
    <div id="modalContent" style="
      position: relative; 
      width: 90vw; 
      height: 92vh; 
      max-width: 1200px;
      background: #000; 
      border-radius: 10px; 
      overflow: hidden;
      box-shadow: 0 0 20px rgba(0,0,0,0.8);
    ">
      <iframe src="" style="
        width:100%; height:100%; border:none; 
        opacity: 0; transition: opacity 0.5s ease;
      " id="modalIframe" allowfullscreen></iframe>
    </div>

    <div id="loadingOverlay" style="
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 350px;
      text-align: center;
      z-index: 10001;
      user-select: none;
      color: white;
      font-family: Arial, sans-serif;
      font-weight: 600;
      font-size: 20px;
    ">
      <div id="loadingText" style="margin-bottom: 12px;">Loading</div>
      <div id="progressContainer" style="
        width: 100%;
        height: 8px;
        background: #333;
        border-radius: 4px;
        overflow: hidden;
      ">
        <div id="progressBar" style="
          width: 0%;
          height: 100%;
          background: #4A6CF7;
          border-radius: 4px;
          transition: width 0.2s ease;
        "></div>
      </div>
    </div>
  `;

  document.body.appendChild(modal);

  // Создаем крестик ЗА пределами модалки и добавляем в body
  const closeBtn = document.createElement("button");
  closeBtn.id = "closeModal";
  closeBtn.textContent = "✖";
  closeBtn.style.cssText = `
    display: none;
    position: fixed;
    top: 15px;
    right: 15px;
    background: #000;
    color: #fff;
    border: none;
    padding: 8px 12px;
    font-size: 20px;
    cursor: pointer;
    border-radius: 50%;
    user-select: none;
    box-shadow: 0 0 12px rgba(0,0,0,0.9);
    z-index: 10002;
    opacity: 0;
    transition: opacity 0.3s ease;
  `;
  document.body.appendChild(closeBtn);

  const iframe = modal.querySelector("#modalIframe");
  const progressBar = modal.querySelector("#progressBar");
  const loadingOverlay = modal.querySelector("#loadingOverlay");

  // Анимация прогресса
  function animateProgress() {
    let width = 0;
    const interval = setInterval(() => {
      if (width >= 90) {
        clearInterval(interval);
      } else {
        width += 5;
        progressBar.style.width = width + '%';
      }
    }, 200);
    return interval;
  }

  let progressInterval;

  slides.forEach(slide => {
    slide.addEventListener("click", function (e) {
      e.preventDefault();

      progressBar.style.width = '0%';
      iframe.style.opacity = '0';
      loadingOverlay.style.display = 'block';
      modal.style.display = 'flex';
      closeBtn.style.display = 'block';

      requestAnimationFrame(() => {
        modal.style.opacity = '1';
        closeBtn.style.opacity = '1';
      });

      progressInterval = animateProgress();

      const targetUrl = this.href;
      const encodedUrl = encodeURIComponent(targetUrl);
      iframe.src = `/wp-admin/admin-ajax.php?action=proxy_site&url=${encodedUrl}`;
    });
  });

  iframe.addEventListener("load", () => {
    clearInterval(progressInterval);
    progressBar.style.width = '100%';

    loadingOverlay.style.display = 'none';

    iframe.style.opacity = '1';
  });

  function closeModal() {
    modal.style.opacity = '0';
    closeBtn.style.opacity = '0';
    setTimeout(() => {
      modal.style.display = 'none';
      closeBtn.style.display = 'none';
      iframe.src = "";
      progressBar.style.width = '0%';
      loadingOverlay.style.display = 'none';
    }, 300);
  }

  closeBtn.addEventListener("click", closeModal);

  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });
});