<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailboxService
{
    /**
     * Test IMAP or POP3 Connection with credentials.
     */
    public function testIncomingConnection(string $protocol, string $host, int $port, string $username, string $password): array
    {
        $protocol = strtolower($protocol);
        if ($protocol === 'pop3') {
            return $this->testPop3Connection($host, $port, $username, $password);
        }
        return $this->testImapConnection($host, $port, $username, $password);
    }

    /**
     * Test IMAP SSL (Port 993)
     */
    protected function testImapConnection(string $host, int $port, string $username, string $password): array
    {
        $target = "ssl://{$host}:{$port}";
        $errno = 0;
        $errstr = '';

        $socket = @stream_socket_client($target, $errno, $errstr, 8, STREAM_CLIENT_CONNECT, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]));

        if (!$socket) {
            return [
                'success' => false,
                'message' => "Gagal terhubung ke {$target}: [{$errno}] {$errstr}",
            ];
        }

        stream_set_timeout($socket, 8);
        $banner = fgets($socket, 512);

        // Send LOGIN command: TAG1 LOGIN "username" "password"
        $tag = 'A001';
        $loginCmd = "{$tag} LOGIN " . $this->escapeImapString($username) . " " . $this->escapeImapString($password) . "\r\n";
        fwrite($socket, $loginCmd);

        $authResponse = '';
        while ($line = fgets($socket, 1024)) {
            $authResponse .= $line;
            if (str_starts_with($line, "{$tag} OK") || str_starts_with($line, "{$tag} NO") || str_starts_with($line, "{$tag} BAD")) {
                break;
            }
        }

        // Logout
        fwrite($socket, "A002 LOGOUT\r\n");
        fclose($socket);

        if (str_contains($authResponse, "{$tag} OK")) {
            return [
                'success' => true,
                'message' => 'Autentikasi IMAP Berhasil!',
                'server_banner' => trim($banner),
            ];
        }

        return [
            'success' => false,
            'message' => 'Autentikasi IMAP Gagal: ' . trim($authResponse),
            'server_banner' => trim($banner),
        ];
    }

    /**
     * Test POP3 SSL (Port 995)
     */
    protected function testPop3Connection(string $host, int $port, string $username, string $password): array
    {
        $target = "ssl://{$host}:{$port}";
        $errno = 0;
        $errstr = '';

        $socket = @stream_socket_client($target, $errno, $errstr, 8, STREAM_CLIENT_CONNECT, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]));

        if (!$socket) {
            return [
                'success' => false,
                'message' => "Gagal terhubung ke {$target}: [{$errno}] {$errstr}",
            ];
        }

        stream_set_timeout($socket, 8);
        $banner = fgets($socket, 512);

        // POP3 USER & PASS
        fwrite($socket, "USER {$username}\r\n");
        $userResp = fgets($socket, 512);

        fwrite($socket, "PASS {$password}\r\n");
        $passResp = fgets($socket, 512);

        fwrite($socket, "QUIT\r\n");
        fclose($socket);

        if (str_starts_with(trim($passResp), '+OK')) {
            return [
                'success' => true,
                'message' => 'Autentikasi POP3 Berhasil!',
                'server_banner' => trim($banner),
            ];
        }

        return [
            'success' => false,
            'message' => 'Autentikasi POP3 Gagal: ' . trim($passResp),
            'server_banner' => trim($banner),
        ];
    }

    /**
     * Get Inbox Emails via IMAP.
     */
    public function getInboxMessages(string $host, int $port, string $username, string $password, int $limit = 20): array
    {
        $target = "ssl://{$host}:{$port}";
        $errno = 0;
        $errstr = '';

        $socket = @stream_socket_client($target, $errno, $errstr, 8, STREAM_CLIENT_CONNECT, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]));

        if (!$socket) {
            return [
                'success' => false,
                'message' => "Gagal terhubung ke server mail: [{$errno}] {$errstr}",
                'messages' => [],
            ];
        }

        stream_set_timeout($socket, 8);
        fgets($socket, 512);

        // LOGIN
        $tag = 'A01';
        fwrite($socket, "{$tag} LOGIN " . $this->escapeImapString($username) . " " . $this->escapeImapString($password) . "\r\n");

        $authOk = false;
        while ($line = fgets($socket, 1024)) {
            if (str_starts_with($line, "{$tag} OK")) {
                $authOk = true;
                break;
            } elseif (str_starts_with($line, "{$tag} NO") || str_starts_with($line, "{$tag} BAD")) {
                break;
            }
        }

        if (!$authOk) {
            fclose($socket);
            return [
                'success' => false,
                'message' => 'Autentikasi IMAP gagal. Pastikan username dan password akun email sudah benar.',
                'messages' => [],
            ];
        }

        // SELECT INBOX
        $tag = 'A02';
        fwrite($socket, "{$tag} SELECT INBOX\r\n");
        $totalMessages = 0;
        while ($line = fgets($socket, 1024)) {
            if (preg_match('/^\*\s+(\d+)\s+EXISTS/i', $line, $m)) {
                $totalMessages = (int) $m[1];
            }
            if (str_starts_with($line, "{$tag} OK") || str_starts_with($line, "{$tag} NO")) {
                break;
            }
        }

        $messages = [];
        if ($totalMessages > 0) {
            $start = max(1, $totalMessages - $limit + 1);
            $end = $totalMessages;

            $tag = 'A03';
            // FETCH headers
            fwrite($socket, "{$tag} FETCH {$start}:{$end} (BODY.PEEK[HEADER.FIELDS (FROM TO SUBJECT DATE)])\r\n");

            $currentMsgNum = null;
            $currentHeaders = '';

            while ($line = fgets($socket, 4096)) {
                if (str_starts_with($line, "{$tag} OK") || str_starts_with($line, "{$tag} NO") || str_starts_with($line, "{$tag} BAD")) {
                    if ($currentMsgNum !== null) {
                        $messages[] = $this->parseHeaders($currentMsgNum, $currentHeaders);
                    }
                    break;
                }

                if (preg_match('/^\*\s+(\d+)\s+FETCH/i', $line, $m)) {
                    if ($currentMsgNum !== null) {
                        $messages[] = $this->parseHeaders($currentMsgNum, $currentHeaders);
                    }
                    $currentMsgNum = (int) $m[1];
                    $currentHeaders = '';
                } elseif ($currentMsgNum !== null) {
                    if (trim($line) === ')') {
                        $messages[] = $this->parseHeaders($currentMsgNum, $currentHeaders);
                        $currentMsgNum = null;
                        $currentHeaders = '';
                    } else {
                        $currentHeaders .= $line;
                    }
                }
            }
        }

        fwrite($socket, "A04 LOGOUT\r\n");
        fclose($socket);

        // Sort descending (latest first)
        $messages = array_reverse($messages);

        return [
            'success' => true,
            'total' => $totalMessages,
            'messages' => $messages,
        ];
    }

    /**
     * Get single email body via IMAP.
     */
    public function getMessageDetail(string $host, int $port, string $username, string $password, int $msgId): array
    {
        $target = "ssl://{$host}:{$port}";
        $socket = @stream_socket_client($target, $errno, $errstr, 8, STREAM_CLIENT_CONNECT, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]));

        if (!$socket) {
            return ['success' => false, 'message' => "Connection failed: [{$errno}] {$errstr}"];
        }

        stream_set_timeout($socket, 8);
        fgets($socket, 512);

        // LOGIN
        $tag = 'B01';
        fwrite($socket, "{$tag} LOGIN " . $this->escapeImapString($username) . " " . $this->escapeImapString($password) . "\r\n");
        while ($line = fgets($socket, 1024)) {
            if (str_starts_with($line, "{$tag} OK") || str_starts_with($line, "{$tag} NO")) break;
        }

        // SELECT INBOX
        fwrite($socket, "B02 SELECT INBOX\r\n");
        while ($line = fgets($socket, 1024)) {
            if (str_starts_with($line, "B02 OK") || str_starts_with($line, "B02 NO")) break;
        }

        // FETCH BODY
        $tag = 'B03';
        fwrite($socket, "{$tag} FETCH {$msgId} (BODY[TEXT] BODY.PEEK[HEADER])\r\n");
        $raw = '';
        while ($line = fgets($socket, 4096)) {
            if (str_starts_with($line, "{$tag} OK") || str_starts_with($line, "{$tag} NO") || str_starts_with($line, "{$tag} BAD")) {
                break;
            }
            $raw .= $line;
        }

        fwrite($socket, "B04 LOGOUT\r\n");
        fclose($socket);

        return [
            'success' => true,
            'id'      => $msgId,
            'raw'     => $raw,
            'body'    => quoted_printable_decode(strip_tags($raw)),
        ];
    }

    protected function parseHeaders(int $num, string $headerText): array
    {
        $subject = 'No Subject';
        $from = 'Unknown';
        $to = 'Unknown';
        $date = '';

        $lines = explode("\n", str_replace("\r", "", $headerText));
        $currentKey = '';
        $headers = [];

        foreach ($lines as $line) {
            if (preg_match('/^([A-Za-z\-]+):\s*(.*)$/', $line, $m)) {
                $currentKey = strtolower($m[1]);
                $headers[$currentKey] = trim($m[2]);
            } elseif ($currentKey && (str_starts_with($line, " ") || str_starts_with($line, "\t"))) {
                $headers[$currentKey] .= ' ' . trim($line);
            }
        }

        return [
            'id'      => $num,
            'from'    => $headers['from'] ?? $from,
            'to'      => $headers['to'] ?? $to,
            'subject' => $this->decodeMimeHeader($headers['subject'] ?? $subject),
            'date'    => $headers['date'] ?? $date,
        ];
    }

    protected function decodeMimeHeader(string $text): string
    {
        if (function_exists('mb_decode_mimeheader')) {
            return mb_decode_mimeheader($text);
        }
        return iconv_mime_decode($text, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
    }

    protected function escapeImapString(string $str): string
    {
        return '"' . addcslashes($str, '\\"') . '"';
    }
}
