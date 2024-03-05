<?php

namespace App\Http\Controllers;

use App\Models\Race;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class RaceController extends Controller
{
    public function getAll() : JsonResponse
    {
        $races = Race::all();

        $data = [];
        foreach ($races as $race) {
            $data[] = [
                'name' => $race->name,
                'distance' => $race->distance,
            ];
        }

        return new JsonResponse([
            'data' => $data,
        ], 200);
    }

    public function get($id) : JsonResponse
    {
        $race = Race::findOrFail($id);

        return new JsonResponse([
            'data' => [
                'name' => $race->name,
                'distance' => $race->distance,
            ]
        ]);
    }

    public function create() : JsonResponse
    {
        $race = Race::create(Request::all());

        return new JsonResponse([
            'data' => [
                'name' => $race->name,
                'distance' => $race->distance,
            ]
        ], 201);
    }

    public function update($id) : JsonResponse
    {
        $race = Race::findOrFail($id);

        $race->update(Request::all());

        return new JsonResponse([
            'data' => [
                'name' => $race->name,
                'distance' => $race->distance,
            ]
        ]);
    }

    public function delete($id) : JsonResponse
    {
        $race = Race::findOrFail($id);

        $race->delete();

        return new JsonResponse([]);
    }
}
