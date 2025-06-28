<?php include __DIR__ . '/../layout/header.php'; ?>

<section class="form-wrapper" style="padding: 2em; max-width: 10000px; margin-top: 15px;">
  <h1 style="text-align: center;">👥 Benutzerverwaltung</h1>
  <?php if (empty($allUsers)): ?>
    <p style="text-align: center;">Es wurden keine Benutzer gefunden.</p>
  <?php else: ?>
    <table class="cart-table">
      <thead>
        <tr>
          <th>Id-#</th>
          <th>Benutzername</th>
          <th>E-Mail</th>
          <th>Registriert am</th>
          <th>Status</th>
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
            <td><?= htmlspecialchars($user['status']) ?></td>
            <td style="display: flex; gap: 0.5em; justify-content: center; align-items: center;">
              <!-- Inaktiv: <a href="#"><button>Löschen</button></a> -->
              <form action="index.php?page=admin&action=deleteUser" method="post" onsubmit="return confirm('Benutzer wirklich löschen?');">
                <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                <button type="submit" class="btn-delete-all">Entfernen</button>
              </form>
              <form action="index.php?page=admin&action=toggleUserStatus" method="post">
                <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                <?php if ($user['status'] === 'banned'): ?>
                  <button type="submit" class="btn-checkout">Freigeben</button>
                <?php else: ?>
                  <button type="submit" class="btn-delete-all">Sperren</button>
                <?php endif; ?>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<?php include __DIR__ . '/../layout/footer.php'; ?>