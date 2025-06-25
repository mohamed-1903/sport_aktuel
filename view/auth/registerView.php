<?php include __DIR__ . '/../layout/header.php'; ?>


<div class="form-wrapper">
  <div align="center">
    <h1>Registrierung</h1>

    <?php if (!empty($error)): ?>
      <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form id="registerForm" action="index.php?page=auth&action=register" method="POST" autocomplete="off">
      <label for="registerUsername">Benutzername:</label><br>
      <input type="text" name="username" id="registerUsername" required minlength="5" pattern="(?=.*[a-z])(?=.*[A-Z]).{5,}">
      <small></small><br>

      <label for="registerEmail">E-Mail:</label><br>
      <input type="email" name="email" id="registerEmail" required>
      <small></small><br>

      <label for="registerPassword">Passwort:</label><br>
      <input type="password" name="password" id="registerPassword" required minlength="10">
      <small></small><br>

      <label for="registerConfirmPassword">Passwort wiederholen:</label><br>
      <input type="password" id="registerConfirmPassword" required>
      <small></small><br>

      <button type="submit" id="registerBtn" disabled>Registrieren</button>
    </form>

    <a href="index.php?page=auth&action=login" class="back-link">
      <button type="button">Zurück zur Anmeldung</button>
    </a>
  </div>
</div>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
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

  async function checkTaken(field, value) {
    const res = await fetch('index.php?page=auth&action=check', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ [field]: value })
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

  async function checkForm() {
    const uValid = await validateUsername(rUser);
    const eValid = await validateEmail(rEmail);
    const pValid = validatePassword(rPass);
    const cValid = validateConfirmPassword(rPass, rConf);
    rBtn.disabled = !(uValid && eValid && pValid && cValid);
  }

  [rUser, rEmail, rPass, rConf].forEach(input => input.addEventListener('input', checkForm));
</script>
<?php include __DIR__ . '/../layout/footer.php'; ?>

