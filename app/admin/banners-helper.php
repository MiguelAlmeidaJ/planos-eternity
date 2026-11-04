<?php
declare(strict_types=1);

function bannerUrl(string $value): string
{
    $value = trim($value);
    if ($value === '') return '';
    if (strlen($value) > 500 || preg_match('/[\x00-\x1F\x7F]/', $value)) {
        throw new RuntimeException('Link inválido ou muito longo.');
    }
    if (str_starts_with($value, '/') && !str_starts_with($value, '//') && !str_contains($value, '\\')) {
        return $value;
    }
    $parts = parse_url($value);
    if (!$parts || !in_array(strtolower($parts['scheme'] ?? ''), ['http','https'], true)
        || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
        throw new RuntimeException('Use um link https://, http:// ou caminho interno iniciado com /.');
    }
    return $value;
}
function bannerFilePresent(?array $file): bool
{
    return is_array($file) && isset($file['error']) && (int)$file['error'] !== UPLOAD_ERR_NO_FILE;
}
function saveBannerImage(array $file, int $width, int $height): string
{
    if (!isset($file['error']) || (int)$file['error'] !== UPLOAD_ERR_OK
        || empty($file['tmp_name']) || !is_uploaded_file((string)$file['tmp_name'])) {
        throw new RuntimeException('Não foi possível receber a imagem. Verifique o limite de upload do PHP.');
    }
    if (!isset($file['size']) || (int)$file['size'] < 1 || (int)$file['size'] > 6 * 1024 * 1024) {
        throw new RuntimeException('Cada imagem deve ter no máximo 6 MB.');
    }
    $tmp = (string)$file['tmp_name'];
    $info = @getimagesize($tmp);
    if (!$info || $info[0] !== $width || $info[1] !== $height) {
        throw new RuntimeException("A imagem precisa ter exatamente {$width}x{$height} pixels.");
    }
    $extensions = [IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_WEBP => 'webp'];
    $type = (int)$info[2];
    if (!isset($extensions[$type])) {
        throw new RuntimeException('Envie uma imagem PNG, JPG ou WebP.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    $accepted = [IMAGETYPE_PNG => 'image/png', IMAGETYPE_JPEG => 'image/jpeg', IMAGETYPE_WEBP => 'image/webp'];
    if ($mime !== $accepted[$type]) throw new RuntimeException('Formato de imagem inconsistente.');
    $dir = dirname(__DIR__, 2) . '/assets/uploads/banners';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Não foi possível criar a pasta de banners.');
    }
    if (!is_writable($dir)) throw new RuntimeException('A pasta de banners não permite gravação.');
    $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$type];
    $destination = $dir . '/' . $filename;
    if (!move_uploaded_file($tmp, $destination)) throw new RuntimeException('Falha ao gravar a imagem no servidor.');
    @chmod($destination, 0644);
    return '/assets/uploads/banners/' . $filename;
}
function deleteBannerImage(string $path): void
{
    if (preg_match('#^/assets/uploads/banners/[a-f0-9]{32}\.(?:png|jpg|webp)$#', $path)) {
        @unlink(dirname(__DIR__, 2) . $path);
    }
}
