<?php


namespace App\Http\Controllers;


use App\Http\Service\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    private $recommendationService;

    public function __construct(RecommendationService $recommendationService){
        $this->recommendationService = $recommendationService;
    }

    public function postUserInput(Request $r){
        $space = $r->get('space');
        $moistures = $r->get('moistures');
        $wind = $r->get('wind');
        $minWidth = $r->get('minWidth');
        $maxWidth = $r->get('maxWidth');

        $recommendation = $this->recommendationService->getRecommendation($space, $moistures, $wind, $minWidth, $maxWidth);
        return $recommendation;
    }
}
