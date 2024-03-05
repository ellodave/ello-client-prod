// Auto Anchor

// Wrap the code in an event listener that runs after the DOM has loaded
document.addEventListener("DOMContentLoaded", function () {
    // Get all the heading elements
    const headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');

    // Iterate through the headings
    headings.forEach(heading => {
        // Get the text content of the heading
        const headingText = heading.textContent;

        // Remove leading and trailing whitespace from the text, remove punctuation, replace spaces with dashes, and convert to lowercase
        const cleanedText = headingText.trim().replace(/^[^\w\s-]+/g, '').replace(/[^\w\s-]/g, '').replace(/\s+/g, '-').toLowerCase();

        // Set the id attribute of the heading to the cleaned text
        heading.id = cleanedText;
    });
});

// Auto image attributes
// Function to add loading="lazy" and inline CSS to all <img> tags
function enhanceImageRender() {
    const images = document.querySelectorAll('img');
    images.forEach(image => {
        // Add loading="lazy" attribute
        image.setAttribute('loading', 'lazy');

        // Add inline CSS
        image.style.transform = "scale(1)";
        image.style.transformOrigin = "50% 50%";
        image.style.objectPosition = "50% 50%";
        image.style.objectFit = "cover";
    });
}

// Call the function after the DOM has loaded
document.addEventListener("DOMContentLoaded", function () {
    enhanceImageRender();
});
