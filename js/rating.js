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

  const imageInput = document.getElementById('ratingImages');
  const previewList = document.getElementById('imagePreviewList');
  let dt = new DataTransfer();

  function renderPreviews() {
    if (!previewList) return;
    previewList.innerHTML = '';
    [...dt.files].forEach((file, idx) => {
      const wrapper = document.createElement('div');
      wrapper.className = 'image-preview';
      const img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.innerHTML = '&times;';
      btn.addEventListener('click', () => {
        dt.items.remove(idx);
        imageInput.files = dt.files;
        renderPreviews();
      });
      wrapper.appendChild(img);
      wrapper.appendChild(btn);
      previewList.appendChild(wrapper);
    });
    previewList.classList.toggle('hidden', dt.files.length === 0);
  }

  if (imageInput && previewList) {
    imageInput.addEventListener('change', () => {
      [...imageInput.files].forEach((file) => {
        if (dt.files.length < 5) dt.items.add(file);
      });
      imageInput.files = dt.files;
      renderPreviews();
    });
  }

  const commentField = document.querySelector('#ratingForm textarea[name="comment"]');

  function showSuggestions(rating) {
    document.querySelectorAll('#ratingForm .suggestions-set').forEach(set => {
      set.classList.toggle('hidden', set.dataset.rating !== rating);
    });
  }

  document.querySelectorAll('#ratingForm .suggest-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      if (commentField) {
        commentField.value = btn.textContent;
        commentField.focus();
      }
    });
  });

  const checked = document.querySelector('.rating-stars input:checked');
  showSuggestions(checked ? checked.value : '5');

  document.querySelectorAll('.rating-stars input').forEach(rad => {
    rad.addEventListener('change', () => showSuggestions(rad.value));
  });

  document.querySelectorAll('.review-images').forEach(container => {
    const images = JSON.parse(container.dataset.images || '[]');
    container.querySelectorAll('img').forEach(img => {
      img.addEventListener('click', () => {
        openImageGallery(images, parseInt(img.dataset.idx, 10) || 0);
      });
    });
  });
});

function openImageGallery(images, start) {
  if (!images || images.length === 0) return;
  window.zoomImages = images;
  window.currentImageIndex = start || 0;
  window.zoomScale = 1;
  const zoomImage = document.getElementById('zoom-image');
  if (zoomImage) {
    zoomImage.src = images[window.currentImageIndex];
    zoomImage.style.transform = 'scale(1)';
  }
  document.body.classList.add('modal-open');
  document.getElementById('zoomModal').classList.remove('hidden');
}

function closeRatingModal() {
  const modal = document.getElementById('ratingModal');
  if (modal) {
    modal.classList.add('hidden');
    document.body.classList.remove('modal-open');
  }
}

