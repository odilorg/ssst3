/**
 * BLOG ARTICLE PAGE JAVASCRIPT
 * Jahongir Travel
 * Purpose: Interactive features for blog article pages
 */

// ===================================
// 1. Reading Time Calculator
// ===================================
function calculateReadingTime() {
  const articleContent = document.querySelector('.article-content');
  if (!articleContent) return;

  const text = articleContent.textContent;
  const wordsPerMinute = 200; // Average reading speed
  const wordCount = text.trim().split(/\s+/).length;
  const readingTime = Math.ceil(wordCount / wordsPerMinute);

  const readingTimeElement = document.querySelector('.article-reading-time');
  if (readingTimeElement) {
    // Remove the existing content (including emoji)
    readingTimeElement.textContent = `${readingTime} min read`;

    // Re-add the emoji
    const emoji = document.createElement('span');
    emoji.textContent = '⏱️';
    emoji.style.marginRight = '0.375rem';
    readingTimeElement.insertBefore(emoji, readingTimeElement.firstChild);
  }
}

// ===================================
// 2. Comment Form Validation
// ===================================
const commentForm = document.getElementById('commentForm');

if (commentForm) {
  // Get form elements
  const commentField = document.getElementById('comment');
  const nameField = document.getElementById('name');
  const emailField = document.getElementById('email');
  const websiteField = document.getElementById('website');

  // Get error message elements
  const commentError = document.getElementById('comment-error');
  const nameError = document.getElementById('name-error');
  const emailError = document.getElementById('email-error');
  const websiteError = document.getElementById('website-error');

  // Validation functions
  function validateComment(value) {
    if (!value || value.trim().length < 10) {
      return 'Comment must be at least 10 characters long';
    }
    return '';
  }

  function validateName(value) {
    if (!value || value.trim().length < 2) {
      return 'Please enter a valid name';
    }
    return '';
  }

  function validateEmail(value) {
    if (!value) {
      return 'Email is required';
    }
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
      return 'Please enter a valid email address';
    }
    return '';
  }

  function validateWebsite(value) {
    if (value && value.trim() !== '') {
      const urlRegex = /^https?:\/\/.+/;
      if (!urlRegex.test(value)) {
        return 'Please enter a valid URL starting with http:// or https://';
      }
    }
    return '';
  }

  // Display error message
  function showError(element, message) {
    if (element) {
      element.textContent = message;
      element.style.display = message ? 'block' : 'none';
    }
  }

  // Clear error message
  function clearError(element) {
    if (element) {
      element.textContent = '';
      element.style.display = 'none';
    }
  }

  // Real-time validation on blur
  if (commentField) {
    commentField.addEventListener('blur', () => {
      const error = validateComment(commentField.value);
      showError(commentError, error);
    });

    commentField.addEventListener('input', () => {
      if (commentError.textContent) {
        clearError(commentError);
      }
    });
  }

  if (nameField) {
    nameField.addEventListener('blur', () => {
      const error = validateName(nameField.value);
      showError(nameError, error);
    });

    nameField.addEventListener('input', () => {
      if (nameError.textContent) {
        clearError(nameError);
      }
    });
  }

  if (emailField) {
    emailField.addEventListener('blur', () => {
      const error = validateEmail(emailField.value);
      showError(emailError, error);
    });

    emailField.addEventListener('input', () => {
      if (emailError.textContent) {
        clearError(emailError);
      }
    });
  }

  if (websiteField) {
    websiteField.addEventListener('blur', () => {
      const error = validateWebsite(websiteField.value);
      showError(websiteError, error);
    });

    websiteField.addEventListener('input', () => {
      if (websiteError.textContent) {
        clearError(websiteError);
      }
    });
  }

  // Form submission
  commentForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Validate all fields
    const commentErrorMsg = validateComment(commentField.value);
    const nameErrorMsg = validateName(nameField.value);
    const emailErrorMsg = validateEmail(emailField.value);
    const websiteErrorMsg = validateWebsite(websiteField.value);

    // Show all errors
    showError(commentError, commentErrorMsg);
    showError(nameError, nameErrorMsg);
    showError(emailError, emailErrorMsg);
    showError(websiteError, websiteErrorMsg);

    // Check if form is valid
    if (commentErrorMsg || nameErrorMsg || emailErrorMsg || websiteErrorMsg) {
      // Scroll to first error
      const firstError = document.querySelector('.form-error:not(:empty)');
      if (firstError) {
        firstError.previousElementSibling?.focus();
      }
      return;
    }

    // Collect form data
    const formData = {
      comment: commentField.value,
      name: nameField.value,
      email: emailField.value,
      website: websiteField.value || '',
      saveInfo: document.getElementById('save-info')?.checked || false,
      timestamp: new Date().toISOString(),
      articleUrl: window.location.href
    };

    // Get submit button
    const submitButton = commentForm.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.textContent;

    try {
      // Disable button during submission
      submitButton.disabled = true;
      submitButton.textContent = 'Posting...';

      // Simulate API call (replace with actual endpoint)
      console.log('Comment data:', formData);

      // Simulate network delay
      await new Promise(resolve => setTimeout(resolve, 1000));

      // Show success message
      alert('Thank you for your comment! It will be published after review.');

      // Reset form
      commentForm.reset();

      // Clear all errors
      clearError(commentError);
      clearError(nameError);
      clearError(emailError);
      clearError(websiteError);

      // Scroll to top of form
      commentForm.scrollIntoView({ behavior: 'smooth', block: 'start' });

    } catch (error) {
      console.error('Error submitting comment:', error);
      alert('Sorry, there was an error submitting your comment. Please try again.');
    } finally {
      // Re-enable button
      submitButton.disabled = false;
      submitButton.textContent = originalButtonText;
    }
  });
}

