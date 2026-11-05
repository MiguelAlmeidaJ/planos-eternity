<?php
declare(strict_types=1);

/**
 * Envia uma única mensagem de teste via SMTP usando stream_socket_client.
 * Não depende de Composer; suporta SSL/TLS e STARTTLS com validação de certificado.
 */
function testSmtpDelivery(string $recipient): void
{
    if (!filter_var($recipient, FILTER_VALIDATE_EMAIL) || strlen($recipient) > 190
        || preg_match('/[\r\n]/', $recipient)) {
        throw new RuntimeException('Informe um e-mail de destino válido.');
    }
    $settings = settingsAll();
    $host = trim((string)($settings['SMTP_HOST'] ?? ''));
    $port = (int)($settings['SMTP_PORT'] ?? 0);
    $security = (string)($settings['SMTP_ENCRYPTION'] ?? '');
    $username = trim((string)($settings['SMTP_USER'] ?? ''));
    $from = trim((string)($settings['SMTP_FROM'] ?? ''));
    $fromName = trim((string)($settings['SMTP_FROM_NAME'] ?? 'Eternity'));
    if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9.-]{0,252}$/D', $host)
        || $port < 1 || $port > 65535
        || !in_array($security, ['ssl','tls'], true)
        || !filter_var($from, FILTER_VALIDATE_EMAIL)
        || !$username || preg_match('/[\r\n]/', $username)
        || preg_match('/[\r\n]/', $fromName)) {
        throw new RuntimeException('Revise servidor, porta, remetente, usuário e segurança (SSL/TLS ou STARTTLS).');
    }
    $encrypted = (string)($settings['SMTP_PASSWORD_ENC'] ?? '');
    if ($encrypted === '') throw new RuntimeException('Salve uma senha SMTP antes de testar.');
    $raw = base64_decode($encrypted, true);
    if ($raw === false || strlen($raw) < 29) throw new RuntimeException('Senha SMTP armazenada inválida.');
    $password = openssl_decrypt(
        substr($raw, 28), 'aes-256-gcm', keyBytes(),
        OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16)
    );
    if ($password === false) throw new RuntimeException('Não foi possível ler a senha SMTP. Confira APP_KEY.');

    $address = ($security === 'ssl' ? 'ssl://' : 'tcp://').$host.':'.$port;
    $context = stream_context_create(['ssl'=>[
        'verify_peer'=>true, 'verify_peer_name'=>true, 'peer_name'=>$host,
        'allow_self_signed'=>false, 'SNI_enabled'=>true
    ]]);
    $socket = @stream_socket_client($address, $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context);
    if (!is_resource($socket)) throw new RuntimeException('Não foi possível conectar ao servidor SMTP. Verifique host, porta e acesso de saída.');
    stream_set_timeout($socket, 10);
    try {
        smtpExpect($socket, [220]);
        smtpWrite($socket, "EHLO eternity.local\r\n");
        smtpExpect($socket, [250]);
        if ($security === 'tls') {
            smtpWrite($socket, "STARTTLS\r\n");
            smtpExpect($socket, [220]);
            if (@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT) !== true) {
                throw new RuntimeException('Falha ao negociar STARTTLS ou validar o certificado.');
            }
            smtpWrite($socket, "EHLO eternity.local\r\n");
            smtpExpect($socket, [250]);
        }
        smtpWrite($socket, "AUTH LOGIN\r\n");
        smtpExpect($socket, [334]);
        smtpWrite($socket, base64_encode($username)."\r\n");
        smtpExpect($socket, [334]);
        smtpWrite($socket, base64_encode($password)."\r\n");
        smtpExpect($socket, [235]);

        smtpWrite($socket, "MAIL FROM:<".$from.">\r\n");
        smtpExpect($socket, [250]);
        smtpWrite($socket, "RCPT TO:<".$recipient.">\r\n");
        smtpExpect($socket, [250,251]);
        smtpWrite($socket, "DATA\r\n");
        smtpExpect($socket, [354]);

        $safeName = str_replace(['\r','\n'], '', $fromName);
        $subject = 'Teste SMTP - Eternity';
        $text = "Este e-mail confirma que o servidor SMTP da Eternity aceitou uma mensagem de teste.\r\nData: ".date('d/m/Y H:i:s')."\r\n";
        $headers = [
            'From: '.sprintf('=?UTF-8?B?%s?=', base64_encode($safeName)).' <'.$from.'>',
            'To: <'.$recipient.'>',
            'Subject: =?UTF-8?B?'.base64_encode($subject).'?=',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            'Date: '.date(DATE_RFC2822)
        ];
        $body = implode("\r\n", $headers)."\r\n\r\n".$text;
        $body = preg_replace('/^\./m', '..', $body);
        smtpWrite($socket, $body."\r\n.\r\n");
        smtpExpect($socket, [250]);
        smtpWrite($socket, "QUIT\r\n");
    } finally {
        fclose($socket);
    }
}
function smtpWrite($socket, string $command): void
{
    if (@fwrite($socket, $command) !== strlen($command)) {
        throw new RuntimeException('Falha de comunicação com o servidor SMTP.');
    }
}
function smtpExpect($socket, array $allowed): void
{
    $last = '';
    for ($i = 0; $i < 60; $i++) {
        $line = @fgets($socket, 2048);
        if ($line === false) throw new RuntimeException('O servidor SMTP não respondeu dentro do prazo.');
        $last = $line;
        if (preg_match('/^([0-9]{3})([ -])/', $line, $matches) && $matches[2] === ' ') {
            if (!in_array((int)$matches[1], $allowed, true)) {
                // Nunca incluir resposta bruta: servidores podem reproduzir credenciais.
                throw new RuntimeException('Servidor SMTP recusou a etapa de envio (código '.$matches[1].').');
            }
            return;
        }
    }
    throw new RuntimeException('Resposta SMTP inválida.');
}
