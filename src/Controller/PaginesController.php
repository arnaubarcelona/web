<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;

class PaginesController extends AppController
{
    /**
     * Pàgina pública
     * /pagines/view/{id}
     */
    public function view(int $id)
    {
        $pagina = $this->Pagines->find()
            ->where([
                'Pagines.id' => $id,
                'Pagines.visible' => 1
            ])
            ->first();

        if (!$pagina) {
            throw new NotFoundException(__('Pàgina no trobada'));
        }

        $this->set(compact('pagina'));
    }
}
