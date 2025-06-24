<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="form-wrapper" style="padding: 2em;">
  <h1 style="text-align: center;">👥 Benutzerverwaltung</h1>

  <?php if (empty($allUsers)): ?>
    <p style="text-align: center;">Es wurden keine Benutzer gefunden.</p>
  <?php else: ?>
    <table class="cart-table" style="margin: auto;">
      <thead>
        <tr>
          <th>#</th>
          <th>Benutzername</th>
          <th>E-Mail</th>
          <th>Registriert am</th>
          <th>Aktionen</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($allUsers as $user): ?>
          <tr>
            <td><?= (int)$user['id'] ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= date("d.m.Y H:i", strtotime($user['created_at'])) ?></td>
            <td>
              <!-- Inaktiv: <a href="#"><button>Löschen</button></a> -->
              <form action="index.php?page=admin&action=deleteUser" method="post" onsubmit="return confirm('Benutzer wirklich löschen?');">
                <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                <button type="submit" class="btn-delete">❌ Entfernen</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<?php include __DIR__ . '/../layout/footer.php'; ?>

