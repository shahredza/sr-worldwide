<?php
/**
 * Safe product image upload.
 * Checks the real file type (not the name or browser-sent type), size and that it's a real image,
 * then stores it under a random name in public/assets/uploads/.
 */

const UPLOAD_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];

/**
 * @return array{path: ?string, error: ?string}  path is relative to public/assets/ (e.g. uploads/ab12.jpg)
 */
function handle_image_upload(string $field): array
{
    $file = $_FILES[$field] ?? null;

    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['path' => null, 'error' => null];
    }
    if (!is_array($file['error']) && in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
        return ['path' => null, 'error' => 'Image is too large (max ' . (UPLOAD_MAX_BYTES / 1024 / 1024) . ' MB).'];
    }
    if (is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        return ['path' => null, 'error' => 'Upload failed, please try again.'];
    }
    if ($file['size'] > UPLOAD_MAX_BYTES) {
        return ['path' => null, 'error' => 'Image is too large (max ' . (UPLOAD_MAX_BYTES / 1024 / 1024) . ' MB).'];
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset(UPLOAD_TYPES[$mime]) || @getimagesize($file['tmp_name']) === false) {
        return ['path' => null, 'error' => 'Only JPG, PNG or WebP images are allowed.'];
    }

    $name = bin2hex(random_bytes(16)) . '.' . UPLOAD_TYPES[$mime];
    $dir = APP_ROOT . '/public/assets/uploads';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        return ['path' => null, 'error' => 'Upload folder is not writable.'];
    }
    if (!move_uploaded_file($file['tmp_name'], "$dir/$name")) {
        return ['path' => null, 'error' => 'Could not save the image.'];
    }
    chmod("$dir/$name", 0644);

    return ['path' => 'uploads/' . $name, 'error' => null];
}

/** Delete an uploaded image. Only touches files inside uploads/ (never the bundled demo photos). */
function delete_uploaded_image(?string $path): void
{
    if ($path && preg_match('#^uploads/[a-f0-9]{32}\.(jpg|png|webp)$#', $path)) {
        $full = APP_ROOT . '/public/assets/' . $path;
        if (is_file($full)) {
            unlink($full);
        }
    }
}
