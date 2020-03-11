<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $greet = '';
        if ($name = $request->query->get('hello')) {
            $greet = sprintf('<h1>Hello %s</h1>', htmlspecialchars($name));
        }
        return new Response(<<<EOF
                    <html>
                        <body style="display: flex; align-items: center; justify-content: center">
                            <div>$greet<br>
                            <img src="/images/under-construction.gif" />
                            </div>
                        </body>
                    </html>
EOF
        );
    }
}
