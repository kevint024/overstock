document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('add-product-form');

    form.addEventListener('submit', function(event) {
    

        // Extract form data
        const productName = document.getElementById('product-name').value.trim();
        const category = document.getElementById('category').value.trim();
        const originalPrice = parseFloat(document.getElementById('original-price').value);
        const discountPrice = parseFloat(document.getElementById('discount-price').value);
        const stockQuantity = parseInt(document.getElementById('stock-quantity').value, 10);

        // Basic validation checks
        if (!productName || !category || !originalPrice || stockQuantity < 0) {
            alert('Please fill in all the required fields with valid values.');
            return;
        }

        if (discountPrice >= originalPrice) {
            alert('Discount price should be less than the original price.');
            return;
        }

        // For now, just alert success message (later this will send to backend)
        alert(`Product ${productName} added successfully!`);

        // TODO: Send data to the backend for storage in the database
    });
});