// ===================================
// 3. Smooth Scroll to Comments
// ===================================
function setupCommentLinks() {
  const commentLinks = document.querySelectorAll('a[href="#comments"]');

  commentLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const commentsSection = document.querySelector('.article-comments');

      if (commentsSection) {
        const headerOffset = parseInt(
          getComputedStyle(document.documentElement)
            .getPropertyValue('--sticky-offset')
            .trim()
        ) || 88;

        const elementPosition = commentsSection.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

        window.scrollTo({
          top: offsetPosition,
          behavior: 'smooth'
        });
      }
    });
  });
}

// ===================================
// 4. Social Share Buttons (Optional)
// ===================================
function shareOnSocial(platform) {
  const url = encodeURIComponent(window.location.href);
  const title = encodeURIComponent(document.title);
  const description = encodeURIComponent(
    document.querySelector('meta[name="description"]')?.content || ''
  );

  const shareUrls = {
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${url}`,
    twitter: `https://twitter.com/intent/tweet?url=${url}&text=${title}`,
    linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${url}`,
    whatsapp: `https://wa.me/?text=${title}%20${url}`,
    telegram: `https://t.me/share/url?url=${url}&text=${title}`
  };

  if (shareUrls[platform]) {
    window.open(
      shareUrls[platform],
      '_blank',
      'width=600,height=400,menubar=no,toolbar=no,location=no'
    );
  }
}

// Make shareOnSocial available globally if needed
window.shareOnSocial = shareOnSocial;

// ===================================
// 5. Image Lazy Loading Enhancement
// ===================================
function enhanceImageLoading() {
  // Add loading state to images
  const images = document.querySelectorAll('.article-content img, .related-article-card img');

  images.forEach(img => {
    if (img.complete) {
      img.classList.add('loaded');
    } else {
      img.addEventListener('load', () => {
        img.classList.add('loaded');
      });
    }
  });
}

// ===================================
// 6. Sidebar Widget Interactions
// ===================================
function setupSidebarInteractions() {
  // Add focus trap for search form
  const searchForm = document.querySelector('.search-form');

  if (searchForm) {
    const searchInput = searchForm.querySelector('input[type="search"]');
    const searchButton = searchForm.querySelector('button');

    searchForm.addEventListener('submit', (e) => {
      if (!searchInput.value.trim()) {
        e.preventDefault();
        searchInput.focus();
        alert('Please enter a search term');
      }
    });
  }
}

// ===================================
// 7. Print Functionality
// ===================================
function setupPrintStyles() {
  // Add print button if needed
  const articleContent = document.querySelector('.article-content');

  if (articleContent) {
    window.addEventListener('beforeprint', () => {
      console.log('Preparing to print article...');
    });

    window.addEventListener('afterprint', () => {
      console.log('Print complete');
    });
  }
}

// ===================================
// 8. Scroll Progress Indicator (Optional)
// ===================================
function addScrollProgressIndicator() {
  const article = document.querySelector('.article-content');
  if (!article) return;

  // Create progress bar
  const progressBar = document.createElement('div');
  progressBar.className = 'scroll-progress-bar';
  progressBar.style.cssText = `
    position: fixed;
    top: var(--sticky-offset, 88px);
    left: 0;
    width: 0%;
    height: 3px;
    background: var(--brand-blue, #1C54B2);
    z-index: 99;
    transition: width 0.1s ease;
  `;
  document.body.appendChild(progressBar);

  // Update progress on scroll
  window.addEventListener('scroll', () => {
    const articleTop = article.offsetTop;
    const articleHeight = article.offsetHeight;
    const viewportHeight = window.innerHeight;
    const scrollTop = window.pageYOffset;

    const articleStart = articleTop - viewportHeight;
    const articleEnd = articleTop + articleHeight;
    const scrollProgress = (scrollTop - articleStart) / (articleEnd - articleStart);

    const progress = Math.max(0, Math.min(100, scrollProgress * 100));
    progressBar.style.width = `${progress}%`;
  });
}

// ===================================
// Initialize on Page Load
// ===================================
document.addEventListener('DOMContentLoaded', () => {
  console.log('Blog article page loaded');

  // Core functionality
  calculateReadingTime();
  setupCommentLinks();
  enhanceImageLoading();
  setupSidebarInteractions();
  setupPrintStyles();

  // Optional: Uncomment if you want scroll progress indicator
  // addScrollProgressIndicator();

  console.log('All article features initialized');
});

// ===================================
// Handle Window Resize
// ===================================
let resizeTimer;
window.addEventListener('resize', () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    console.log('Window resized - recalculating layouts');
  }, 250);
});
