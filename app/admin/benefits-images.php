<?php
declare(strict_types=1);

/** Upload opcional de imagem para estabelecimentos: PNG/JPEG/WebP até 3 MB. */
function benefitImageUploaded(?array $file): bool
{
    return is_array($file) && isset($file['error']) && (int)$file['error'] !== UPLOAD_ERR_NO_FILE;
}
function saveBenefitImage(array $file): string
{
    if (($file['error'] ?? null) !== UPLOAD_ERR_OK || !isset($file['tmp_name'])
        || !is_uploaded_file((string)$file['tmp_name'])) {
        throw new RuntimeException('Não foi possível enviar a imagem. Confira o limite de upload da hospedagem.');
    }
    if (!isset($file['size']) || (int)$file['size'] < 1 || (int)$file['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('A imagem deve ter no máximo 3 MB.');
    }
    $tmp = (string)$file['tmp_name'];
    $imageInfo = @getimagesize($tmp);
    $allowed = [
        IMAGETYPE_PNG => ['png','image/png'],
        IMAGETYPE_JPEG => ['jpg','image/jpeg'],
        IMAGETYPE_WEBP => ['webp','image/webp'],
    ];
    if (!$imageInfo || !isset($allowed[(int)$imageInfo[2]])
        || $imageInfo[0] < 1 || $imageInfo[1] < 1
        || $imageInfo[0] > 4096 || $imageInfo[1] > 4096) {
        throw new RuntimeException('Use PNG, JPG ou WebP válido, com no máximo 4096 pixels por lado.');
    }
    $format = $allowed[(int)$imageInfo[2]];
    if ((new finfo(FILEINFO_MIME_TYPE))->file($tmp) !== $format[1]) {
        throw new RuntimeException('Formato de imagem inválido.');
    }
    $directory = dirname(__DIR__, 2) . '/assets/uploads/benefits';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('Não foi possível criar a pasta de imagens dos benefícios.');
    }
    if (!is_writable($directory)) throw new RuntimeException('A pasta de imagens não tem permissão de escrita.');
    $fileName = bin2hex(random_bytes(16)) . '.' . $format[0];
    if (!move_uploaded_file($tmp, $directory . '/' . $fileName)) {
        throw new RuntimeException('Não foi possível salvar a imagem no servidor.');
    }
    @chmod($directory . '/' . $fileName, 0644);
    return '/assets/uploads/benefits/' . $fileName;
}
function deleteBenefitImage(string $imagePath): void
{
    if (preg_match('#^/assets/uploads/benefits/[a-f0-9]{32}\\.(?:png|jpg|webp)$#', $imagePath)) {
        @unlink(dirname(__DIR__, 2) . $imagePath);
    }
}
