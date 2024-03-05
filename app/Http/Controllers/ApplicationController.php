<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Race;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ApplicationController extends Controller
{
    public function getAll(): JsonResponse
    {
        $applications = Application::where([]);

        if (Auth::user()->role === 'Applicant') {
            $applications->where(['user_id' => Auth::user()->id]);
        }

        $applications = $applications->get();

        $data = [];
        foreach ($applications as $application) {
            $data[] = [
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'club' => $application->club,
                'race_id' => $application->race_id,
                'user_id' => $application->user_id,
            ];
        }

        return new JsonResponse([
            'data' => $data,
        ], 200);
    }

    public function get($id): JsonResponse
    {
        $application = Application::findOrFail($id);

        if (! Gate::allows('manage-application', $application)) {
            throw new AccessDeniedHttpException();
        }

        return new JsonResponse([
            'data' => [
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'club' => $application->club,
                'race_id' => $application->race_id,
                'user_id' => $application->user_id,
            ]
        ]);
    }

    public function create(): JsonResponse
    {
        $application = Application::create(array_merge(Request::all(), ['user_id' => Auth::user()->id]));

        if (! Gate::allows('manage-application', $application)) {
            throw new AccessDeniedHttpException();
        }

        return new JsonResponse([
            'data' => [
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'club' => $application->club,
                'race_id' => $application->race_id,
                'user_id' => $application->user_id,
            ]
        ], 201);
    }

    public function delete($id): JsonResponse
    {
        $application = Application::findOrFail($id);

        if (! Gate::allows('manage-application', $application)) {
            throw new AccessDeniedHttpException();
        }

        $application->delete();

        return new JsonResponse([]);
    }
}