// Check if product details page
if (document.querySelector(".product-detail-image")) {
  // Product image zoom effect on hover
  const productImage = document.querySelector(".product-detail-image")

  productImage.addEventListener("mouseover", function () {
    this.style.transform = "scale(1.05)"
    this.style.transition = "transform 0.3s ease"
  })

  productImage.addEventListener("mouseout", function () {
    this.style.transform = "scale(1)"
  })
}

// Add to cart animation
document.addEventListener("DOMContentLoaded", () => {
  const addToCartButtons = document.querySelectorAll('form[action="add_to_cart.php"] button')

  addToCartButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      // Don't prevent form submission

      // Add animation class
      this.classList.add("btn-success")
      this.innerHTML = '<i class="bi bi-check"></i> Added'

      // Reset after animation
      setTimeout(() => {
        this.classList.remove("btn-success")
        this.innerHTML = '<i class="bi bi-cart-plus"></i> Add to Cart'
      }, 1000)
    })
  })
})

// Search form validation
const searchForms = document.querySelectorAll('form[action*="products.php"], form[action*="manage_products.php"]')

searchForms.forEach((form) => {
  form.addEventListener("submit", function (e) {
    const searchInput = this.querySelector('input[name="search"]')
    if (searchInput && searchInput.value.trim() === "") {
      e.preventDefault()
      searchInput.focus()
    }
  })
})

// Mobile menu toggle
document.addEventListener("DOMContentLoaded", () => {
  const navbarToggler = document.querySelector(".navbar-toggler")
  const navbarCollapse = document.querySelector(".navbar-collapse")

  if (navbarToggler && navbarCollapse) {
    navbarToggler.addEventListener("click", () => {
      navbarCollapse.classList.toggle("show")
    })

    // Close menu when clicking outside
    document.addEventListener("click", (e) => {
      if (
        !navbarToggler.contains(e.target) &&
        !navbarCollapse.contains(e.target) &&
        navbarCollapse.classList.contains("show")
      ) {
        navbarCollapse.classList.remove("show")
      }
    })
  }
})
