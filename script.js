let slideIndex = 0;
showSlides();

function showSlides() {
  const slides = document.getElementsByClassName("slide");
  for (let i = 0; i < slides.length; i++) {
    slides[i].style.display = "none"; // Ẩn tất cả các slide
  }
  slideIndex++;
  if (slideIndex > slides.length) {
    slideIndex = 1;
  } // Quay lại slide đầu tiên
  slides[slideIndex - 1].style.display = "block"; // Hiện slide hiện tại
  setTimeout(showSlides, 5000); // Chuyển slide sau mỗi 3 giây
}

function changeSlide(n) {
  slideIndex += n; // Thay đổi chỉ số slide
  const slides = document.getElementsByClassName("slide");
  if (slideIndex > slides.length) {
    slideIndex = 1;
  }
  if (slideIndex < 1) {
    slideIndex = slides.length;
  }
  for (let i = 0; i < slides.length; i++) {
    slides[i].style.display = "none"; // Ẩn tất cả các slide
  }
  slides[slideIndex - 1].style.display = "block"; // Hiện slide hiện tại
}

document.addEventListener("DOMContentLoaded", () => {
  const carouselContent = document.querySelector(".container_2");
  const leftArrow = document.querySelector(".left-arrow");
  const rightArrow = document.querySelector(".right-arrow");

  if (!carouselContent || !leftArrow || !rightArrow) {
    console.error("Một trong các phần tử không tìm thấy");
    return;
  }

  let currentIndex = 0;
  const items = document.querySelectorAll(".book-item");
  const totalItems = items.length;
  const itemWidth = 220; // 250px width + 20px gap
  const itemsToShow = 5; // Số lượng items muốn hiển thị

  // Tính toán tổng số bước tối đa mà carousel có thể di chuyển
  const maxSteps = Math.max(totalItems - itemsToShow, 0); // Đảm bảo không có giá trị âm

  function updateCarousel() {
    const offset = currentIndex * itemWidth;
    carouselContent.style.transform = `translateX(-${offset}px)`;
  }

  // Xử lý khi nhấn nút left
  leftArrow.addEventListener("click", () => {
    if (currentIndex > 0) {
      currentIndex--;
      updateCarousel();
    }
  });

  // Xử lý khi nhấn nút right
  rightArrow.addEventListener("click", () => {
    if (currentIndex < maxSteps) {
      currentIndex++;
      updateCarousel();
    }
  });

  // Khởi tạo vị trí ban đầu
  updateCarousel();
});

// Thiết lập thời gian kết thúc (thay đổi ngày giờ theo ý muốn)
const endDate = new Date("2024-11-07T24:00:00").getTime();

