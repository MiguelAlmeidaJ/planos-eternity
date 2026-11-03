<?php
declare(strict_types=1);

/**
 * Recebe unicamente PNG ou SVG estático e grava em pasta persistente, ignorada pelo Git.
 * O nome é aleatório, sem aproveitar o nome enviado pelo navegador.
 */
function uploadBrandingAsset(array $file, string $type): string
{
    if (!in_array($type, ['logo', 'favicon'], true)) {
        throw new RuntimeException('Tipo de arquivo inválido.');
    }
    if (!isset($file['error']) || (int)$file['error'] !== UPLOAD_ERR_OK
        || !isset($file['tmp_name'], $file['size']) || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Falha no envio. Escolha uma imagem PNG ou SVG válida.');
    }
    $size = (int)$file['size'];
    if ($size <= 0 || $size > 2 * 1024 * 1024) {
        throw new RuntimeException('A imagem deve ter no máximo 2 MB.');
    }
    $source = (string)$file['tmp_name'];
    $signature = file_get_contents($source, false, null, 0, 8);
    $png = $signature === "\x89PNG\r\n\x1a\n";
    $content = file_get_contents($source);
    if ($content === false) throw new RuntimeException('Não foi possível analisar a imagem.');

    if ($png) {
        $dimensions = @getimagesize($source);
        if (!$dimensions || ($dimensions[2] ?? null) !== IMAGETYPE_PNG
            || $dimensions[0] < 1 || $dimensions[1] < 1
            || $dimensions[0] > 4096 || $dimensions[1] > 4096) {
            throw new RuntimeException('PNG inválido ou com dimensões excessivas.');
        }
        $extension = 'png';
    } else {
        if ($size > 512 * 1024) throw new RuntimeException('SVG deve ter até 512 KB.');
        if (!class_exists(DOMDocument::class)) throw new RuntimeException('A extensão PHP DOM é necessária para validar SVG.');
        $extension = 'svg';
        // Nunca aceitar DTD, entidade, script, evento, URL externa nem conteúdo incorporado.
        if (preg_match('/<!DOCTYPE|<!ENTITY|<\?xml-stylesheet/i', $content)) {
            throw new RuntimeException('Este SVG contém recursos não permitidos.');
        }
        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            if (!$dom->loadXML($content, LIBXML_NONET | LIBXML_COMPACT)) {
                throw new RuntimeException('O arquivo SVG é inválido.');
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        if ($dom->doctype !== null || !$dom->documentElement
            || $dom->documentElement->localName !== 'svg'
            || $dom->documentElement->namespaceURI !== 'http://www.w3.org/2000/svg') {
            throw new RuntimeException('O arquivo precisa ser um SVG puro.');
        }
        $allowedTags = ['svg','g','path','circle','ellipse','rect','line','polyline','polygon',
            'defs','linearGradient','radialGradient','stop','clipPath','mask','title','desc'];
        $allowedAttrs = ['xmlns','viewBox','width','height','x','y','x1','x2','y1','y2',
            'cx','cy','r','rx','ry','d','points','fill','stroke','stroke-width','stroke-linecap',
            'stroke-linejoin','stroke-miterlimit','opacity','fill-opacity','stroke-opacity',
            'transform','id','gradientUnits','gradientTransform','offset','stop-color',
            'stop-opacity','clip-path','mask','fill-rule','clip-rule','preserveAspectRatio',
            'version'];
        $nodes = [$dom->documentElement];
        while ($nodes) {
            $node = array_pop($nodes);
            if (!in_array($node->localName, $allowedTags, true)
                || $node->namespaceURI !== 'http://www.w3.org/2000/svg') {
                throw new RuntimeException('SVG contém elementos não permitidos.');
            }
            foreach ($node->attributes as $attribute) {
                $value = $attribute->value;
                if (!in_array($attribute->nodeName, $allowedAttrs, true)
                    || ($attribute->namespaceURI && $attribute->namespaceURI !== 'http://www.w3.org/2000/xmlns/')
                    || preg_match('/(?:https?:|data:|javascript:|@import|expression|[<>])/i', $value)
                    || (str_contains($value, 'url(') && !preg_match('/^url\\(#[a-zA-Z0-9_-]+\\)$/', $value))) {
                    throw new RuntimeException('SVG contém atributos não permitidos.');
                }
            }
            foreach ($node->childNodes as $child) {
                if ($child instanceof DOMElement) $nodes[] = $child;
                elseif (!($child instanceof DOMText || $child instanceof DOMComment)) {
                    throw new RuntimeException('SVG contém conteúdo não permitido.');
                }
            }
        }
        $content = $dom->saveXML($dom->documentElement);
        if ($content === false) throw new RuntimeException('Não foi possível processar o SVG.');
    }

    $directory = dirname(__DIR__, 2) . '/assets/uploads/branding';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('Não foi possível criar a pasta de imagens. Verifique permissões da hospedagem.');
    }
    if (!is_writable($directory)) throw new RuntimeException('Pasta de imagens sem permissão de escrita.');
    $name = $type . '-' . bin2hex(random_bytes(16)) . '.' . $extension;
    $destination = $directory . '/' . $name;
    if (file_put_contents($destination, $content, LOCK_EX) === false) {
        throw new RuntimeException('Não foi possível salvar a imagem.');
    }
    chmod($destination, 0644);
    return '/assets/uploads/branding/' . $name;
}
