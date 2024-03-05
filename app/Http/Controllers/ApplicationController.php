<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Race;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class ApplicationController extends Controller
{
    public function getAll(): JsonResponse
    {
        $applications = Application::all();

        $data = [];
        foreach ($applications as $application) {
            $data[] = [
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'club' => $application->club,
                'race_id' => $application->race_id,
            ];
        }

        return new JsonResponse([
            'data' => $data,
        ], 200);
    }

    public function get($id): JsonResponse
    {
        $application = Application::findOrFail($id);

        return new JsonResponse([
            'data' => [
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'club' => $application->club,
                'race_id' => $application->race_id,
            ]
        ]);
    }

    public function create(): JsonResponse
    {
        $application = Application::create(Request::all());

        return new JsonResponse([
            'data' => [
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'club' => $application->club,
                'race_id' => $application->race_id,
            ]
        ], 201);
    }

    public function delete($id) : JsonResponse
    {
        $application = Application::findOrFail($id);

        $application->delete();

        return new JsonResponse([]);
    }
}