// Cập nhật đồng hồ đếm ngược mỗi giây
const countdown = setInterval(() => {
  const now = new Date().getTime();
  const distance = endDate - now;

  // Tính toán thời gian còn lại

  const hours = Math.floor(
    (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
  );
  const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((distance % (1000 * 60)) / 1000);

  // Hiển thị thời gian còn lại

  document.getElementById("hours").innerText = hours
    .toString()
    .padStart(2, "0");
  document.getElementById("minutes").innerText = minutes
    .toString()
    .padStart(2, "0");
  document.getElementById("seconds").innerText = seconds
    .toString()
    .padStart(2, "0");

  // Nếu đồng hồ đếm ngược kết thúc
  if (distance < 0) {
    clearInterval(countdown);
    document.querySelector(".countdown").innerHTML = "Đã kết thúc!";
  }
}, 1000);

document.addEventListener("DOMContentLoaded", () => {
  const text1 = document.getElementById("tieu_de_xu_huong_1");
  const text2 = document.getElementById("tieu_de_xu_huong_2");
  const text3 = document.getElementById("tieu_de_xu_huong_3");
  const list1 = document.getElementById("list_1");
  const list2 = document.getElementById("list_2");
  const list3 = document.getElementById("list_3");

  console.log("List 1:", list1);
  console.log("List 2:", list2);

  // Ẩn tất cả các danh sách ban đầu
  list1.style.display = "flex";
  list2.style.display = "none";
  list3.style.display = "none";
  text1.style.color = "rgb(141, 17, 17)";
  text1.style.textDecoration = "underline";
  text1.style.textDecorationThickness = "1px";
  text2.style.color = "#000000";
  text3.style.color = "#000000";

  // Biến để theo dõi danh sách nào đang được hiển thị
  let activeList = list1;

  text1.addEventListener("click", () => {
    if (activeList !== list1) {
      // Chỉ xử lý khi list1 không phải là danh sách đang active
      list1.style.display = "flex";
      list2.style.display = "none";
      list3.style.display = "none";
      activeList = list1;
      // Thêm style cho text khi được chọn
      text1.style.color = "rgb(141, 17, 17)";
      text1.style.textDecoration = "underline";
      text1.style.textDecorationThickness = "1px";
      text2.style.color = "#000000";
      text2.style.textDecoration = "none";
      text3.style.color = "#000000";
      text3.style.textDecoration = "none";
      console.log("List 1 display:", list1.style.display);
      console.log("List 2 display:", list2.style.display);
    }
  });

  text2.addEventListener("click", () => {
    if (activeList !== list2) {
      // Chỉ xử lý khi list2 không phải là danh sách đang active
      list2.style.display = "flex";
      list1.style.display = "none";
      list3.style.display = "none";
      activeList = list2;
      // Thêm style cho text khi được chọn
      text2.style.color = "rgb(141, 17, 17)";
      text2.style.textDecoration = "underline";
      text2.style.textDecorationThickness = "1px";

      text1.style.color = "#000000";
      text1.style.textDecoration = "none";
      text3.style.color = "#000000";
      text3.style.textDecoration = "none";
      console.log("List 1 display:", list1.style.display);
      console.log("List 2 display:", list2.style.display);
    }
  });
  text3.addEventListener("click", () => {
    if (activeList !== list3) {
      // Chỉ xử lý khi list2 không phải là danh sách đang active
      list3.style.display = "flex";
      list1.style.display = "none";
      list2.style.display = "none";
      activeList = list3;
      // Thêm style cho text khi được chọn
      text3.style.color = "rgb(141, 17, 17)";
      text3.style.textDecoration = "underline";
      text3.style.textDecorationThickness = "1px";
      text1.style.color = "#000000";
      text1.style.textDecoration = "none";
      text2.style.color = "#000000";
      text2.style.textDecoration = "none";
      console.log("List 1 display:", list1.style.display);
      console.log("List 2 display:", list2.style.display);
    }
  });
});
const leftArrow = document.querySelector(".left-arrows");
const rightArrow = document.querySelector(".right-arrows");
const container = document.querySelector(".bestseller_container");

// Dịch chuyển sang trái
leftArrow.addEventListener("click", () => {
  container.scrollBy({ left: -200, behavior: "smooth" }); // Dịch sang trái 200px
});

// Dịch chuyển sang phải
rightArrow.addEventListener("click", () => {
  container.scrollBy({ left: 200, behavior: "smooth" }); // Dịch sang phải 200px
});

document.addEventListener("DOMContentLoaded", function () {
  const ebookSlider = document.querySelector(".ebook-slider");
  const leftArrow = document.querySelector(".left_arrows");
  const rightArrow = document.querySelector(".right_arrows");

  let scrollAmount = 0;
  const scrollStep = 200; // Điều chỉnh khoảng cách scroll

  leftArrow.addEventListener("click", function () {
    scrollAmount = Math.max(0, scrollAmount - scrollStep);
    ebookSlider.scrollTo({
      left: scrollAmount,
      behavior: "smooth",
    });
  });

  rightArrow.addEventListener("click", function () {
    scrollAmount = Math.min(
      ebookSlider.scrollWidth - ebookSlider.clientWidth,
      scrollAmount + scrollStep
    );
    ebookSlider.scrollTo({
      left: scrollAmount,
      behavior: "smooth",
    });
  });
});

