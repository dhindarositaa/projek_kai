<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {

            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON([
                        'status' => 'unauthorized',
                        'message' => 'Silakan login kembali'
                    ]);
            }

            return redirect()->to('/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}
