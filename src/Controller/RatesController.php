<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RatesController extends AbstractController
{
    /**
     * @Route("/rates", name="rates")
     */
    public function index(Request $request)
    {
        $currencyNBP = file_get_contents("http://api.nbp.pl/api/exchangerates/rates/c/usd/2019-08-04/2019-09-04/?format=json");
        $data = json_decode($currencyNBP);
        dump($data);
        return $this->render('rates/index.html.twig', [
            'controller_name' => 'RatesController',
        ]);
    }
}
