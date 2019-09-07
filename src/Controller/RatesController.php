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
        // data from API
        $date = date("Y-m-d");
        if ($request->isMethod('POST')){

            // get data from submit form
            $date = $request->get('date');

            // set query to API
            $today = date("Y-m-d");
            $query = "http://api.nbp.pl/api/exchangerates/rates/c/usd/".$date."/".$today."/?format=json";

            // send query to API
            $apiRequest = file_get_contents($query);
            $rates = json_decode($apiRequest);
            dump($rates);
        }
        // dump($date);

        // calculation of rate
        $count = sizeof($rates->rates);
        $startDate = $rates->rates[0]->effectiveDate;
        $endDate = $rates->rates[$count-1]->effectiveDate;

        $startAsk = $rates->rates[0]->ask;
        $endAsk = $rates->rates[$count-1]->ask;

        $startBid = $rates->rates[0]->bid;
        $endBid = $rates->rates[$count-1]->bid;

        $ask = $endAsk - $startAsk;
        $bid = $endBid - $startBid;

        dump($ask);
        dump($bid);

        return $this->render('rates/index.html.twig', [
            'controller_name' => 'RatesController',
            'date' => $date,
            'rates' => $rates->rates,
            'code' => $rates->code,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'ask' => $ask,
            'bid' => $bid,
        ]);
    }
}
