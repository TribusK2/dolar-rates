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
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $query);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $apiRequest = curl_exec($ch);

            $rates = json_decode($apiRequest);

            if(curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
                $startDate = $date;
                $endDate = $today;

                return $this->render('rates/error.html.twig', [
                    'controller_name' => 'RatesController',
                    'errorMessage' => 'Brak danych',
                    'date' => $date,
                    'code' => 'USD',
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'ask' => '',
                    'bid' => '',
                ]);
                die;
            }
            curl_close($ch);

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

            // render class colors
            $growClass = 'text-success';
            $dropClass = 'text-danger';
            $equalClass = 'text-dark';
            if($ask > 0){
                $askClass = $growClass;
            }elseif($ask < 0){
                $askClass = $dropClass;
            }else{
                $askClass = $equalClass;
            }

            if($bid > 0){
                $bidClass = $growClass;
            }elseif($ask < 0){
                $bidClass = $dropClass;
            }else{
                $bidClass = $equalClass;
            }
            return $this->render('rates/rates.html.twig', [
                'controller_name' => 'RatesController',
                'date' => $date,
                'rates' => $rates->rates,
                'code' => $rates->code,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'ask' => round($ask, 4),
                'bid' => round($bid, 4),
                'askClass' => $askClass,
                'bidClass' => $bidClass,
            ]);
            die;
        }

        return $this->render('rates/index.html.twig', [
            'controller_name' => 'RatesController',
            'startDate' => '',
        ]);
    }
}
