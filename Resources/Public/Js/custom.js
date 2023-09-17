console.log('test');

const star = document.querySelectorAll('.testimonial-star');
star.forEach(element => {
  let starRating = element.getAttribute('data-value');
  for (let index = 0; index < starRating; index++) {
    element.innerHTML += '<i class="fa fa-star checked" style="color: #f6d32d;"></i>';
  }
});
