<?php
declare(strict_types=1);
require_once __DIR__ . '/banners-helper.php';
require_once __DIR__ . '/audit-helper.php';
if (!$user) redirectAdmin('/login');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newFiles = [];
    try {
        csrfCheck();
        $action = (string)($_POST['action'] ?? '');
        if ($action === 'delete') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            if (!$id || $id < 1) throw new RuntimeException('Banner inválido.');
            $pdo = database();
            $q = $pdo->prepare('SELECT desktop_image,mobile_image FROM home_banners WHERE id=?');
            $q->execute([$id]); $existing = $q->fetch();
            if (!$existing) throw new RuntimeException('Banner não encontrado.');
            $q = $pdo->prepare('DELETE FROM home_banners WHERE id=?');
            $q->execute([$id]);
            deleteBannerImage($existing['desktop_image']);
            deleteBannerImage($existing['mobile_image']);
            auditEvent($user, 'banner.deleted', 'banner', (int)$id, 'Banner excluído');
            flash('ok','Banner excluído.');
            redirectAdmin('/banners');
        }
        if ($action !== 'save' || !in_array($section, ['banners/novo','banners/editar'], true)) {
            throw new RuntimeException('Operação de banner inválida.');
        }
        $isEdit = $section === 'banners/editar';
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($isEdit && (!$id || $id < 1)) throw new RuntimeException('Banner inválido.');
        if (!$isEdit && $id) throw new RuntimeException('Cadastro inválido.');
        $link = bannerUrl((string)($_POST['link_url'] ?? ''));
        $pdo = database();
        $gotLock = (int)$pdo->query("SELECT GET_LOCK('eternity_banners_limit', 5)")->fetchColumn();
        if ($gotLock !== 1) throw new RuntimeException('Outro banner está sendo salvo. Tente novamente.');
        try {
            $existing = null;
            if ($isEdit) {
                $q = $pdo->prepare('SELECT * FROM home_banners WHERE id=?');
                $q->execute([$id]); $existing = $q->fetch();
                if (!$existing) throw new RuntimeException('Banner não encontrado.');
            } else {
                $count = (int)$pdo->query('SELECT COUNT(*) FROM home_banners')->fetchColumn();
                if ($count >= 4) throw new RuntimeException('Limite de quatro banners atingido.');
            }
            $desktop = $existing['desktop_image'] ?? '';
            $mobile = $existing['mobile_image'] ?? '';
            $desktopUpload = $_FILES['desktop_image'] ?? null;
            $mobileUpload = $_FILES['mobile_image'] ?? null;
            if (bannerFilePresent($desktopUpload)) {
                $desktop = saveBannerImage($desktopUpload, 1600, 600);
                $newFiles[] = $desktop;
            } elseif (!$isEdit) throw new RuntimeException('Selecione a imagem desktop (1600x600).');
            if (bannerFilePresent($mobileUpload)) {
                $mobile = saveBannerImage($mobileUpload, 1000, 600);
                $newFiles[] = $mobile;
            } elseif (!$isEdit) throw new RuntimeException('Selecione a imagem mobile (1000x600).');

            if ($isEdit) {
                $q = $pdo->prepare('UPDATE home_banners SET desktop_image=?, mobile_image=?, link_url=? WHERE id=?');
                $q->execute([$desktop,$mobile,$link,$id]);
            } else {
                $q = $pdo->prepare('INSERT INTO home_banners (desktop_image,mobile_image,link_url) VALUES (?,?,?)');
                $q->execute([$desktop,$mobile,$link]);
            }
            if ($existing) {
                if ($desktop !== $existing['desktop_image']) deleteBannerImage($existing['desktop_image']);
                if ($mobile !== $existing['mobile_image']) deleteBannerImage($existing['mobile_image']);
            }
            $newFiles = [];
            auditEvent($user, $isEdit ? 'banner.updated' : 'banner.created', 'banner', $isEdit ? (int)$id : (int)$pdo->lastInsertId(), 'Banner atualizado no site');
            flash('ok', $isEdit ? 'Banner atualizado.' : 'Banner criado.');
            redirectAdmin('/banners');
        } finally {
            $pdo->query("SELECT RELEASE_LOCK('eternity_banners_limit')");
        }
    } catch (Throwable $exception) {
        foreach ($newFiles as $file) deleteBannerImage($file);
        if ($exception instanceof PDOException) {
            error_log('Eternity banner DB: '.$exception->getMessage());
            $message = 'Erro no banco. Execute php scripts/install.php e tente novamente.';
        } else $message = $exception->getMessage();
        flash('error', $message);
        redirectAdmin($section === 'banners/editar' ? '/banners/editar?id='.(int)($_POST['id'] ?? 0) : ($section === 'banners/novo' ? '/banners/novo' : '/banners'));
    }
}
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
require __DIR__ . '/view.php';
