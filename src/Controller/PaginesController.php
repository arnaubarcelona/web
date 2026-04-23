<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;

class PaginesController extends AppController
{
    public function index()
    {
        $this->paginate = [
            'order' => [
                'Pagines.order_code' => 'ASC',
                'Pagines.id' => 'ASC',
            ],
        ];

        $pagines = $this->paginate($this->Pagines);
        $this->set(compact('pagines'));
    }

    public function actualitza()
    {
        $this->request->allowMethod(['post']);

        $output = [];
        $exitCode = 0;
        exec('flock -n /tmp/web_sync.lock mysql < /opt/web_sync/sync_web.sql 2>&1', $output, $exitCode);

        if ($exitCode === 0) {
            $this->Flash->success(__('Sincronització web executada correctament.'));
        } else {
            $this->Flash->error(__('No s\'ha pogut executar la sincronització web ({0}). {1}', $exitCode, implode("\n", $output)));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Pàgina pública
     * /pagines/view/{identifier} (format antic)
     * /{slug} (format nou)
     */
    public function view(string $identifier)
    {
        $lookup = trim($identifier);
        $pagina = null;

        if (ctype_digit($lookup)) {
            $pagina = $this->Pagines->find()
                ->where(['Pagines.id' => (int)$lookup])
                ->first();

            if ($pagina) {
                return $this->redirect(['_name' => 'pagina:view', 'slug' => $pagina->slug], 301);
            }
        }

        $lookupDecoded = urldecode($lookup);
        $pagina = $this->Pagines->find()
            ->where([
                'OR' => [
                    'Pagines.title' => $lookupDecoded,
                    'Pagines.link' => $lookupDecoded,
                ],
            ])
            ->first();

        if (!$pagina) {
            $lookupSlug = mb_strtolower($lookupDecoded);
            $pagina = $this->Pagines->find()->all()
                ->filter(function ($candidate) use ($lookupSlug) {
                    return $candidate->slug === $lookupSlug;
                })
                ->first();
        }

        if (!$pagina) {
            throw new NotFoundException(__('Pàgina no trobada'));
        }

        $this->set(compact('pagina'));
    }
}
