<!-- Laith -->

<?php
include __DIR__ . '/../layout/header.php';

// Zeigt eine Fehlermeldung an, wenn beim letzten Login-Versuch ein Fehler aufgetreten ist
if (!empty($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>

<div class="form-wrapper">
  <div align="center">
    <h1>Login</h1>

    <!-- Anzeige von Statusmeldungen -->
    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p> <!-- Fehlermeldung bei falschem Login -->
    <?php elseif (!empty($_GET['registered'])): ?>
      <p class="success">Registrierung erfolgreich. Bitte einloggen.</p> <!-- Erfolg nach Registrierung -->
    <?php elseif (!empty($_GET['logout'])): ?>
      <p class="success">Du wurdest erfolgreich abgemeldet.</p> <!-- Erfolg nach Logout -->
    <?php endif; ?>

    <!-- Login-Formular -->
    <form 
      id="loginForm" 
      action="index.php?page=auth&action=login<?php if (isset($_GET['redirect'])) echo '&redirect=' . urlencode($_GET['redirect']); ?>" 
      method="post" 
      autocomplete="on">

      <!-- E-Mail-Feld -->
      <label for="loginEmail">E-Mail:</label>
      <input type="email" name="email" id="loginEmail" required>
      <small></small><br>

      <!-- Passwort-Feld -->
      <label for="loginPassword">Passwort:</label>
      <input type="password" name="password" id="loginPassword" required minlength="10">
      <small></small><br>

      <!-- Submit-Button ist initial deaktiviert -->
      <button type="submit" id="loginBtn" disabled>Login</button>
    </form>

    <!-- Link zur Registrierung -->
    <a href="index.php?page=auth&action=register" class="btn-link">Zur Registrierung</a>
  </div>
</div>

<!-- Scroll-To-Top Button -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<!-- Validierungsskript -->
<script>
  // Validiert E-Mail-Format
  function validateEmail(input) {
    const value = input.value.trim();
    const valid = /\S+@\S+\.\S+/.test(value);
    setValidation(input, valid, "Bitte eine gültige E-Mail eingeben.");
    return valid;
  }

  // Validiert Passwortlänge
  function validatePassword(input) {
    const value = input.value;
    const valid = value.length >= 10;
    setValidation(input, valid, "Passwort muss mindestens 10 Zeichen lang sein.");
    return valid;
  }

  // Setzt optisches Feedback für Validierungen
  function setValidation(input, isValid, message) {
    const small = input.nextElementSibling;
    input.classList.toggle("valid", isValid);
    input.classList.toggle("invalid", !isValid);
    small.textContent = isValid ? "" : message;
  }

  // Referenzen auf Felder und Button
  const emailInput = document.getElementById("loginEmail");
  const passInput = document.getElementById("loginPassword");
  const loginBtn = document.getElementById("loginBtn");

  // Überprüft Formular bei jeder Eingabe
  [emailInput, passInput].forEach(input => input.addEventListener("input", () => {
    const valid = validateEmail(emailInput) && validatePassword(passInput);
    loginBtn.disabled = !valid;
  }));
</script>

<?php 
include __DIR__ . '/../layout/footer.php'; 
?>
