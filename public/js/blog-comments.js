/**
 * Blog Comments JavaScript
 * Handles comment submission, reply forms, and flagging
 */

(function() {
    'use strict';

    // Configuration
    const config = {
        commentFormSelector: '#commentForm',
        charCountSelector: '#charCount',
        commentTextSelector: '#commentText',
        parentIdSelector: '#parentCommentId',
        cancelReplySelector: '#cancelReply',
        formMessageSelector: '#formMessage',
        submitButtonSelector: '#submitComment',
        maxChars: 2000,
    };

    // State
    let originalFormParent = null;

    /**
     * Initialize comment functionality
     */
    function init() {
        setupCommentForm();
        setupReplyButtons();
        setupFlagButtons();
        setupCharacterCounter();
    }

    /**
     * Setup main comment form submission
     */
    function setupCommentForm() {
        const form = document.querySelector(config.commentFormSelector);
        if (!form) return;

        // Store original form parent for cancel reply
        originalFormParent = form.parentElement;

        form.addEventListener('submit', handleCommentSubmit);
    }

    /**
     * Handle comment form submission
     */
    async function handleCommentSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const submitBtn = form.querySelector(config.submitButtonSelector);
        const formMessage = document.querySelector(config.formMessageSelector);

        // Clear previous messages
        clearFormErrors(form);
        hideMessage(formMessage);

        // Show loading state
        setLoadingState(submitBtn, true);

        // Get form data
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/comments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (response.ok) {
                // Success
                showMessage(formMessage, result.message, 'success');
                form.reset();

                // Reset character count
                const charCount = document.querySelector(config.charCountSelector);
                if (charCount) charCount.textContent = '0';

                // If replying, cancel reply mode
                if (data.parent_id) {
                    cancelReply();
                }

                // Reload comments section after 2 seconds
                setTimeout(() => {
                    reloadComments();
                }, 2000);
            } else if (response.status === 422) {
                // Validation errors
                displayValidationErrors(form, result.errors || {});
                showMessage(formMessage, 'Please fix the errors above.', 'error');
            } else {
                // Other errors
                showMessage(formMessage, result.message || 'Failed to post comment. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Comment submission error:', error);
            showMessage(formMessage, 'Network error. Please check your connection and try again.', 'error');
        } finally {
            setLoadingState(submitBtn, false);
        }
    }

    /**
     * Setup reply button handlers
     */
    function setupReplyButtons() {
        document.addEventListener('click', (e) => {
            const replyBtn = e.target.closest('.reply-btn');
            if (!replyBtn) return;

            e.preventDefault();
            const commentId = replyBtn.dataset.commentId;
            const authorName = replyBtn.dataset.author;

            showReplyForm(commentId, authorName);
        });

        // Cancel reply button
        const cancelBtn = document.querySelector(config.cancelReplySelector);
        if (cancelBtn) {
            cancelBtn.addEventListener('click', cancelReply);
        }
    }

    /**
     * Show reply form for a specific comment
     */
    function showReplyForm(commentId, authorName) {
        const form = document.querySelector(config.commentFormSelector);
        const parentIdInput = document.querySelector(config.parentIdSelector);
        const cancelBtn = document.querySelector(config.cancelReplySelector);
        const commentText = document.querySelector(config.commentTextSelector);
        const formTitle = form.closest('.comment-form-wrapper').querySelector('.comment-form-title');

        if (!form || !parentIdInput) return;

        // Set parent ID
        parentIdInput.value = commentId;

        // Update form title
        if (formTitle) {
            formTitle.textContent = `Reply to ${authorName}`;
        }

        // Show cancel button
        if (cancelBtn) {
            cancelBtn.style.display = 'inline-flex';
        }

        // Move form to reply container
        const replyContainer = document.querySelector(`#reply-container-${commentId}`);
        if (replyContainer) {
            replyContainer.style.display = 'block';
            replyContainer.appendChild(form.closest('.comment-form-wrapper'));
        }

        // Focus on comment text
        if (commentText) {
            commentText.focus();
            // Optionally add @mention
            if (commentText.value === '' || !commentText.value.startsWith('@')) {
                commentText.value = `@${authorName} `;
            }
        }

        // Scroll to form
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    /**
     * Cancel reply and restore form to original position
     */
    function cancelReply() {
        const form = document.querySelector(config.commentFormSelector);
        const parentIdInput = document.querySelector(config.parentIdSelector);
        const cancelBtn = document.querySelector(config.cancelReplySelector);
        const formTitle = form.closest('.comment-form-wrapper').querySelector('.comment-form-title');
        const commentText = document.querySelector(config.commentTextSelector);

        if (!form || !originalFormParent) return;

        // Reset parent ID
        if (parentIdInput) {
            parentIdInput.value = '';
        }

        // Restore form title
        if (formTitle) {
            formTitle.textContent = 'Leave a Comment';
        }

        // Hide cancel button
        if (cancelBtn) {
            cancelBtn.style.display = 'none';
        }

        // Move form back to original position
        originalFormParent.appendChild(form.closest('.comment-form-wrapper'));

        // Clear @mention if present
        if (commentText && commentText.value.startsWith('@')) {
            commentText.value = '';
        }

        // Scroll to form
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    /**
     * Setup flag button handlers
     */
    function setupFlagButtons() {
        document.addEventListener('click', async (e) => {
            const flagBtn = e.target.closest('.flag-btn');
            if (!flagBtn) return;

            e.preventDefault();

            if (!confirm('Are you sure you want to report this comment as inappropriate?')) {
                return;
            }

            const commentId = flagBtn.dataset.commentId;
            const csrfToken = document.querySelector('input[name="_token"]').value;

            // Disable button
            flagBtn.disabled = true;
            flagBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Reporting...';

            try {
                const response = await fetch(`/comments/${commentId}/flag`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const result = await response.json();

                if (response.ok) {
                    alert(result.message || 'Comment has been flagged for review.');
                    flagBtn.innerHTML = '<i class="fas fa-check"></i> Reported';
                } else {
                    alert(result.message || 'Failed to flag comment. Please try again.');
                    flagBtn.disabled = false;
                    flagBtn.innerHTML = '<i class="fas fa-flag"></i> Report';
                }
            } catch (error) {
                console.error('Flag error:', error);
                alert('Network error. Please try again.');
                flagBtn.disabled = false;
                flagBtn.innerHTML = '<i class="fas fa-flag"></i> Report';
            }
        });
    }

    /**
     * Setup character counter for comment textarea
     */
    function setupCharacterCounter() {
        const commentText = document.querySelector(config.commentTextSelector);
        const charCount = document.querySelector(config.charCountSelector);

        if (!commentText || !charCount) return;

        commentText.addEventListener('input', () => {
            const count = commentText.value.length;
            charCount.textContent = count;

            // Change color if approaching limit
            if (count > config.maxChars * 0.9) {
                charCount.style.color = '#DC2626';
            } else if (count > config.maxChars * 0.75) {
                charCount.style.color = '#F59E0B';
            } else {
                charCount.style.color = '#9CA3AF';
            }
        });
    }

    /**
     * Display validation errors in form
     */
    function displayValidationErrors(form, errors) {
        Object.keys(errors).forEach(field => {
            const errorElement = form.querySelector(`#error-${field}`);
            if (errorElement && errors[field][0]) {
                errorElement.textContent = errors[field][0];
                errorElement.style.display = 'block';

                // Also highlight the input
                const input = form.querySelector(`[name="${field}"]`);
                if (input) {
                    input.style.borderColor = '#DC2626';
                }
            }
        });
    }

    /**
     * Clear all form errors
     */
    function clearFormErrors(form) {
        // Clear error messages
        form.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });

        // Reset input borders
        form.querySelectorAll('input, textarea').forEach(input => {
            input.style.borderColor = '';
        });
    }

    /**
     * Show message in form
     */
    function showMessage(element, message, type = 'success') {
        if (!element) return;

        element.textContent = message;
        element.className = `form-message ${type}`;
        element.style.display = 'block';

        // Scroll to message
        element.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Hide message
     */
    function hideMessage(element) {
        if (!element) return;
        element.style.display = 'none';
    }

    /**
     * Set loading state on submit button
     */
    function setLoadingState(button, isLoading) {
        if (!button) return;

        const btnText = button.querySelector('.btn-text');
        const btnLoader = button.querySelector('.btn-loader');

        if (isLoading) {
            button.disabled = true;
            if (btnText) btnText.style.display = 'none';
            if (btnLoader) btnLoader.style.display = 'inline-flex';
        } else {
            button.disabled = false;
            if (btnText) btnText.style.display = 'inline';
            if (btnLoader) btnLoader.style.display = 'none';
        }
    }

    /**
     * Reload comments section using HTMX
     */
    function reloadComments() {
        // Get current slug from URL
        const slug = window.location.pathname.split('/').pop();
        const commentsSection = document.querySelector('#comments');

        if (!commentsSection || !slug) return;

        // Use HTMX to reload comments
        if (window.htmx) {
            htmx.ajax('GET', `/partials/blog/${slug}/comments`, {
                target: '#comments',
                swap: 'outerHTML',
            });
        } else {
            // Fallback: reload page
            window.location.reload();
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
