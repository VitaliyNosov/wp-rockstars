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

  modal.innerHTML = `
    <div id="modalContent" style="
      position: relative; 
      width: 95vw; 
      height: 90vh; 
      max-width: 1200px;
      background: #000; 
      border-radius: 6px; 
      overflow: hidden;
      box-shadow: 0 0 15px rgba(0,0,0,0.7);
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
      width: 300px;
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
    position: fixed;
    top: 5vh;
    right: 2vw;
    background: #000;
    color: #fff;
    border: none;
    padding: 10px 15px;
    font-size: 28px;
    cursor: pointer;
    border-radius: 50%;
    user-select: none;
    box-shadow: 0 0 8px rgba(0,0,0,0.8);
    z-index: 10002;
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
