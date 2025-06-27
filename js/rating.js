// Handles the rating modal interactions
window.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('ratingModal');
  if (!modal) return;
  const closeBtn = modal.querySelector('.review-close');
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

  if (closeBtn) closeBtn.addEventListener('click', closeRatingModal);

  window.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
      closeRatingModal();
    }
  });
});

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
  }
}

