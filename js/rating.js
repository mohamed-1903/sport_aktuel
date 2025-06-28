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
      const parentInput = document.getElementById('ratingParentId');
      if (parentInput) parentInput.value = '';
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
  const selectedFiles = [];

  function syncInput() {
    if (!imageInput) return;
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    imageInput.files = dt.files;
  }

  function renderPreviews() {
    if (!previewList) return;
    previewList.innerHTML = '';
    selectedFiles.forEach((file, idx) => {
      const wrapper = document.createElement('div');
      wrapper.className = 'image-preview';
      const img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.innerHTML = '&times;';
      btn.addEventListener('click', () => {
        selectedFiles.splice(idx, 1);
        syncInput();
        renderPreviews();
      });
      wrapper.appendChild(img);
      wrapper.appendChild(btn);
      previewList.appendChild(wrapper);
    });
    previewList.classList.toggle('hidden', selectedFiles.length === 0);
  }

  if (imageInput && previewList) {
    imageInput.addEventListener('change', () => {
      [...imageInput.files].forEach((file) => {
        if (selectedFiles.length < 5) selectedFiles.push(file);
      });
      syncInput();
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

  function updateCounts(btn, data) {
    if (!data) return;
    const actions = btn.closest('.review-actions');
    actions.querySelector('.like-btn span').textContent = data.likes;
    actions.querySelector('.dislike-btn span').textContent = data.dislikes;
  }

  document.querySelectorAll('.like-btn').forEach(btn => {
    const id = btn.dataset.id;
    const other = btn.parentElement.querySelector('.dislike-btn');
    const key = 'ratingVote_' + id;
    if (localStorage.getItem(key) === 'like') btn.classList.add('active');

    btn.addEventListener('click', () => {
      const current = localStorage.getItem(key);
      if (current === 'like') {
        fetch('index.php?page=community&action=unlikeRating', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'rating_id=' + encodeURIComponent(id)
        })
          .then(res => res.json())
          .then(data => updateCounts(btn, data))
          .catch(console.error);
        localStorage.removeItem(key);
        btn.classList.remove('active');
      } else {
        fetch('index.php?page=community&action=likeRating', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'rating_id=' + encodeURIComponent(id)
        })
          .then(res => res.json())
          .then(data => updateCounts(btn, data))
          .catch(console.error);
        btn.classList.add('active');
        if (current === 'dislike' && other) {
          other.classList.remove('active');
          fetch('index.php?page=community&action=undislikeRating', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'rating_id=' + encodeURIComponent(id)
          })
            .then(res => res.json())
            .then(data => updateCounts(btn, data))
            .catch(console.error);
        }
        localStorage.setItem(key, 'like');
      }
    });
  });

  document.querySelectorAll('.dislike-btn').forEach(btn => {
    const id = btn.dataset.id;
    const other = btn.parentElement.querySelector('.like-btn');
    const key = 'ratingVote_' + id;
    if (localStorage.getItem(key) === 'dislike') btn.classList.add('active');

    btn.addEventListener('click', () => {
      const current = localStorage.getItem(key);
      if (current === 'dislike') {
        fetch('index.php?page=community&action=undislikeRating', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'rating_id=' + encodeURIComponent(id)
        })
          .then(res => res.json())
          .then(data => updateCounts(btn, data))
          .catch(console.error);
        localStorage.removeItem(key);
        btn.classList.remove('active');
      } else {
        fetch('index.php?page=community&action=dislikeRating', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'rating_id=' + encodeURIComponent(id)
        })
          .then(res => res.json())
          .then(data => updateCounts(btn, data))
          .catch(console.error);
        btn.classList.add('active');
        if (current === 'like' && other) {
          other.classList.remove('active');
          fetch('index.php?page=community&action=unlikeRating', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'rating_id=' + encodeURIComponent(id)
          })
            .then(res => res.json())
            .then(data => updateCounts(btn, data))
            .catch(console.error);
        }
        localStorage.setItem(key, 'dislike');
      }
    });
  });

  document.querySelectorAll('.reply-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.body.classList.add('modal-open');
      modal.classList.remove('hidden');
      const pidInput = document.getElementById('ratingProductId');
      const parentInput = document.getElementById('ratingParentId');
      if (pidInput) pidInput.value = btn.dataset.productId || '';
      if (parentInput) parentInput.value = btn.dataset.id || '';

    });
  });

  document.querySelectorAll('.reply-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.body.classList.add('modal-open');
      modal.classList.remove('hidden');
      const pidInput = document.getElementById('ratingProductId');
      const parentInput = document.getElementById('ratingParentId');
      if (pidInput) pidInput.value = btn.dataset.productId || '';
      if (parentInput) parentInput.value = btn.dataset.id || '';
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
  const selectedFiles = [];

  function syncInput() {
    if (!imageInput) return;
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    imageInput.files = dt.files;
  }

  function renderPreviews() {
    if (!previewList) return;
    previewList.innerHTML = '';
    selectedFiles.forEach((file, idx) => {

      const wrapper = document.createElement('div');
      wrapper.className = 'image-preview';
      const img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.innerHTML = '&times;';
      btn.addEventListener('click', () => {
        selectedFiles.splice(idx, 1);
        syncInput();

        renderPreviews();
      });
      wrapper.appendChild(img);
      wrapper.appendChild(btn);
      previewList.appendChild(wrapper);
    });
    previewList.classList.toggle('hidden', selectedFiles.length === 0);

  }

  if (imageInput && previewList) {
    imageInput.addEventListener('change', () => {
      [...imageInput.files].forEach((file) => {
        if (selectedFiles.length < 5) selectedFiles.push(file);
      });
      syncInput();
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
  zoomImages = images;
  currentImageIndex = start || 0;
  zoomScale = 1;
  const zoomImage = document.getElementById('zoom-image');
  if (zoomImage) {
    zoomImage.src = images[currentImageIndex];
    zoomImage.style.transform = 'scale(1)';
  }
  document.body.classList.add('modal-open');
  document.getElementById('zoomModal').classList.remove('hidden');
}

function closeRatingModal() {
  const modal = document.getElementById('ratingModal');
  if (modal) {
    modal.classList.add('hide');
    modal.addEventListener(
      'animationend',
      () => {
        modal.classList.add('hidden');
        modal.classList.remove('hide');
      },
      { once: true }
    );

    document.body.classList.remove('modal-open');
    const parentInput = document.getElementById('ratingParentId');
    if (parentInput) parentInput.value = '';
  }
}

