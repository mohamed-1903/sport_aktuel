<!-- Mohamed -->
<?php include __DIR__ . '/../layout/header.php'; ?>

<!-- Hauptbereich der Benutzerverwaltung -->
<section class="form-wrapper" style="padding: 2em; max-width: 10000px; margin: 20px;">
  <h1 style="text-align: center;">👥 Benutzerverwaltung</h1>

  <?php if (empty($allUsers)): ?>
    <!-- Hinweis bei leerer Benutzerliste -->
    <p style="text-align: center;">Es wurden keine Benutzer gefunden.</p>
  <?php else: ?>
    <!-- Tabelle mit Benutzerübersicht -->
    <div class="table-responsive">
      <table class="cart-table">
      <thead>
        <tr>
          <th>Id-#</th> <!-- Benutzer-ID -->
          <th>Benutzername</th> <!-- Anzeigename -->
          <th>E-Mail</th> <!-- Kontaktadresse -->
          <th>Registriert am</th> <!-- Datum der Registrierung -->
          <th>Status</th> <!-- z.B. "aktiv" oder "banned" -->
          <th>Aktionen</th> <!-- Admin-Aktionen -->
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
              
              <!-- 🗑 Benutzer löschen mit Sicherheitsabfrage -->
              <form action="index.php?page=admin&action=deleteUser" method="post" 
                    onsubmit="return confirm('Benutzer wirklich löschen?');">
                <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                <button type="submit" class="btn-delete-all">Entfernen</button>
              </form>

              <!-- 🔒 Benutzer sperren / freigeben -->
              <form action="index.php?page=admin&action=toggleUserStatus" method="post">
                <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                <?php if ($user['status'] === 'banned'): ?>
                  <!-- Freischalten -->
                  <button type="submit" class="btn-checkout">Freigeben</button>
                <?php else: ?>
                  <!-- Sperren -->
                  <button type="submit" class="btn-delete-all">Sperren</button>
                <?php endif; ?>
              </form>

            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>

<!-- Nach-oben-Button -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<?php include __DIR__ . '/../layout/footer.php'; ?>