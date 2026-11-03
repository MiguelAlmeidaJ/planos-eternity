<?php
declare(strict_types=1);
require_once __DIR__ . '/helpers.php';
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header('Cache-Control: no-store');
$section = trim(substr($path, strlen('admin')), '/');
$valid = ['', 'login','logout','usuarios','planos','configuracoes'];
if (!in_array($section, $valid, true)) { http_response_code(404); exit('Página administrativa não encontrada.'); }
$user = adminUser();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrfCheck();
        if ($section === 'login') {
            $attempts = $_SESSION['login_attempts'] ?? [];
            $attempts = array_values(array_filter($attempts, static fn($t) => $t > time()-900));
            if (count($attempts) >= 5) throw new RuntimeException('Muitas tentativas. Tente novamente em 15 minutos.');
            $email = mb_strtolower(trim((string)($_POST['email'] ?? '')));
            $query = database()->prepare('SELECT id,password_hash FROM admin_users WHERE email=? AND active=1 LIMIT 1');
            $query->execute([$email]);
            $candidate = $query->fetch();
            if (!$candidate || !password_verify((string)($_POST['password'] ?? ''), $candidate['password_hash'])) {
                $attempts[] = time(); $_SESSION['login_attempts'] = $attempts;
                throw new RuntimeException('Credenciais inválidas.');
            }
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$candidate['id'];
            unset($_SESSION['login_attempts']);
            redirectAdmin('');
        }
        if (!$user) redirectAdmin('/login');
        if ($section === 'logout') {
            $_SESSION = [];
            session_regenerate_id(true);
            redirectAdmin('/login');
        }
        $action = (string)($_POST['action'] ?? '');
        if ($section === 'usuarios') {
            requireAdmin($user, true);
            if ($action === 'save') {
                $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
                $name = trim((string)($_POST['name'] ?? ''));
                $email = mb_strtolower(trim((string)($_POST['email'] ?? '')));
                $role = (string)($_POST['role'] ?? 'editor');
                $active = isset($_POST['active']) ? 1 : 0;
                $password = (string)($_POST['password'] ?? '');
                if (mb_strlen($name)<3 || mb_strlen($name)>120 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>190 || !in_array($role,['admin','editor'],true)) throw new RuntimeException('Dados do usuário inválidos.');
                if ($id) {
                    $old = database()->prepare('SELECT id,role,active FROM admin_users WHERE id=?');$old->execute([$id]);$existing=$old->fetch();
                    if (!$existing) throw new RuntimeException('Usuário não encontrado.');
                    if ($id === (int)$user['id'] && ($role!=='admin' || !$active)) throw new RuntimeException('Não é possível remover seu próprio acesso.');
                    if ($existing['role']==='admin' && (int)$existing['active']===1 && ($role!=='admin' || !$active)) {
                        $count=(int)database()->query("SELECT COUNT(*) FROM admin_users WHERE role='admin' AND active=1")->fetchColumn();
                        if ($count <= 1) throw new RuntimeException('Mantenha pelo menos um administrador ativo.');
                    }
                    if ($password !== '' && strlen($password)<12) throw new RuntimeException('A senha deve ter pelo menos 12 caracteres.');
                    if ($password !== '') {
                        $q=database()->prepare('UPDATE admin_users SET name=?,email=?,role=?,active=?,password_hash=? WHERE id=?');
                        $q->execute([$name,$email,$role,$active,password_hash($password,PASSWORD_DEFAULT),$id]);
                    } else {
                        $q=database()->prepare('UPDATE admin_users SET name=?,email=?,role=?,active=? WHERE id=?');
                        $q->execute([$name,$email,$role,$active,$id]);
                    }
                } else {
                    if (strlen($password)<12) throw new RuntimeException('Informe uma senha com ao menos 12 caracteres.');
                    $q=database()->prepare('INSERT INTO admin_users (name,email,role,active,password_hash) VALUES (?,?,?,?,?)');
                    $q->execute([$name,$email,$role,$active,password_hash($password,PASSWORD_DEFAULT)]);
                }
                flash('ok','Usuário salvo.');
            } elseif ($action === 'delete') {
                $id=(int)($_POST['id'] ?? 0);
                if ($id === (int)$user['id']) throw new RuntimeException('Não é possível excluir sua própria conta.');
                $target=database()->prepare('SELECT role,active FROM admin_users WHERE id=?');$target->execute([$id]);$data=$target->fetch();
                if ($data && $data['role']==='admin' && (int)$data['active']===1) {
                    $count=(int)database()->query("SELECT COUNT(*) FROM admin_users WHERE role='admin' AND active=1")->fetchColumn();
                    if ($count<=1) throw new RuntimeException('Mantenha pelo menos um administrador ativo.');
                }
                $q=database()->prepare('DELETE FROM admin_users WHERE id=?');$q->execute([$id]);
                flash('ok','Usuário excluído.');
            }
            redirectAdmin('/usuarios');
        }
        if ($section === 'planos') {
            if ($action === 'toggle') {
                $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
                if (!$id || $id < 1) throw new RuntimeException('Plano inválido.');
                $stmt = database()->prepare('UPDATE site_plans SET active = CASE WHEN active = 1 THEN 0 ELSE 1 END WHERE id = ?');
                $stmt->execute([$id]);
                if ($stmt->rowCount() !== 1) throw new RuntimeException('Plano não encontrado.');
                flash('ok', 'Visibilidade atualizada.');
                redirectAdmin('/planos');
            }
            if ($action === 'save') {
                $id=(int)($_POST['id'] ?? 0);
                $name=trim((string)($_POST['name'] ?? ''));
                $slug=trim((string)($_POST['slug'] ?? ''));
                $tagline=trim((string)($_POST['tagline'] ?? ''));
                $description=trim((string)($_POST['description'] ?? ''));
                $features=trim((string)($_POST['features'] ?? ''));
                $price=trim((string)($_POST['price_label'] ?? ''));
                $order=(int)($_POST['sort_order'] ?? 0);
                $featured=isset($_POST['featured'])?1:0;
                $active=isset($_POST['active'])?1:0;
                if ($name==='' || mb_strlen($name)>120 || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/',$slug) || strlen($slug)>100 || mb_strlen($tagline)>180 || mb_strlen($price)>120 || mb_strlen($description)>5000 || mb_strlen($features)>5000) throw new RuntimeException('Revise os dados do plano e o slug (letras minúsculas e hífens).');
                if ($id) {
                    $q=database()->prepare('UPDATE site_plans SET slug=?,name=?,tagline=?,description=?,features=?,price_label=?,sort_order=?,featured=?,active=? WHERE id=?');
                    $q->execute([$slug,$name,$tagline,$description,$features,$price,$order,$featured,$active,$id]);
                } else {
                    $q=database()->prepare('INSERT INTO site_plans (slug,name,tagline,description,features,price_label,sort_order,featured,active) VALUES (?,?,?,?,?,?,?,?,?)');
                    $q->execute([$slug,$name,$tagline,$description,$features,$price,$order,$featured,$active]);
                }
                flash('ok','Plano salvo.');
            } elseif ($action === 'delete') {
                $q=database()->prepare('DELETE FROM site_plans WHERE id=?');$q->execute([(int)($_POST['id'] ?? 0)]);
                flash('ok','Plano excluído.');
            }
            redirectAdmin('/planos');
        }
        if ($section === 'configuracoes') {
            requireAdmin($user,true);
            $fields = ['APP_NAME'=>120,'BRAND_SUBTITLE'=>100,'BRAND_LOGO'=>255,'BRAND_PRIMARY'=>7,'BRAND_ACCENT'=>7,'APP_PHONE'=>50,'APP_WHATSAPP'=>50,'APP_EMAIL'=>190,'APP_ADDRESS'=>255,'APP_CNPJ'=>30,'SMTP_HOST'=>190,'SMTP_PORT'=>5,'SMTP_USER'=>190,'SMTP_FROM'=>190,'SMTP_FROM_NAME'=>120,'SMTP_ENCRYPTION'=>5];
            foreach ($fields as $field=>$max) {
                $value=trim((string)($_POST[$field] ?? ''));
                if (mb_strlen($value)>$max) throw new RuntimeException('Campo muito longo: '.$field);
                if (in_array($field,['BRAND_PRIMARY','BRAND_ACCENT'],true) && !preg_match('/^#[0-9a-fA-F]{6}$/',$value)) throw new RuntimeException('Cor inválida: '.$field);
                if ($field==='BRAND_LOGO' && $value!=='' && (!str_starts_with($value,'/assets/') || !preg_match('#^/assets/[a-zA-Z0-9_./-]+$#',$value) || str_contains($value,'..'))) throw new RuntimeException('Use um caminho local em /assets/ para o logotipo.');
                if (in_array($field,['APP_EMAIL','SMTP_FROM'],true) && $value!=='' && !filter_var($value,FILTER_VALIDATE_EMAIL)) throw new RuntimeException('E-mail inválido: '.$field);
                if ($field==='SMTP_ENCRYPTION' && !in_array($value,['','tls','ssl'],true)) throw new RuntimeException('Segurança SMTP inválida.');
                if ($field==='SMTP_PORT' && ($value==='' || !ctype_digit($value) || (int)$value<1 || (int)$value>65535)) throw new RuntimeException('Porta SMTP inválida.');
                saveOption($field,$value);
            }
            $secret=(string)($_POST['SMTP_PASSWORD'] ?? '');
            if ($secret !== '') saveOption('SMTP_PASSWORD_ENC',encryptSecret($secret));
            if (isset($_POST['clear_smtp_password'])) saveOption('SMTP_PASSWORD_ENC','');
            flash('ok','Configurações salvas.');
            redirectAdmin('/configuracoes');
        }
        throw new RuntimeException('Operação não reconhecida.');
    } catch (Throwable $ex) {
        if ($ex instanceof PDOException) {
            error_log('Admin DB error: '.$ex->getMessage());
            $message = 'Erro no banco de dados. Confira duplicidade de e-mails ou slugs.';
        } else $message = $ex->getMessage();
        flash('error',$message);
        redirectAdmin($section === 'login' ? '/login' : '/'.$section);
    }
}
if (!$user && $section!=='login') redirectAdmin('/login');
if ($user && $section==='login') redirectAdmin('');
if ($user && in_array($section,['usuarios','configuracoes'],true) && $user['role']!=='admin') { http_response_code(403); exit('Acesso restrito.'); }
$flash=$_SESSION['flash']??null;unset($_SESSION['flash']);
require __DIR__ . '/view.php';
