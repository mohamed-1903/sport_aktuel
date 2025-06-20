document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-add-to-watch').forEach(btn => {
    btn.addEventListener('click', () => {
      const item = {
        id: btn.dataset.iid,
        name: btn.dataset.name,
        price: btn.dataset.price,
        image: btn.dataset.image
      };

      fetch('index.php?page=watchlist&action=add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(item)
      }).then(res => {
        const popup = document.getElementById('watchlist-preview-popup');
        if (res.ok) {
          if (popup) {
            popup.innerHTML = `<img src="${item.image}" alt=""><strong>${item.name}</strong><small>zur Sammelliste hinzugefügt</small>`;
            popup.style.display = 'block';
            setTimeout(() => popup.style.display = 'none', 2000);
          } else {
            alert('Produkt wurde zur Sammelliste hinzugefügt.');
          }
        } else {
          alert('Fehler beim Hinzufügen zur Sammelliste.');
        }
      });
    });
  });
});