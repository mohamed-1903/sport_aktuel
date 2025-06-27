// Handles the rating modal interactions
window.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('ratingModal');
  if (!modal) return;

  document.querySelectorAll('.open-review-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      document.body.classList.add('modal-open');
      modal.classList.remove('hidden');
      const pidInput = document.getElementById('ratingProductId');
      if (pidInput) pidInput.value = btn.dataset.productId || '';
    });
  });

  modal.addEventListener('click', e => {
    if (e.target === modal) closeRatingModal();
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeRatingModal();
  });

  const imageInput = document.getElementById('ratingImage');
  const previewContainer = document.getElementById('imagePreviewContainer');
  const preview = document.getElementById('ratingPreview');
  const removeBtn = document.getElementById('removeImageBtn');
  if (imageInput && preview && previewContainer) {
    imageInput.addEventListener('change', () => {
      const file = imageInput.files[0];
      if (file) {
        preview.src = URL.createObjectURL(file);
        previewContainer.classList.remove('hidden');
      } else {
        previewContainer.classList.add('hidden');
      }
    });
  }
  if (removeBtn) {
    removeBtn.addEventListener('click', () => {
      imageInput.value = '';
      preview.src = '';
      previewContainer.classList.add('hidden');
    });
  }

  const suggestionBar = document.getElementById('suggestionBar');
  const suggestionsByRating = suggestionBar ? JSON.parse(suggestionBar.dataset.suggestions || '{}') : {};
  const commentField = document.querySelector('#ratingForm textarea[name="comment"]');

  function renderSuggestions(rating) {
    if (!suggestionBar) return;
    suggestionBar.innerHTML = '';
    (suggestionsByRating[rating] || []).forEach(text => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'suggest-btn';
      btn.textContent = text;
      btn.addEventListener('click', () => {
        if (commentField) {
          commentField.value = text;
          commentField.focus();
        }
      });
      suggestionBar.appendChild(btn);
    });
  }

  const checked = document.querySelector('.rating-stars input:checked');
  renderSuggestions(checked ? checked.value : '5');

  document.querySelectorAll('.rating-stars input').forEach(rad => {
    rad.addEventListener('change', () => renderSuggestions(rad.value));
  });
});

function closeRatingModal() {
  const modal = document.getElementById('ratingModal');
  if (modal) {
    modal.classList.add('hidden');
    document.body.classList.remove('modal-open');
  }
}

