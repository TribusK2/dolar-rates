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
        $date = date("Y-m-d");
        if ($request->isMethod('POST')){
            // get data from submit form
            $date = $request->get('date');
            dump($date);

            // set query to API
            $today = date("Y-m-d");
            dump($today);
            $query = "http://api.nbp.pl/api/exchangerates/rates/c/usd/".$date."/".$today."/?format=json";
            dump($query);
            // send query to API
            $apiRequest = file_get_contents($query);
            $rates = json_decode($apiRequest);
            dump($rates);
        }
        // dump($date);

        return $this->render('rates/index.html.twig', [
            'controller_name' => 'RatesController',
            'date' => $date,
            'rates' => $rates->rates,
            'code' => $rates->code,
        ]);
    }
}
