<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;

class WebrootController extends AppController
{
    /**
     * Serve legacy links that include /webroot/ in the public URL.
     *
     * Static assets in CakePHP are published from WWW_ROOT at the site root, but
     * older page content can contain href="/webroot/...". When those links reach
     * the CakePHP router, they are interpreted as a Webroot controller request.
     */
    public function file(string ...$path)
    {
        $relativePath = implode(DS, $path);

        if ($relativePath === '' || str_contains($relativePath, '..')) {
            throw new NotFoundException(__('Fitxer no trobat'));
        }

        $filePath = WWW_ROOT . $relativePath;
        if (!is_file($filePath)) {
            throw new NotFoundException(__('Fitxer no trobat'));
        }

        return $this->response->withFile($filePath, [
            'download' => false,
            'name' => basename($filePath),
        ]);
    }
}
