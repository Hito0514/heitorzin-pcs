<?php
/**
 * Upload seguro de imagens (valida tipo real, tamanho e gera nome aleatório).
 */
class Upload
{
    private const TIPOS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    /**
     * Salva a imagem enviada.
     * @return string|null nome do arquivo salvo, ou null se nenhum arquivo foi enviado
     * @throws RuntimeException em caso de arquivo inválido
     */
    public static function imagem(?array $arquivo): ?string
    {
        if (!$arquivo || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Falha no envio da imagem (código ' . $arquivo['error'] . ').');
        }
        if ($arquivo['size'] > UPLOAD_MAX_BYTES) {
            throw new RuntimeException('A imagem deve ter no máximo 2 MB.');
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
        if (!isset(self::TIPOS[$mime])) {
            throw new RuntimeException('Formato inválido. Envie JPG, PNG, WEBP ou GIF.');
        }
        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0775, true);
        }
        $nome = bin2hex(random_bytes(12)) . '.' . self::TIPOS[$mime];
        if (!move_uploaded_file($arquivo['tmp_name'], UPLOAD_PATH . '/' . $nome)) {
            throw new RuntimeException('Não foi possível salvar a imagem no servidor.');
        }
        return $nome;
    }

    public static function remover(?string $nome): void
    {
        if ($nome && preg_match('/^[a-f0-9]{24}\.(jpg|png|webp|gif)$/', $nome)) {
            $caminho = UPLOAD_PATH . '/' . $nome;
            if (is_file($caminho)) {
                unlink($caminho);
            }
        }
    }
}
