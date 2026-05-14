// Warten bis die Seite geladen ist, dann sämtliche Event-Handler registrieren
window.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('ratingModal');
  if (!modal) return;
  // merkt sich eine hervorgehobene Bewertung, um sie später zurückzusetzen
  let highlighted = null;

  // Klick auf "Bewertung schreiben" öffnet das Modal
  document.querySelectorAll('.open-review-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      document.body.classList.add('modal-open');
      modal.classList.remove('hidden');
      const pidInput = document.getElementById('ratingProductId');
      if (pidInput) pidInput.value = btn.dataset.productId || '';
      const parentInput = document.getElementById('ratingParentId');
      if (parentInput) parentInput.value = '';
      const target = document.getElementById('replyTarget');
      if (target) {
        target.classList.add('hidden');
        target.textContent = '';
      }
      if (highlighted) {
        highlighted.classList.remove('target-highlight');
        highlighted = null;
      }
    });
  });

  // Klick außerhalb des Dialogs schließt das Modal
  modal.addEventListener('click', e => {
    if (e.target === modal) closeRatingModal();
  });

  // Modal per Escape-Taste schließen
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeRatingModal();
  });

  // Einfache Textvorschläge je nach gewählter Sternebewertung ein-/ausblenden
  function showSuggestions(rating) {
    document.querySelectorAll('#ratingForm .suggestions-set').forEach(set => {
      set.classList.toggle('hidden', set.dataset.rating !== rating);
    });
  }



  // Like/Dislike Buttons an bereits vorhandenen Bewertungen
  // (diese Logik taucht später noch einmal in addRatingToDom auf → Redundanz)
  document.querySelectorAll('.review-actions').forEach(actions => {
    const likeBtn = actions.querySelector('.like-btn');
    const dislikeBtn = actions.querySelector('.dislike-btn');
    if (!likeBtn || !dislikeBtn) return;
    const id = likeBtn.dataset.id;

    // Serverantwort anwenden und Zähler aktualisieren
    function apply(data) {
      if (!data) return;
      actions.querySelector('.like-btn span').textContent = data.likes;
      actions.querySelector('.dislike-btn span').textContent = data.dislikes;
      actions.dataset.userVote = data.user_vote || '';
      likeBtn.classList.toggle('active', data.user_vote === 'like');
      dislikeBtn.classList.toggle('active', data.user_vote === 'dislike');
    }

    // Vote per AJAX an den Server senden
    function send(action) {
      fetch(`index.php?page=community&action=${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'rating_id=' + encodeURIComponent(id)
      })
        .then(res => res.json())
        .then(apply)
        .catch(console.error);
    }

    // Handhabung des Like Buttons
    likeBtn.addEventListener('click', () => {
      if (!window.isLoggedIn) { window.location.href = 'index.php?page=auth&action=login'; return; }
      const current = actions.dataset.userVote;
      if (current === 'like') send('unlikeRating');
      else send('likeRating');
    });

    // Handhabung des Dislike Buttons
    dislikeBtn.addEventListener('click', () => {
      if (!window.isLoggedIn) { window.location.href = 'index.php?page=auth&action=login'; return; }
      const current = actions.dataset.userVote;
      if (current === 'dislike') send('undislikeRating');
      else send('dislikeRating');
    });
  });

  // "Antworten" auf bestehende Bewertung öffnet Modal und markiert Zielkommentar
  document.querySelectorAll('.reply-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.body.classList.add('modal-open');
      modal.classList.remove('hidden');
      const pidInput = document.getElementById('ratingProductId');
      const parentInput = document.getElementById('ratingParentId');
      if (pidInput) pidInput.value = btn.dataset.productId || '';
      if (parentInput) parentInput.value = btn.dataset.id || '';
      const target = document.getElementById('replyTarget');
      const review = btn.closest('.review');
      if (target && review) {
        const name = review.querySelector('strong')?.textContent || '';
        const snippet = review.querySelector('p')?.textContent.slice(0, 60) || '';
        target.textContent = '';
        target.append('Antwort auf ' + name + ': ');
        const span = document.createElement('span');
        span.className = 'parent-excerpt';
        span.textContent = snippet;
        target.appendChild(span);
        target.classList.remove('hidden');
      }
      if (highlighted) highlighted.classList.remove('target-highlight');
      if (review) {
        review.classList.add('target-highlight');
        highlighted = review;
      }

    });
  });

  // Bild-Upload inkl. Vorschau der ausgewählten Dateien
  const imageInput = document.getElementById('ratingImages');
  const previewList = document.getElementById('imagePreviewList');
  // enthält maximal fünf ausgewählte Dateien
  const selectedFiles = [];

  // Synchronisiert die ausgewählten Dateien mit dem versteckten File-Input
  function syncInput() {
    if (!imageInput) return;
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    imageInput.files = dt.files;
  }

  // Erstellt kleine Vorschaubilder der gewählten Dateien
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

  // Beim Auswählen neuer Dateien Vorschau erstellen
  if (imageInput && previewList) {
    imageInput.addEventListener('change', () => {
      [...imageInput.files].forEach((file) => {
        if (selectedFiles.length < 5) selectedFiles.push(file);
      });
      syncInput();
      renderPreviews();
    });
  }

  // Klick auf einen Vorschlag füllt das Kommentar-Feld
  const commentField = document.getElementById('ratingComment');
  document.querySelectorAll('#ratingForm .suggest-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      if (commentField) {
        commentField.value = btn.textContent;
        commentField.focus();
      }
    });
  });

  // Beim Laden direkt passende Vorschläge anzeigen
  const checked = document.querySelector('.rating-stars input:checked');
  showSuggestions(checked ? checked.value : '5');

  // Wechsel der Sternebewertung aktualisiert die angezeigten Vorschläge
  document.querySelectorAll('.rating-stars input').forEach(rad => {
    rad.addEventListener('change', () => showSuggestions(rad.value));
  });

  // Klick auf Vorschaubilder öffnet die Galerie
  document.querySelectorAll('.review-images').forEach(container => {
    const images = JSON.parse(container.dataset.images || '[]');
    container.querySelectorAll('img').forEach(img => {
      img.addEventListener('click', () => {
        openImageGallery(images, parseInt(img.dataset.idx, 10) || 0);
      });
    });
  });

  // Formular wird via AJAX abgeschickt, um neue Bewertung dynamisch hinzuzufügen
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

// Zeigt eine Galerieansicht für Bewertungsbilder an
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

// Schließt das Bewertungs-Modal und setzt evtl. gesetzte Markierungen zurück
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
    const target = document.getElementById('replyTarget');
    if (target) target.classList.add('hidden');
    if (highlighted) {
      highlighted.classList.remove('target-highlight');
      highlighted = null;
    }
  }
}

// Fügt eine neue Bewertung inklusive Interaktionen in das DOM ein
function addRatingToDom(rating) {
  const reviews = document.querySelector('.reviews');
  if (!reviews || !rating) return;

  const reviewEl = document.createElement('div');
  reviewEl.className = 'review' + (rating.parent_id ? ' reply' : '');
  reviewEl.dataset.reviewId = rating.id;
  if (rating.parent_id) reviewEl.dataset.parentId = rating.parent_id;

  const content = document.createElement('div');
  content.className = 'review-content';
  if (rating.parent_name) {
    const info = document.createElement('small');
    info.className = 'reply-info';
    info.append('Antwort auf ' + rating.parent_name + ': ');
    if (rating.parent_comment) {
      const ex = document.createElement('span');
      ex.className = 'parent-excerpt';
      ex.textContent = rating.parent_comment;
      info.appendChild(ex);
    }
    content.appendChild(info);
  }
  const name = document.createElement('strong');
  name.textContent = rating.display_name || rating.username || '';
  const date = document.createElement('small');
  date.className = 'rating-date';
  date.textContent = new Date(rating.created_at).toLocaleString('de-DE');
  const stars = document.createElement('span');
  stars.className = 'rating-stars';
  stars.style.pointerEvents = 'none';
  for (let s = 5; s >= 1; s--) {
    const lab = document.createElement('label');
    lab.textContent = s <= rating.stars ? '★' : '☆';
    stars.appendChild(lab);
  }
  const text = document.createElement('p');
  text.innerHTML = (rating.comment || '').replace(/\n/g, '<br>');
  content.appendChild(name);
  content.appendChild(date);

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

  like.innerHTML = '👍 <span>' + (rating.likes || 0) + '</span>';
  const dislike = document.createElement('button');
  dislike.type = 'button';
  dislike.className = 'dislike-btn';
  dislike.dataset.id = rating.id;

  dislike.innerHTML = '👎 <span>' + (rating.dislikes || 0) + '</span>';
  actions.appendChild(like);
  actions.appendChild(dislike);
  if (window.isLoggedIn) {
    const reply = document.createElement('button');
    reply.type = 'button';
    reply.className = 'reply-btn';
    reply.dataset.id = rating.id;
    reply.dataset.productId = rating.product_id;

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

    btnDel.textContent = 'Löschen';
    form.appendChild(idIn);
    form.appendChild(pidIn);
    form.appendChild(btnDel);
    actions.appendChild(form);
  }
  reviewEl.appendChild(content);
  reviewEl.appendChild(actions);

  const noReviews = reviews.querySelector('.no-reviews');
  if (noReviews) noReviews.remove();

  if (rating.parent_id) {
    const parent = reviews.querySelector(`[data-review-id="${rating.parent_id}"]`);
    if (parent) {
      let target = parent;
      let next = target.nextElementSibling;
      while (next && next.dataset.parentId == rating.parent_id) {
        target = next;
        next = target.nextElementSibling;
      }
      target.insertAdjacentElement('afterend', reviewEl);
    } else {
      reviews.appendChild(reviewEl);
    }
  } else {
    reviews.insertBefore(reviewEl, reviews.firstChild);
  }


  reviewEl.classList.add('pulse-highlight');
  reviewEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
  setTimeout(() => reviewEl.classList.remove('pulse-highlight'), 1000);

  // Like/Dislike Logik erneut (siehe oben) – könnte in Funktion ausgelagert werden
  actions.dataset.userVote = rating.user_vote || '';
  like.classList.toggle('active', rating.user_vote === 'like');
  dislike.classList.toggle('active', rating.user_vote === 'dislike');

  function update(votes) {
    if (!votes) return;
    like.querySelector('span').textContent = votes.likes;
    dislike.querySelector('span').textContent = votes.dislikes;
    actions.dataset.userVote = votes.user_vote || '';
    like.classList.toggle('active', votes.user_vote === 'like');
    dislike.classList.toggle('active', votes.user_vote === 'dislike');
  }

  function send(action) {
    fetch('index.php?page=community&action=' + action, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'rating_id=' + encodeURIComponent(rating.id)
    })
      .then(r => r.json())
      .then(update);
  }

  like.addEventListener('click', () => {
    if (!window.isLoggedIn) { window.location.href = 'index.php?page=auth&action=login'; return; }
    const current = actions.dataset.userVote;
    if (current === 'like') send('unlikeRating');
    else send('likeRating');
  });

  dislike.addEventListener('click', () => {
    if (!window.isLoggedIn) { window.location.href = 'index.php?page=auth&action=login'; return; }
    const current = actions.dataset.userVote;
    if (current === 'dislike') send('undislikeRating');
    else send('dislikeRating');
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

