document.addEventListener("DOMContentLoaded", function () {
    // Your JavaScript code that accesses the "quantity" element here

    //for quantity button
    var input = document.getElementById("quantity");
    var minusButton = document.getElementById("minusButton");
    var plusButton = document.getElementById("plusButton");

    minusButton.addEventListener("click", minus);
    plusButton.addEventListener("click", plus);

    function minus() {
        var quantity = input.value;
        if (quantity > 1) {
            quantity = parseInt(quantity) - 1;
            input.value = quantity;
        }
    }

    function plus() {
        var quantity = input.value;
        if (quantity < 100) {
            quantity = parseInt(quantity) + 1;
            input.value = quantity;
        }
    }

    //for add_to_cart button
    var cartButton = document.getElementById("cartButton");
    cartButton.addEventListener("click", handleButton);

    function handleButton() {
        var productId = cartButton.getAttribute("data-productid");
        var quantity = input.value; // Use the updated quantity value
        var xhr2 = new XMLHttpRequest();

        xhr2.open("POST", "/addToCart", true);
        xhr2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        // Add the CSRF token to the request headers
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        xhr2.setRequestHeader('X-CSRF-TOKEN', csrfToken);

        xhr2.onreadystatechange = function () {
            if (xhr2.readyState === XMLHttpRequest.DONE) {
                if (xhr2.status === 200) {
                    var response = xhr2.responseText;
                    if (response.includes('<!DOCTYPE html>')) {
                        // Response contains HTML, meaning it is the login view
                        document.open();
                        document.write(response);
                        document.close();
                    } else {
                        console.log(response);
                    }
                } else {
                    console.error("Error: " + xhr2.status);
                }
            }
        }
        xhr2.send("productid=" + productId + "&quantity=" + quantity);
    }

});