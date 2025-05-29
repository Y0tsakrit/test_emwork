let autoplay = false;
let intervalId = null;
let scrollIndex = 0;

function toggleAutoplay() {
  const container = document.getElementById('watthai');
  const btnText = document.getElementById('autoplayBtnText');
  const imageWidth = 320;
  autoplay = !autoplay;

  if (autoplay) {
    btnText.textContent = "หยุด Auto Slide";
    intervalId = setInterval(() => {
      scrollIndex++;
      const maxIndex = container.children.length - visibleCount;
      if (scrollIndex > maxIndex) scrollIndex = 0;
      container.scrollTo({
        left: scrollIndex * imageWidth,
        behavior: 'smooth'
      });
    }, 3000);
  } else {
    btnText.textContent = "เริ่ม Auto Slide";
    clearInterval(intervalId);
  }
}

let visibleCount = 3;

document.getElementById('columnSelector').addEventListener('change', (e) => {
  visibleCount = parseInt(e.target.value);
  const watthai = document.getElementById('watthai');
  watthai.style.maxWidth = `calc(300px * ${visibleCount} + 20px * ${visibleCount - 1})`;
  scrollIndex = 0;
  watthai.scrollTo({ left: 0 });
});
