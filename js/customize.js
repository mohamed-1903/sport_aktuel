document.addEventListener('DOMContentLoaded', () => {
  const BASE_PRICE = 84.95;
  const CUSTOM_PRICE = 15.0;
  const priceEl = document.getElementById('totalPrice');
  const customFields = document.getElementById('customFields');
  const nameInputs = document.querySelectorAll('input[name="nameOption"]');
  const badgeBL = document.getElementById('badgeBL');
  const badgeCL = document.getElementById('badgeCL');
  const nameInput = document.getElementById('customName');
  const numberInput = document.getElementById('customNumber');
  const previewName = document.getElementById('previewName');
  const previewNumber = document.getElementById('previewNumber');
  const togglePreview = document.getElementById('togglePreview');
  const previewSection = document.getElementById('previewSection');

  // Falls die Elemente nicht existieren, ist diese Datei auf der aktuellen Seite
  // nicht erforderlich. In diesem Fall brechen wir ab, um Fehler zu vermeiden.
  if (!priceEl || !customFields || !badgeBL || !badgeCL) {
    return;
  }

  function updatePrice() {
    let total = BASE_PRICE;
    const option = document.querySelector('input[name="nameOption"]:checked').value;
    if (option === 'custom') total += CUSTOM_PRICE;
    if (badgeBL.checked) total += 4.0;
    if (badgeCL.checked) total += 10.95;
    priceEl.textContent = total.toFixed(2) + ' €';
  }

  function updateFields() {
    const option = document.querySelector('input[name="nameOption"]:checked').value;
    customFields.classList.toggle('hidden', option !== 'custom');
    updatePrice();
  }

  nameInputs.forEach(el => el.addEventListener('change', updateFields));
  badgeBL.addEventListener('change', updatePrice);
  badgeCL.addEventListener('change', updatePrice);

  nameInput.addEventListener('input', () => {
    previewName.textContent = nameInput.value;
  });
  numberInput.addEventListener('input', () => {
    previewNumber.textContent = numberInput.value;
  });

  togglePreview.addEventListener('click', e => {
    e.preventDefault();
    const hidden = previewSection.classList.toggle('hidden');
    togglePreview.textContent = hidden ? 'Vorschau einblenden' : 'Vorschau ausblenden';
  });

  updateFields();
});
