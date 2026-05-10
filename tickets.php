<?php
/**
 * tickets.php — Liste complète des tickets avec filtres
 * Étudiant → ses tickets uniquement
 * Tuteur   → tous les tickets
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

// Récupération des tickets
if (isTutor()) {
    $tickets = getAllTickets();
} else {
    $tickets = getTicketsByAuthor($_SESSION['username']);
}

// ─── Filtres GET ───
$filterStatus   = get('status');
$filterCategory = get('category');
$filterPriority = get('priority');
$search         = get('q');

// Application des filtres
if ($filterStatus && in_array($filterStatus, TICKET_STATUSES, true)) {
    $tickets = array_values(array_filter($tickets, fn($t) => $t['status'] === $filterStatus));
}
if ($filterCategory && in_array($filterCategory, TICKET_CATEGORIES, true)) {
    $tickets = array_values(array_filter($tickets, fn($t) => $t['category'] === $filterCategory));
}
if ($filterPriority && in_array($filterPriority, TICKET_PRIORITIES, true)) {
    $tickets = array_values(array_filter($tickets, fn($t) => $t['priority'] === $filterPriority));
}
if ($search !== '') {
    $tickets = array_values(array_filter($tickets, function($t) use ($search) {
        return stripos($t['title'], $search) !== false
            || stripos($t['description'], $search) !== false;
    }));
}

$pageTitle = 'Mes tickets';
if (isTutor()) $pageTitle = 'Tous les tickets';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><?= isTutor() ? 'Tous les tickets' : 'Mes tickets' ?></h1>
        <p class="page-subtitle"><?= count($tickets) ?> ticket(s) trouvé(s)</p>
    </div>
    <?php if (isStudent()): ?>
    <a href="create_ticket.php" class="btn btn-primary">+ Nouveau ticket</a>
    <?php endif; ?>
</div>

<!-- Barre de filtres -->
<form method="GET" action="tickets.php" class="filter-bar">
    <input
        type="text"
        name="q"
        placeholder="Rechercher…"
        value="<?= e($search) ?>"
        class="filter-search"
    >

    <select name="status" class="filter-select">
        <option value="">Tous les statuts</option>
        <?php foreach (TICKET_STATUSES as $s): ?>
        <option value="<?= e($s) ?>" <?= $filterStatus === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="category" class="filter-select">
        <option value="">Toutes catégories</option>
        <?php foreach (TICKET_CATEGORIES as $c): ?>
        <option value="<?= e($c) ?>" <?= $filterCategory === $c ? 'selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="priority" class="filter-select">
        <option value="">Toutes priorités</option>
        <?php foreach (TICKET_PRIORITIES as $p): ?>
        <option value="<?= e($p) ?>" <?= $filterPriority === $p ? 'selected' : '' ?>><?= e($p) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
    <?php if ($filterStatus || $filterCategory || $filterPriority || $search): ?>
    <a href="tickets.php" class="btn btn-ghost btn-sm">Réinitialiser</a>
    <?php endif; ?>
</form>

<!-- Liste des tickets -->
<?php if (empty($tickets)): ?>
<div class="empty-state">
    <p>Aucun ticket ne correspond à votre recherche.</p>
</div>
<?php else: ?>
<div class="ticket-table-wrapper">
    <table class="ticket-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Titre</th>
                <?php if (isTutor()): ?><th>Auteur</th><?php endif; ?>
                <th>Catégorie</th>
                <th>Priorité</th>
                <th>Statut</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td class="td-id">#<?= (int)$ticket['id'] ?></td>
                <td class="td-title"><?= e($ticket['title']) ?></td>
                <?php if (isTutor()): ?>
                <td><?= e($ticket['author_name']) ?></td>
                <?php endif; ?>
                <td><span class="badge badge-category"><?= e($ticket['category']) ?></span></td>
                <td><span class="badge <?= priorityClass($ticket['priority']) ?>"><?= e($ticket['priority']) ?></span></td>
                <td><span class="badge <?= statusClass($ticket['status']) ?>"><?= e($ticket['status']) ?></span></td>
                <td class="td-date"><?= formatDate($ticket['created_at']) ?></td>
                <td>
                    <a href="ticket.php?id=<?= (int)$ticket['id'] ?>" class="btn btn-ghost btn-xs">Voir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
