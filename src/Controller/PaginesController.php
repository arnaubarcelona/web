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
     * /pagines/view/{id}
     */
    public function view(int $id)
    {
        $pagina = $this->Pagines->find()
            ->where(['Pagines.id' => $id])
            ->first();

        if (!$pagina) {
            throw new NotFoundException(__('Pàgina no trobada'));
        }

        $this->set(compact('pagina'));
    }
}
