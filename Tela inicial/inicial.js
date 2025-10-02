const sections = document.querySelectorAll("section, header"); 
const navLinks = document.querySelectorAll(".navbar-nav .nav-link");

window.addEventListener("load", () => {
  navLinks.forEach(link => link.classList.remove("active"));
  navLinks[0].classList.add("active"); // Home
});

window.addEventListener("scroll", () => {
  let current = "";

  sections.forEach(section => {
    const sectionTop = section.offsetTop - 90; 
    const sectionHeight = section.offsetHeight;
    if (pageYOffset >= sectionTop && pageYOffset < sectionTop + sectionHeight) {
      current = section.getAttribute("id");
    }
  });

  navLinks.forEach(link => {
    link.classList.remove("active");
    if (link.getAttribute("href") === `#${current}`) {
      link.classList.add("active");
    }
  });
});
