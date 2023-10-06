document.addEventListener("DOMContentLoaded", function () {

    // --------------------------Function to handle quantity increment--------------------------------------------

    function incrementQuantity(productId) {
        console.log("plus button with productid= " + productId);
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    const quantity = response.quantity;
                    const grandTotal = response.grand_total;
                    console.log('new quantity: ' + quantity);
                    console.log('grand total: ' + grandTotal);
                    input = document.getElementById('cart_quantity_' + productId);
                    input.value = quantity;
                    updateTotal(quantity, productId);
                    document.getElementById('grand_total').textContent = grandTotal;
                } else {
                    console.error('Request failed:', xhr.status);
                }
            }
        };

        xhr.open('POST', '/cart/increment', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        const data = JSON.stringify({ productId: productId }); // Create a JSON object with the productId
        xhr.send(data);
    }

    // --------------------------Function to handle quantity decrement-----------------------------------

    function decrementQuantity(productId) {
        console.log("minus button with productid= " + productId);
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    const quantity = response.quantity;
                    const grandTotal = response.grand_total;
                    console.log('new quantity: ' + quantity);
                    console.log('grand total: ' + grandTotal);
                    input = document.getElementById('cart_quantity_' + productId);
                    input.value = quantity;
                    updateTotal(quantity, productId);
                    document.getElementById('grand_total').textContent = grandTotal;

                } else {
                    console.error('Request failed:', xhr.status);
                }
            }
        };

        xhr.open('POST', '/cart/decrement', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        const data = JSON.stringify({ productId: productId }); // Create a JSON object with the productId
        xhr.send(data);
    }
    //----------------------------------update total function---------------------------------------

    function updateTotal(quantity, productId) {
        const price_element = document.getElementById('price_' + productId);
        const price = parseFloat(price_element.textContent);
        const total = price * parseInt(quantity);

        const total_element = document.getElementById('total_' + productId);
        total_element.textContent = total.toFixed(2);
    }

    // -------------------------Function to handle cart item deletion--------------------------------

    function deleteCartItem(productId) {
        console.log("delete button with productid= " + productId);
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Handle successful deletion
                    console.log("Cart item deleted successfully");
                    //updte the grand total 
                    const total_value = document.getElementById('total_' + productId).textContent;
                    const grand_total = document.getElementById('grand_total').textContent;
                    const new_grand_total = grand_total - total_value;
                    document.getElementById('grand_total').textContent = new_grand_total;
                    // Remove the row from the table
                    const row = document.getElementById('row_' + productId);
                    row.remove();
                } else {
                    console.error('Request failed:', xhr.status);
                }
            }
        };

        xhr.open('POST', '/cart/delete', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        const data = JSON.stringify({ productId: productId }); // Create a JSON object with the productId
        xhr.send(data);
    }
    //-------------------------------checkout function----------------------------------
    function checkout() {
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    const message = response.message;
                    console.log(message); // Display the success message in the console
                    document.getElementById('all_rows').remove();
                } else {
                    console.error('Request failed:', xhr.status);
                }
            }
        };

        xhr.open('POST', '/cart/checkout', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.send();
    }


    //----------------------------------event binding for plus and minus buttons---------------

    const plusButtons = document.querySelectorAll('.fa-plus');
    const minusButtons = document.querySelectorAll('.fa-minus');

    plusButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            const productId = this.closest('.input-group').querySelector('button.minus').getAttribute('data-product-id');
            incrementQuantity(productId);
        });
    });

    minusButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            const productId = this.closest('.input-group').querySelector('button.plus').getAttribute('data-product-id');
            decrementQuantity(productId);
        });
    });

    // ------------------------Event binding for delete buttons----------------------

    const deleteButtons = document.querySelectorAll('.fa-circle-xmark');

    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            const productId = this.closest('button').getAttribute('data-product-id');
            deleteCartItem(productId);
        });
    });

    //-------------------------------for checkout -------------------------------------

    const checkout_button = document.getElementById('checkout');
    checkout_button.addEventListener('click', function () {
        checkout();
    });

});
