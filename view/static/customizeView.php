<?php include __DIR__ . '/../layout/header.php'; ?>
<div class="custom-container">
  <p class="notice">BITTE BEACHTE, dass sich durch die individuelle Veredelung die Lieferzeit um ca. 2–3 Werktage verlängert. Personalisiere deinen Artikel mit einem Spieler oder deinem eigenen Namen.</p>
  <div class="option-group">
    <label><input type="radio" name="nameOption" value="player" checked> Spielername</label>
    <label><input type="radio" name="nameOption" value="custom"> Eigener Name (+15,00&nbsp;€)</label>
  </div>
  <div id="customFields" class="custom-fields hidden">
    <input type="text" id="customName" placeholder="Namen eingeben …">
    <input type="text" id="customNumber" placeholder="Nr. …">
  </div>
  <div class="option-group">
    <label><input type="checkbox" id="badgeBL"> Bundesliga-Badge (+4,00&nbsp;€)</label>
  </div>
  <div class="option-group">
    <label><input type="checkbox" id="badgeCL"> Champions-League-Badge (+10,95&nbsp;€)</label>
  </div>
  <p class="price-display">Gesamt: <span id="totalPrice">84,95&nbsp;€</span></p>
  <div class="preview">
    <a href="#" id="togglePreview" class="toggle-preview">Vorschau ausblenden</a>
    <div id="previewSection" class="preview-area">
      <svg viewBox="0 0 200 200" class="shirt-svg">
        <path d="M50 20 L70 20 L90 40 L110 40 L130 20 L150 20 L150 60 L160 80 L160 180 L40 180 L40 80 L50 60 Z" fill="white" stroke="black" stroke-width="2" />
        <text id="previewName" x="100" y="110" text-anchor="middle" font-size="16" fill="black"></text>
        <text id="previewNumber" x="100" y="140" text-anchor="middle" font-size="26" fill="black"></text>
      </svg>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layout/footer.php'; ?>
