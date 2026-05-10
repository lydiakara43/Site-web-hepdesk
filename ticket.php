<?php
/**
 * ticket.php — Détail d'un ticket, commentaires et gestion du statut
 * Accès :
 *   - Étudiant : uniquement ses propres tickets
 *   - Tuteur   : tous les tickets
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

// ─── Validation de l'ID en GET ───
$ticketId = getInt('id');
if ($ticketId === null) {
    setFlash('error', 'Identifiant de ticket invalide.');
    redirect('tickets.php');
}

// ─── Récupération du ticket ───
$ticket = getTicketById($ticketId);
if ($ticket === null) {
    setFlash('error', 'Ticket introuvable.');
    redirect('tickets.php');
}

// ─── Contrôle d'accès : un étudiant ne peut voir que ses propres tickets ───
if (isStudent() && $ticket['author'] !== $_SESSION['username']) {
    setFlash('error', 'Accès refusé : ce ticket ne vous appartient pas.');
    redirect('tickets.php');
}

$errors = [];

// ─── Traitement : mise à jour du statut (tuteur uniquement) ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    if (!isTutor()) {
        setFlash('error', 'Action non autorisée.');
        redirect("ticket.php?id=$ticketId");
    }
    $newStatus = post('status');
    if (!in_array($newStatus, TICKET_STATUSES, true)) {
        $errors[] = 'Statut invalide.';
    } else {
        updateTicketStatus($ticketId, $newStatus);
        setFlash('success', 'Statut mis à jour avec succès.');
        redirect("ticket.php?id=$ticketId");
    }
}

// ─── Traitement : ajout de commentaire ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $message = post('message');
    if (empty($message)) {
        $errors[] = 'Le commentaire ne peut pas être vide.';
    } elseif (mb_strlen($message) > 2000) {
        $errors[] = 'Le commentaire ne doit pas dépasser 2000 caractères.';
    } else {
        addComment($ticketId, $_SESSION['username'], $_SESSION['name'], $message);
        setFlash('success', 'Commentaire ajouté.');
        redirect("ticket.php?id=$ticketId");
    }
}

// ─── Rechargement du ticket après modification ───
$ticket   = getTicketById($ticketId);
$comments = getCommentsByTicket($ticketId);

$pageTitle = 'Ticket #' . $ticketId;
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <a href="tickets.php" class="back-link">← Retour aux tickets</a>
        <h1 class="page-title">Ticket #<?= (int)$ticket['id'] ?></h1>
    </div>
    <?php if (isTutor()): ?>
    <form method="POST" action="ticket.php?id=<?= (int)$ticketId ?>" class="status-form">
        <input type="hidden" name="action" value="update_status">
        <select name="status" class="filter-select">
            <?php foreach (TICKET_STATUSES as $s): ?>
            <option value="<?= e($s) ?>" <?= $ticket['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Mettre à jour</button>
    </form>
    <?php endif; ?>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <?php foreach ($errors as $err): ?>
    <p><?= e($err) ?></p>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Détail du ticket -->
<div class="ticket-detail-card">
    <div class="ticket-detail-header">
        <h2 class="ticket-detail-title"><?= e($ticket['title']) ?></h2>
        <div class="ticket-detail-badges">
            <span class="badge <?= statusClass($ticket['status']) ?>"><?= e($ticket['status']) ?></span>
            <span class="badge <?= priorityClass($ticket['priority']) ?>"><?= e($ticket['priority']) ?></span>
            <span class="badge badge-category"><?= e($ticket['category']) ?></span>
        </div>
    </div>

    <div class="ticket-meta-row">
        <span>👤 <strong><?= e($ticket['author_name']) ?></strong></span>
        <span>📅 <?= formatDate($ticket['created_at']) ?></span>
    </div>

    <div class="ticket-description">
        <?= nl2br(e($ticket['description'])) ?>
    </div>
</div>

<!-- Section commentaires -->
<div class="section">
    <h2 class="section-title">
        Commentaires
        <span class="comment-count"><?= count($comments) ?></span>
    </h2>

    <?php if (empty($comments)): ?>
    <p class="no-comments">Aucun commentaire pour l'instant. Soyez le premier à répondre.</p>
    <?php else: ?>
    <div class="comments-list">
        <?php foreach ($comments as $comment): ?>
        <div class="comment-card <?= $comment['author'] === 'tuteur' || (isset($comment['role']) && $comment['role'] === 'tutor') ? 'comment-tutor' : '' ?>">
            <div class="comment-header">
                <strong class="comment-author"><?= e($comment['author_name']) ?></strong>
                <span class="comment-date"><?= formatDate($comment['created_at']) ?></span>
            </div>
            <div class="comment-body">
                <?= nl2br(e($comment['message'])) ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Formulaire d'ajout de commentaire -->
    <div class="comment-form-wrapper">
        <h3>Ajouter un commentaire</h3>
        <form method="POST" action="ticket.php?id=<?= (int)$ticketId ?>">
            <input type="hidden" name="action" value="add_comment">
            <div class="form-group">
                <textarea
                    name="message"
                    rows="4"
                    placeholder="Votre message…"
                    maxlength="2000"
                    required
                ></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer le commentaire</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
