<?php


namespace App\Http\Service;


use App\Models\SmallPlant;
use App\Models\Tree;

class RecommendationService
{
    public function getRecommendation(string $space, $moistures, string $wind, float $minWidth ,float $maxWidth){
        if ($moistures == null){
            if ($space == 'largeSpace'){
                $trees = Tree::all()->where('wind', $wind);
                return response()->json([
                    'success' => true,
                    'data' => $trees,
                ]);
            } else {
                $smallPlants = SmallPlant::all()
                    ->whereBetween('width', [$minWidth, $maxWidth]);
                return response()->json([
                    'success' => true,
                    'data' => $smallPlants,
                ]);
            }
        } else{
            if ($space == 'largeSpace'){
                $trees = Tree::all()->whereIn('moisture', $moistures)->where('wind', $wind);
                return response()->json([
                    'success' => true,
                    'data' => $trees,
                ]);
            } else {
                $smallPlants = SmallPlant::all()->whereIn('moisture', $moistures)
                    ->whereBetween('width', [$minWidth, $maxWidth]);
                return response()->json([
                    'success' => true,
                    'data' => $smallPlants,
                ]);
            }
        }
    }
}
