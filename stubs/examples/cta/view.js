/**
 * View script for the block.
 *
 * This file handles any client-side functionality needed for the block
 * on the frontend of the site.
 */

(function() {
    // Select all instances of this block on the page
    const blocks = document.querySelectorAll('.wp-block-vendor-example-block');

    // Function to initialize a block
    function initBlock(block) {
      // Example: Track button clicks
      const buttons = block.querySelectorAll('.wp-block-button__link');

      buttons.forEach(function(button) {
        button.addEventListener('click', function(e) {
          // Add your tracking or animation logic here
          // Example: console.log('CTA button clicked:', this.textContent);
        });
      });
    }

    // Initialize each block found
    if (blocks.length) {
      blocks.forEach(function(block) {
        initBlock(block);
      });
    }
  })();
