<?php
/**
 * create_ticket.php — Formulaire de création d'un ticket (étudiant uniquement)
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';

// Seuls les étudiants peuvent créer des tickets
requireRole('student');

$errors = [];
$form   = ['title' => '', 'description' => '', 'category' => '', 'priority' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $form['title']       = post('title');
    $form['description'] = post('description');
    $form['category']    = post('category');
    $form['priority']    = post('priority');

    // ─── Validation ───
    if (empty($form['title'])) {
        $errors[] = 'Le titre est obligatoire.';
    } elseif (mb_strlen($form['title']) > 150) {
        $errors[] = 'Le titre ne doit pas dépasser 150 caractères.';
    }

    if (empty($form['description'])) {
        $errors[] = 'La description est obligatoire.';
    } elseif (mb_strlen($form['description']) > 3000) {
        $errors[] = 'La description ne doit pas dépasser 3000 caractères.';
    }

    if (!in_array($form['category'], TICKET_CATEGORIES, true)) {
        $errors[] = 'Catégorie invalide.';
    }

    if (!in_array($form['priority'], TICKET_PRIORITIES, true)) {
        $errors[] = 'Priorité invalide.';
    }

    // ─── Création si pas d'erreur ───
    if (empty($errors)) {
        $newId = createTicket([
            'author'      => $_SESSION['username'],
            'author_name' => $_SESSION['name'],
            'title'       => $form['title'],
            'description' => $form['description'],
            'category'    => $form['category'],
            'priority'    => $form['priority'],
        ]);
        setFlash('success', 'Ticket #' . $newId . ' créé avec succès !');
        redirect("ticket.php?id=$newId");
    }
}

$pageTitle = 'Nouveau ticket';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <a href="tickets.php" class="back-link">← Retour aux tickets</a>
        <h1 class="page-title">Créer un ticket</h1>
        <p class="page-subtitle">Décrivez votre problème avec le plus de détails possible.</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <strong>Veuillez corriger les erreurs suivantes :</strong>
    <ul>
        <?php foreach ($errors as $err): ?>
        <li><?= e($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="form-card">
    <form method="POST" action="create_ticket.php" novalidate>

        <!-- Titre -->
        <div class="form-group">
            <label for="title">Titre <span class="required">*</span></label>
            <input
                type="text"
                id="title"
                name="title"
                value="<?= e($form['title']) ?>"
                placeholder="Ex : Erreur dans le TP4 — méthode sort()"
                maxlength="150"
                required
            >
            <small class="form-hint">Maximum 150 caractères.</small>
        </div>

        <!-- Description -->
        <div class="form-group">
            <label for="description">Description <span class="required">*</span></label>
            <textarea
                id="description"
                name="description"
                rows="6"
                placeholder="Décrivez précisément votre problème, ce que vous avez essayé, etc."
                maxlength="3000"
                required
            ><?= e($form['description']) ?></textarea>
            <small class="form-hint">Maximum 3000 caractères.</small>
        </div>

        <!-- Catégorie & Priorité -->
        <div class="form-row">
            <div class="form-group">
                <label for="category">Catégorie <span class="required">*</span></label>
                <select id="category" name="category" required>
                    <option value="">— Choisir —</option>
                    <?php foreach (TICKET_CATEGORIES as $cat): ?>
                    <option value="<?= e($cat) ?>" <?= $form['category'] === $cat ? 'selected' : '' ?>>
                        <?= e($cat) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="priority">Priorité <span class="required">*</span></label>
                <select id="priority" name="priority" required>
                    <option value="">— Choisir —</option>
                    <?php foreach (TICKET_PRIORITIES as $prio): ?>
                    <option value="<?= e($prio) ?>" <?= $form['priority'] === $prio ? 'selected' : '' ?>>
                        <?= e($prio) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="tickets.php" class="btn btn-ghost">Annuler</a>
            <button type="submit" class="btn btn-primary">Créer le ticket</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
