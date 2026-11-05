<?php
$allowedAuditActions = ['', 'auth.login', 'auth.failed', 'auth.logout', 'user.created', 'user.updated',
  'user.deleted','plan.created','plan.updated','plan.deleted','plan.visibility','plan.reordered',
  'banner.created','banner.updated','banner.deleted','branding.uploaded','branding.removed',
  'settings.updated','smtp.test.success','smtp.test.failed'];
$selectedAction = (string)($_GET['action'] ?? '');
if (!in_array($selectedAction, $allowedAuditActions, true)) $selectedAction = '';
$pageNum = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
$pageNum = max(1, min($pageNum, 100000));
$pageSize = 25;
$where = $selectedAction !== '' ? ' WHERE action = :action' : '';
$countQuery = database()->prepare('SELECT COUNT(*) FROM admin_audit_logs'.$where);
if ($selectedAction !== '') $countQuery->bindValue(':action', $selectedAction);
$countQuery->execute();
$totalLogs = (int)$countQuery->fetchColumn();
$totalPages = max(1, (int)ceil($totalLogs/$pageSize));
$pageNum = min($pageNum, $totalPages);
$offset = ($pageNum - 1)*$pageSize;
$listQuery = database()->prepare('SELECT id,actor_name,action,target_type,target_id,details,ip_address,created_at FROM admin_audit_logs'.$where.' ORDER BY id DESC LIMIT :limit OFFSET :offset');
if ($selectedAction !== '') $listQuery->bindValue(':action', $selectedAction);
$listQuery->bindValue(':limit', $pageSize, PDO::PARAM_INT);
$listQuery->bindValue(':offset', $offset, PDO::PARAM_INT);
$listQuery->execute();
$auditRows = $listQuery->fetchAll();
?>
<div class="audit-admin">
  <header class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div><p class="kicker mb-2">SEGURANÇA E CONTROLE</p><h1>Auditoria</h1><p class="sub mb-0">Histórico de ações no painel administrativo.</p></div>
    <span class="audit-total"><i class="bi bi-shield-check" aria-hidden="true"></i> <?= $totalLogs ?> registro(s)</span>
  </header>
  <div class="panel">
    <form class="audit-filters" method="get" action="/admin/auditoria">
      <label for="audit-action">Filtrar por evento</label>
      <select id="audit-action" name="action" class="form-select">
        <option value="">Todos os eventos</option>
        <?php foreach ($allowedAuditActions as $auditAction): if ($auditAction === '') continue; ?>
          <option value="<?= e($auditAction) ?>" <?= $selectedAction === $auditAction ? 'selected' : '' ?>><?= e($auditAction) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-outline-primary" type="submit"><i class="bi bi-funnel" aria-hidden="true"></i> Filtrar</button>
    </form>
    <div class="table-responsive">
      <table class="table table-hover align-middle audit-table">
        <thead><tr><th>Data / hora</th><th>Usuário</th><th>Evento</th><th>Detalhes</th><th>IP</th></tr></thead>
        <tbody>
          <?php foreach ($auditRows as $entry): ?>
          <tr>
            <td class="text-nowrap"><?= e(date('d/m/Y H:i', strtotime($entry['created_at']))) ?></td>
            <td><?= e($entry['actor_name']) ?></td>
            <td><span class="audit-event"><?= e($entry['action']) ?></span></td>
            <td><?= e($entry['details']) ?><?php if ($entry['target_id'] !== null): ?><small class="d-block text-secondary"><?= e($entry['target_type']) ?> #<?= (int)$entry['target_id'] ?></small><?php endif; ?></td>
            <td class="text-secondary"><?= e($entry['ip_address']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$auditRows): ?><tr><td colspan="5" class="text-center text-secondary py-5">Nenhum registro encontrado.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
    <nav aria-label="Paginação da auditoria" class="audit-pages">
      <span>Página <?= $pageNum ?> de <?= $totalPages ?></span>
      <div>
        <?php if ($pageNum > 1): ?><a class="btn btn-sm btn-outline-secondary" href="/admin/auditoria?page=<?= $pageNum-1 ?>&amp;action=<?= rawurlencode($selectedAction) ?>">Anterior</a><?php endif; ?>
        <?php if ($pageNum < $totalPages): ?><a class="btn btn-sm btn-outline-secondary" href="/admin/auditoria?page=<?= $pageNum+1 ?>&amp;action=<?= rawurlencode($selectedAction) ?>">Próxima</a><?php endif; ?>
      </div>
    </nav>
  </div>
  <p class="small text-secondary">Os registros mostram ações realizadas após a instalação deste módulo. Não são armazenados conteúdos de formulários ou senhas.</p>
</div>
