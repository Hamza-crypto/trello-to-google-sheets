document.addEventListener('DOMContentLoaded', function() {
    // Attach a click event listener to the pagination links
    document.addEventListener('click', function(e) {
        if (e.target.matches('#pagination-container ul.pagination a')) {
            e.preventDefault();

            var url = e.target.getAttribute('href'); // Get the URL of the clicked page

            // Send an AJAX request to fetch the products for the clicked page
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = xhr.responseText;
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(response, 'text/html');
                    var container = document.querySelector('#products-container'); // Get the products container

                    // Update the products container with the fetched data
                    container.innerHTML = doc.querySelector('#products-container').innerHTML;

                    // Update the pagination container with the fetched pagination links
                    var paginationContainer = document.querySelector('#pagination-container');
                    paginationContainer.innerHTML = doc.querySelector('#pagination-container').innerHTML;
                } else {
                    // Handle error if necessary
                }
            };
            xhr.onerror = function() {
                // Handle error if necessary
            };
            xhr.send();
        }
    });
});
