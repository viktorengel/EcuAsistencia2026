<?php
class Mailer {

    public static function send($to, $subject, $body) {
        // socket_create disponible + sendmail_path configurado en este hosting
        return self::sendViaSendmail($to, $subject, $body);
    }

    private static function sendViaSendmail($to, $subject, $body) {
        $from     = defined('SMTP_FROM') ? SMTP_FROM : 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $fromName = defined('SMTP_NAME') ? SMTP_NAME : 'EcuAsistencia';
        $plainText = strip_tags(str_replace(['<br>','<br/>','</p>','<p>'], "\n", $body));
        $boundary  = md5(uniqid(rand(), true));

        $headers  = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$from}>\r\n";
        $headers .= "To: {$to}\r\n";
        $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
        $headers .= "X-Mailer: EcuAsist/1.0\r\n";

        $message  = "--{$boundary}\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= chunk_split(base64_encode($plainText)) . "\r\n";
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= chunk_split(base64_encode($body)) . "\r\n";
        $message .= "--{$boundary}--\r\n";

        $email = $headers . "\r\n" . $message;

        $sendmail = ini_get('sendmail_path') ?: '/usr/sbin/sendmail -t -i';

        $descriptors = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],  // stderr
        ];

        $process = proc_open($sendmail, $descriptors, $pipes);

        if (!is_resource($process)) {
            error_log("[EcuAsist][Mailer] proc_open falló al abrir sendmail");
            return false;
        }

        fwrite($pipes[0], $email);
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            error_log("[EcuAsist][Mailer] sendmail error (code {$exitCode}): {$stderr}");
            return false;
        }

        return true;
    }
}