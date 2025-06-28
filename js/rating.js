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

  const ratingForm = document.getElementById('ratingForm');
  if (ratingForm) {
    ratingForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const formData = new FormData(ratingForm);
      fetch('index.php?page=community&action=addRatingAjax', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data && data.success && data.rating) {
            addRatingToDom(data.rating);
            ratingForm.reset();
            selectedFiles.length = 0;
            renderPreviews();
            closeRatingModal();
          } else {
            ratingForm.submit();
          }
        })
        .catch(err => {
          console.error(err);
          ratingForm.submit();
        });
    });
  }

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

function addRatingToDom(rating) {
  const reviews = document.querySelector('.reviews');
  if (!reviews || !rating) return;

  const reviewEl = document.createElement('article');
  reviewEl.className = 'review' + (rating.parent_id ? ' reply' : '');
  reviewEl.dataset.reviewId = rating.id;
  if (rating.parent_id) reviewEl.dataset.parentId = rating.parent_id;
  reviewEl.tabIndex = 0;
  reviewEl.setAttribute('role', 'article');

  const content = document.createElement('div');
  content.className = 'review-content';
  const header = document.createElement('header');
  header.className = 'review-header';
  header.id = 'review-' + rating.id + '-header';
  const name = document.createElement('strong');
  name.className = 'review-author';
  name.textContent = rating.display_name || rating.username || '';
  const date = document.createElement('time');
  date.className = 'rating-date';
  date.dateTime = new Date(rating.created_at).toISOString();
  date.textContent = new Date(rating.created_at).toLocaleString('de-DE');
  header.appendChild(name);
  header.appendChild(date);
  const stars = document.createElement('div');
  stars.className = 'rating-stars';
  stars.style.pointerEvents = 'none';
  stars.setAttribute('role', 'img');
  stars.setAttribute('aria-label', rating.stars + ' von 5 Sternen');
  for (let s = 5; s >= 1; s--) {
    const lab = document.createElement('label');
    lab.textContent = s <= rating.stars ? '★' : '☆';
    lab.setAttribute('aria-hidden', 'true');
    stars.appendChild(lab);
  }
  const text = document.createElement('p');
  text.className = 'review-text';
  text.innerHTML = (rating.comment || '').replace(/\n/g, '<br>');
  content.appendChild(header);
  content.appendChild(stars);
  content.appendChild(text);

  if (rating.image_paths && rating.image_paths.length) {
    const imgWrap = document.createElement('div');
    imgWrap.className = 'review-images';
    imgWrap.dataset.images = JSON.stringify(rating.image_paths);
    rating.image_paths.forEach((img, idx) => {
      const im = document.createElement('img');
      im.src = img;
      im.dataset.idx = idx;
      im.alt = 'Bild zur Bewertung';
      im.addEventListener('click', () => {
        openImageGallery(rating.image_paths, idx);
      });
      imgWrap.appendChild(im);
    });
    content.appendChild(imgWrap);
  }

  const actions = document.createElement('div');
  actions.className = 'review-actions';
  const like = document.createElement('button');
  like.type = 'button';
  like.className = 'like-btn';
  like.dataset.id = rating.id;
  like.setAttribute('aria-label', 'Bewertung positiv bewerten');
  like.innerHTML = '👍 <span>' + (rating.likes || 0) + '</span>';
  const dislike = document.createElement('button');
  dislike.type = 'button';
  dislike.className = 'dislike-btn';
  dislike.dataset.id = rating.id;
  dislike.setAttribute('aria-label', 'Bewertung negativ bewerten');
  dislike.innerHTML = '👎 <span>' + (rating.dislikes || 0) + '</span>';
  actions.appendChild(like);
  actions.appendChild(dislike);
  if (window.isLoggedIn) {
    const reply = document.createElement('button');
    reply.type = 'button';
    reply.className = 'reply-btn';
    reply.dataset.id = rating.id;
    reply.dataset.productId = rating.product_id;
    reply.setAttribute('aria-label', 'Auf diese Bewertung antworten');
    reply.textContent = 'Antworten';
    actions.appendChild(reply);
  }
  if (window.currentUserId && (window.currentUserId === rating.user_id || window.isAdmin)) {
    const form = document.createElement('form');
    form.className = 'delete-rating-form';
    form.method = 'post';
    form.action = 'index.php?page=community&action=deleteRating';
    form.onsubmit = () => confirm('Bewertung löschen?');
    const idIn = document.createElement('input');
    idIn.type = 'hidden';
    idIn.name = 'rating_id';
    idIn.value = rating.id;
    const pidIn = document.createElement('input');
    pidIn.type = 'hidden';
    pidIn.name = 'product_id';
    pidIn.value = rating.product_id;
    const btnDel = document.createElement('button');
    btnDel.type = 'submit';
    btnDel.className = 'btn-delete-rating';
    btnDel.setAttribute('aria-label', 'Bewertung löschen');
    btnDel.textContent = 'Löschen';
    form.appendChild(idIn);
    form.appendChild(pidIn);
    form.appendChild(btnDel);
    actions.appendChild(form);
  }
  reviewEl.appendChild(content);
  reviewEl.appendChild(actions);

  if (rating.parent_id) {
    const parent = document.querySelector(`[data-review-id="${rating.parent_id}"]`);
    let replies = parent && parent.querySelector('.review-replies');
    if (!replies && parent) {
      replies = document.createElement('div');
      replies.className = 'review-replies';
      parent.appendChild(replies);
    }
    if (replies) replies.appendChild(reviewEl);
  } else {
    const noReviews = reviews.querySelector('.no-reviews');
    if (noReviews) noReviews.remove();
    reviews.insertBefore(reviewEl, reviews.firstChild);
  }
  reviewEl.classList.add('pulse-highlight');
  reviewEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
  setTimeout(() => reviewEl.classList.remove('pulse-highlight'), 1000);

  // attach like/dislike events
  [like, dislike].forEach(btn => {
    const id = btn.dataset.id;
    const key = 'ratingVote_' + id;
    if (localStorage.getItem(key) === 'like' && btn === like) btn.classList.add('active');
    if (localStorage.getItem(key) === 'dislike' && btn === dislike) btn.classList.add('active');
  });
  like.addEventListener('click', () => {
    const key = 'ratingVote_' + rating.id;
    const other = dislike;
    const current = localStorage.getItem(key);
    if (current === 'like') {
      fetch('index.php?page=community&action=unlikeRating', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'rating_id=' + encodeURIComponent(rating.id)
      })
        .then(r => r.json())
        .then(d => { like.querySelector('span').textContent = d.likes; dislike.querySelector('span').textContent = d.dislikes; });
      localStorage.removeItem(key);
      like.classList.remove('active');
    } else {
      fetch('index.php?page=community&action=likeRating', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'rating_id=' + encodeURIComponent(rating.id)
      })
        .then(r => r.json())
        .then(d => { like.querySelector('span').textContent = d.likes; dislike.querySelector('span').textContent = d.dislikes; });
      like.classList.add('active');
      if (current === 'dislike') {
        other.classList.remove('active');
        fetch('index.php?page=community&action=undislikeRating', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'rating_id=' + encodeURIComponent(rating.id)
        })
          .then(r => r.json())
          .then(d => { like.querySelector('span').textContent = d.likes; dislike.querySelector('span').textContent = d.dislikes; });
      }
      localStorage.setItem(key, 'like');
    }
  });

  dislike.addEventListener('click', () => {
    const key = 'ratingVote_' + rating.id;
    const other = like;
    const current = localStorage.getItem(key);
    if (current === 'dislike') {
      fetch('index.php?page=community&action=undislikeRating', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'rating_id=' + encodeURIComponent(rating.id)
      })
        .then(r => r.json())
        .then(d => { like.querySelector('span').textContent = d.likes; dislike.querySelector('span').textContent = d.dislikes; });
      localStorage.removeItem(key);
      dislike.classList.remove('active');
    } else {
      fetch('index.php?page=community&action=dislikeRating', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'rating_id=' + encodeURIComponent(rating.id)
      })
        .then(r => r.json())
        .then(d => { like.querySelector('span').textContent = d.likes; dislike.querySelector('span').textContent = d.dislikes; });
      dislike.classList.add('active');
      if (current === 'like') {
        other.classList.remove('active');
        fetch('index.php?page=community&action=unlikeRating', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'rating_id=' + encodeURIComponent(rating.id)
        })
          .then(r => r.json())
          .then(d => { like.querySelector('span').textContent = d.likes; dislike.querySelector('span').textContent = d.dislikes; });
      }
      localStorage.setItem(key, 'dislike');
    }
  });

  if (window.isLoggedIn) {
    const reply = actions.querySelector('.reply-btn');
    if (reply) {
      reply.addEventListener('click', () => {
        document.body.classList.add('modal-open');
        document.getElementById('ratingModal').classList.remove('hidden');
        document.getElementById('ratingProductId').value = rating.product_id;
        document.getElementById('ratingParentId').value = rating.id;
      });
    }
  }
}

