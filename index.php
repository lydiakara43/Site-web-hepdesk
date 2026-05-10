<?php
/**
 * index.php — Tableau de bord
 * Affiche des statistiques et un aperçu des tickets récents.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

// Récupération des tickets selon le rôle
if (isTutor()) {
    $tickets = getAllTickets();
} else {
    $tickets = getTicketsByAuthor($_SESSION['username']);
}

// Calcul des statistiques
$stats = [
    'total'      => count($tickets),
    'open'       => count(array_filter($tickets, fn($t) => $t['status'] === 'Ouvert')),
    'progress'   => count(array_filter($tickets, fn($t) => $t['status'] === 'En cours')),
    'resolved'   => count(array_filter($tickets, fn($t) => $t['status'] === 'Résolu')),
];

// 5 tickets les plus récents
$recentTickets = array_slice($tickets, 0, 5);

$pageTitle = 'Tableau de bord';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Tableau de bord</h1>
        <p class="page-subtitle">
            Bonjour, <strong><?= e($_SESSION['name']) ?></strong> !
            <?= isTutor() ? 'Vue tuteur — tous les tickets.' : 'Voici vos tickets.' ?>
        </p>
    </div>
    <?php if (isStudent()): ?>
    <a href="create_ticket.php" class="btn btn-primary">+ Nouveau ticket</a>
    <?php endif; ?>
</div>

<!-- Cartes statistiques -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?= $stats['total'] ?></div>
        <div class="stat-label">Total tickets</div>
    </div>
    <div class="stat-card stat-open">
        <div class="stat-number"><?= $stats['open'] ?></div>
        <div class="stat-label">Ouverts</div>
    </div>
    <div class="stat-card stat-progress">
        <div class="stat-number"><?= $stats['progress'] ?></div>
        <div class="stat-label">En cours</div>
    </div>
    <div class="stat-card stat-resolved">
        <div class="stat-number"><?= $stats['resolved'] ?></div>
        <div class="stat-label">Résolus</div>
    </div>
</div>

<!-- Tickets récents -->
<div class="section">
    <div class="section-header">
        <h2>Tickets récents</h2>
        <a href="tickets.php" class="btn btn-ghost btn-sm">Voir tous →</a>
    </div>

    <?php if (empty($recentTickets)): ?>
    <div class="empty-state">
        <p>Aucun ticket pour l'instant.</p>
        <?php if (isStudent()): ?>
        <a href="create_ticket.php" class="btn btn-primary">Créer mon premier ticket</a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="ticket-list">
        <?php foreach ($recentTickets as $ticket): ?>
        <a href="ticket.php?id=<?= (int)$ticket['id'] ?>" class="ticket-row">
            <div class="ticket-row-main">
                <span class="ticket-id">#<?= (int)$ticket['id'] ?></span>
                <span class="ticket-title"><?= e($ticket['title']) ?></span>
            </div>
            <div class="ticket-row-meta">
                <?php if (isTutor()): ?>
                <span class="meta-author"><?= e($ticket['author_name']) ?></span>
                <?php endif; ?>
                <span class="badge <?= statusClass($ticket['status']) ?>"><?= e($ticket['status']) ?></span>
                <span class="badge <?= priorityClass($ticket['priority']) ?>"><?= e($ticket['priority']) ?></span>
                <span class="meta-date"><?= formatDate($ticket['created_at']) ?></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
