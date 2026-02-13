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

        $body = (string)($pagina->body ?? '');

        if ($body !== '') {
            $view = $this->createView();

            // Regex que captura:
            //  - {element}
            //  - &#123;element&#125;
            $pattern = '/(?:\{|\&\#123;)\s*([a-zA-Z0-9_-]+)\s*(?:\}|\&\#125;)/';

            $body = preg_replace_callback($pattern, function ($m) use ($view) {
                $elementName = $m[1];

                // seguretat extra
                if ($elementName === '' || str_contains($elementName, '..') || str_contains($elementName, '/')) {
                    return $m[0];
                }

                // comprova existència (Cake 4: templates/element/)
                $path = ROOT . DS . 'templates' . DS . 'element' . DS . $elementName . '.php';
                if (!is_file($path)) {
                    return $m[0]; // deixa el placeholder tal qual
                }

                return $view->element($elementName);
            }, $body);

            $pagina->body = $body;
        }

        $this->set(compact('pagina'));
    }


}
