/**
 * Tour Gallery Lightbox
 * Interactive image gallery with full-screen lightbox viewer
 */

(function() {
  'use strict';

  class TourGalleryLightbox {
    constructor() {
      this.lightbox = null;
      this.images = [];
      this.currentIndex = 0;
      this.isOpen = false;

      this.init();
    }

    init() {
      // Wait for gallery data to be available
      this.waitForGalleryData();

      // Create lightbox HTML
      this.createLightbox();

      // Bind events
      this.bindEvents();
    }

    waitForGalleryData() {
      // Check if gallery data exists
      const checkData = () => {
        const galleryDataScript = document.getElementById('gallery-data');
        if (galleryDataScript) {
          try {
            const data = JSON.parse(galleryDataScript.textContent);
            this.images = [
              { src: data.heroImage, alt: 'Hero Image' },
              ...data.images
            ];
            this.attachClickHandlers();
            console.log('[Gallery] Loaded', this.images.length, 'images');
          } catch (e) {
            console.error('[Gallery] Failed to parse gallery data:', e);
          }
        } else {
          // Retry after a short delay if data not yet available
          setTimeout(checkData, 100);
        }
      };

      checkData();
    }

    createLightbox() {
      const lightboxHTML = `
        <div class="tour-gallery-lightbox" id="tourGalleryLightbox" role="dialog" aria-modal="true" aria-label="Image gallery">
          <div class="lightbox-content">
            <div class="lightbox-loader"></div>
            <img class="lightbox-image" src="" alt="" id="lightboxImage">

            <button class="lightbox-close" id="lightboxClose" aria-label="Close lightbox">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>

            <button class="lightbox-nav lightbox-prev" id="lightboxPrev" aria-label="Previous image">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>

            <button class="lightbox-nav lightbox-next" id="lightboxNext" aria-label="Next image">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>

            <div class="lightbox-info">
              <div class="lightbox-counter" id="lightboxCounter">1 / 1</div>
              <div class="lightbox-caption" id="lightboxCaption"></div>
            </div>
          </div>
        </div>
      `;

      document.body.insertAdjacentHTML('beforeend', lightboxHTML);
      this.lightbox = document.getElementById('tourGalleryLightbox');
    }

    attachClickHandlers() {
      // Hero image click handler
      const heroImage = document.getElementById('main-gallery-image');
      if (heroImage) {
        heroImage.addEventListener('click', (e) => {
          e.preventDefault();
          this.open(0);
        });
        heroImage.style.cursor = 'pointer';
      }

      // Thumbnail click handlers
      const thumbnails = document.querySelectorAll('.thumbnail');
      thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', (e) => {
          e.preventDefault();
          // Add 1 to index because hero image is at index 0
          this.open(index + 1);
        });
      });

      console.log('[Gallery] Attached click handlers to', thumbnails.length + 1, 'images');
    }

    bindEvents() {
      // Close button
      const closeBtn = document.getElementById('lightboxClose');
      if (closeBtn) {
        closeBtn.addEventListener('click', () => this.close());
      }

      // Navigation buttons
      const prevBtn = document.getElementById('lightboxPrev');
      const nextBtn = document.getElementById('lightboxNext');

      if (prevBtn) {
        prevBtn.addEventListener('click', () => this.prev());
      }

      if (nextBtn) {
        nextBtn.addEventListener('click', () => this.next());
      }

      // Click outside to close
      if (this.lightbox) {
        this.lightbox.addEventListener('click', (e) => {
          if (e.target === this.lightbox) {
            this.close();
          }
        });
      }

      // Keyboard navigation
      document.addEventListener('keydown', (e) => {
        if (!this.isOpen) return;

        switch(e.key) {
          case 'Escape':
            this.close();
            break;
          case 'ArrowLeft':
            this.prev();
            break;
          case 'ArrowRight':
            this.next();
            break;
        }
      });

      // Prevent scroll when lightbox is open
      this.lightbox.addEventListener('wheel', (e) => {
        e.preventDefault();
      }, { passive: false });
    }

    open(index) {
      if (index < 0 || index >= this.images.length) return;

      this.currentIndex = index;
      this.isOpen = true;

      // Add class to body to prevent scrolling
      document.body.classList.add('lightbox-open');

      // Show lightbox
      this.lightbox.classList.add('active');

      // Load image
      this.loadImage();

      // Update UI
      this.updateUI();

      console.log('[Gallery] Opened lightbox at index', index);
    }

    close() {
      this.isOpen = false;

      // Remove body class
      document.body.classList.remove('lightbox-open');

      // Hide lightbox
      this.lightbox.classList.remove('active');

      console.log('[Gallery] Closed lightbox');
    }

    prev() {
      if (this.currentIndex > 0) {
        this.currentIndex--;
        this.loadImage();
        this.updateUI();
      }
    }

    next() {
      if (this.currentIndex < this.images.length - 1) {
        this.currentIndex++;
        this.loadImage();
        this.updateUI();
      }
    }

    loadImage() {
      const img = document.getElementById('lightboxImage');
      const loader = this.lightbox.querySelector('.lightbox-loader');
      const currentImage = this.images[this.currentIndex];

      // Show loader
      if (loader) loader.style.display = 'block';

      // Remove loaded class for animation
      img.classList.remove('loaded');

      // Load new image
      const tempImg = new Image();
      tempImg.onload = () => {
        img.src = currentImage.src;
        img.alt = currentImage.alt || '';

        // Hide loader
        if (loader) loader.style.display = 'none';

        // Show image with animation
        setTimeout(() => {
          img.classList.add('loaded');
        }, 50);
      };

      tempImg.onerror = () => {
        console.error('[Gallery] Failed to load image:', currentImage.src);
        if (loader) loader.style.display = 'none';
      };

      tempImg.src = currentImage.src;
    }

    updateUI() {
      // Update counter
      const counter = document.getElementById('lightboxCounter');
      if (counter) {
        counter.textContent = `${this.currentIndex + 1} / ${this.images.length}`;
      }

      // Update caption
      const caption = document.getElementById('lightboxCaption');
      if (caption) {
        caption.textContent = this.images[this.currentIndex].alt || '';
      }

      // Update navigation button states
      const prevBtn = document.getElementById('lightboxPrev');
      const nextBtn = document.getElementById('lightboxNext');

      if (prevBtn) {
        if (this.currentIndex === 0) {
          prevBtn.classList.add('disabled');
        } else {
          prevBtn.classList.remove('disabled');
        }
      }

      if (nextBtn) {
        if (this.currentIndex === this.images.length - 1) {
          nextBtn.classList.add('disabled');
        } else {
          nextBtn.classList.remove('disabled');
        }
      }
    }
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      new TourGalleryLightbox();
    });
  } else {
    new TourGalleryLightbox();
  }

})();
