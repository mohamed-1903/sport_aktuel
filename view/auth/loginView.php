
<?php
include 'view/layout/header.php';

// Fehlermeldung aus der vorherigen Sitzung anzeigen
if (!empty($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>


<div class="form-wrapper">
  <div align="center">
    <h1>Login</h1>

    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif (!empty($_GET['registered'])): ?>
      <p class="success">Registrierung erfolgreich. Bitte einloggen.</p>
    <?php elseif (!empty($_GET['logout'])): ?>
      <p class="success">Du wurdest erfolgreich abgemeldet.</p>
    <?php endif; ?>

    <form id="loginForm" action="index.php?page=auth&action=login<?php if (isset($_GET['redirect'])) echo '&redirect=' . urlencode($_GET['redirect']); ?>" method="post" autocomplete="on">
      <label for="loginEmail">E-Mail:</label><br>
      <input type="email" name="email" id="loginEmail" required><br>
      <small></small><br>

      <label for="loginPassword">Passwort:</label><br>
      <input type="password" name="password" id="loginPassword" required minlength="10"><br>
      <small></small><br>

      <button type="submit" id="loginBtn" disabled>Login</button>
    </form>

    <a href="index.php?page=auth&action=register" class="btn-link">Zur Registrierung</a>
  </div>
</div>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<script>
  function validateEmail(input) {
    const value = input.value.trim();
    const valid = /\S+@\S+\.\S+/.test(value);
    setValidation(input, valid, "Bitte eine gültige E-Mail eingeben.");
    return valid;
  }

  function validatePassword(input) {
    const value = input.value;
    const valid = value.length >= 10;
    setValidation(input, valid, "Passwort muss mindestens 10 Zeichen lang sein.");
    return valid;
  }

  function setValidation(input, isValid, message) {
    const small = input.nextElementSibling;
    input.classList.toggle("valid", isValid);
    input.classList.toggle("invalid", !isValid);
    small.textContent = isValid ? "" : message;
  }

  const emailInput = document.getElementById("loginEmail");
  const passInput = document.getElementById("loginPassword");
  const loginBtn = document.getElementById("loginBtn");

  [emailInput, passInput].forEach(input => input.addEventListener("input", () => {
    const valid = validateEmail(emailInput) && validatePassword(passInput);
    loginBtn.disabled = !valid;
  }));
</script>
<?php include 'view/layout/footer.php'; ?>

