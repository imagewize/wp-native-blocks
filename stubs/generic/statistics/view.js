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
      // Add your frontend JavaScript here
      // Example: Add counter animation for statistics
      // const stats = block.querySelectorAll('h3');
      // stats.forEach(function(stat) {
      //   // Add animation logic
      // });
    }

    // Initialize each block found
    if (blocks.length) {
      blocks.forEach(function(block) {
        initBlock(block);
      });
    }
  })();
