<!-- Laith -->

<?php include __DIR__ . '/../layout/header.php'; ?>

<!-- Registrierungsformular -->
<div class="form-wrapper">
  <div align="center">
    <h1>Registrierung</h1>

    <!-- Fehlermeldung anzeigen, falls vorhanden -->
    <?php if (!empty($error)): ?>
      <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form id="registerForm" action="index.php?page=auth&action=register" method="POST" autocomplete="off">
      <!-- Benutzername mit Pattern für mind. eine Groß- und Kleinbuchstabe -->
      <label for="registerUsername">Benutzername:</label>
      <input type="text" name="username" id="registerUsername" required minlength="5" pattern="(?=.*[a-z])(?=.*[A-Z]).{5,}">
      <small></small><br>

      <!-- E-Mail-Eingabe -->
      <label for="registerEmail">E-Mail:</label>
      <input type="email" name="email" id="registerEmail" required>
      <small></small><br>

      <!-- Passwortfeld -->
      <label for="registerPassword">Passwort:</label><br>
      <input type="password" name="password" id="registerPassword" required minlength="10">
      <small></small><br>

      <!-- Passwortwiederholung -->
      <label for="registerConfirmPassword">Passwort wiederholen:</label>
      <input type="password" id="registerConfirmPassword" required>
      <small></small><br>

      <!-- Absende-Button (initial deaktiviert) -->
      <button type="submit" id="registerBtn" disabled>Registrieren</button>
    </form>

    <!-- Link zur Anmeldung -->
    <a href="index.php?page=auth&action=login" class="btn-link">Zurück zur Anmeldung</a>
  </div>
</div>

<!-- Scroll-to-top Button -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<!-- JavaScript zur Validierung -->
<script>
  function validateUsernameFormat(input) {
    const value = input.value;
    const valid = value.length >= 5 && /[a-z]/.test(value) && /[A-Z]/.test(value);
    setValidation(input, valid, "Mind. 5 Zeichen, Groß- & Kleinbuchstaben erforderlich.");
    return valid;
  }

  function validatePassword(input) {
    const value = input.value;
    const valid = value.length >= 10;
    setValidation(input, valid, "Passwort muss mind. 10 Zeichen lang sein.");
    return valid;
  }

  function validateConfirmPassword(passwordInput, confirmInput) {
    const valid = confirmInput.value === passwordInput.value && confirmInput.value.length > 0;
    setValidation(confirmInput, valid, "Passwörter stimmen nicht überein.");
    return valid;
  }

  function setValidation(input, isValid, message) {
    const small = input.nextElementSibling;
    input.classList.toggle("valid", isValid);
    input.classList.toggle("invalid", !isValid);
    small.textContent = isValid ? "" : message;
  }

  const rUser = document.getElementById("registerUsername");
  const rEmail = document.getElementById("registerEmail");
  const rPass = document.getElementById("registerPassword");
  const rConf = document.getElementById("registerConfirmPassword");
  const rBtn = document.getElementById("registerBtn");

  // Servercheck auf Verfügbarkeit von Username/Email
  async function checkTaken(field, value) {
    const res = await fetch('index.php?page=auth&action=check', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        [field]: value
      })
    });
    const data = await res.json();
    return data[field + 'Taken'];
  }

  async function validateUsername(input) {
    if (!validateUsernameFormat(input)) return false;
    const taken = await checkTaken('username', input.value);
    setValidation(input, !taken, 'Benutzername bereits vergeben.');
    return !taken;
  }

  async function validateEmail(input) {
    const value = input.value;
    if (value.length === 0) {
      setValidation(input, false, '');
      return false;
    }
    const taken = await checkTaken('email', value);
    setValidation(input, !taken, 'E-Mail bereits vergeben.');
    return !taken;
  }

  // Prüfe alle Felder gemeinsam
  async function checkForm() {
    const uValid = await validateUsername(rUser);
    const eValid = await validateEmail(rEmail);
    const pValid = validatePassword(rPass);
    const cValid = validateConfirmPassword(rPass, rConf);
    rBtn.disabled = !(uValid && eValid && pValid && cValid);
  }

  // Eventlistener auf Eingabefelder
  [rUser, rEmail, rPass, rConf].forEach(input => input.addEventListener('input', checkForm));
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>