// Function to update the cart count dynamically
document.addEventListener("DOMContentLoaded", () => {
  // Fetch the cart count when the page loads
  fetch("cart.php?action=cart_count")
    .then((response) => response.json())
    .then((data) => {
      const cartCountEl = document.getElementById("cart-count");
      cartCountEl.textContent = data.cart_count || 0; // Display 0 if no items
    })
    .catch((error) => console.error("Error fetching cart count:", error));
});
