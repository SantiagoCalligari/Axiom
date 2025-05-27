<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\University;
use App\Models\Career;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    // Suscribir a una universidad
    public function subscribeToUniversity(Request $request, University $university)
    {
        $user = Auth::user();
        $user->subscribedUniversities()->syncWithoutDetaching([$university->id]);
        return response()->json(['message' => 'Suscripción a universidad realizada correctamente']);
    }

    // Desuscribir de una universidad
    public function unsubscribeFromUniversity(Request $request, University $university)
    {
        $user = Auth::user();
        $user->subscribedUniversities()->detach($university->id);
        return response()->json(['message' => 'Suscripción a universidad cancelada correctamente']);
    }

    // Suscribir a una carrera
    public function subscribeToCareer(Request $request, University $university, Career $career)
    {
        $user = Auth::user();
        $user->subscribedCareers()->syncWithoutDetaching([$career->id]);
        return response()->json(['message' => 'Suscripción a carrera realizada correctamente']);
    }

    // Desuscribir de una carrera
    public function unsubscribeFromCareer(Request $request, University $university, Career $career)
    {
        $user = Auth::user();
        $user->subscribedCareers()->detach($career->id);
        return response()->json(['message' => 'Suscripción a carrera cancelada correctamente']);
    }

    // Suscribir a una materia
    public function subscribeToSubject(Request $request, University $university, Career $career, Subject $subject)
    {
        $user = Auth::user();
        $user->subscribedSubjects()->syncWithoutDetaching([$subject->id]);
        return response()->json(['message' => 'Suscripción a materia realizada correctamente']);
    }

    // Desuscribir de una materia
    public function unsubscribeFromSubject(Request $request, University $university, Career $career, Subject $subject)
    {
        $user = Auth::user();
        $user->subscribedSubjects()->detach($subject->id);
        return response()->json(['message' => 'Suscripción a materia cancelada correctamente']);
    }

    // Obtener suscripciones del usuario
    public function getUserSubscriptions()
    {
        $user = Auth::user();
        
        // Cargar las relaciones con sus slugs
        $universities = $user->subscribedUniversities()->get();
        $careers = $user->subscribedCareers()->with('university:id,name,slug')->get();
        $subjects = $user->subscribedSubjects()->with(['career:id,name,slug', 'career.university:id,name,slug'])->get();

        return response()->json([
            'universities' => $universities->map(function ($university) {
                return [
                    'id' => $university->id,
                    'name' => $university->name,
                    'slug' => $university->slug,
                    'description' => $university->description
                ];
            }),
            'careers' => $careers->map(function ($career) {
                return [
                    'id' => $career->id,
                    'name' => $career->name,
                    'slug' => $career->slug,
                    'description' => $career->description,
                    'university' => [
                        'id' => $career->university->id,
                        'name' => $career->university->name,
                        'slug' => $career->university->slug
                    ]
                ];
            }),
            'subjects' => $subjects->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'slug' => $subject->slug,
                    'description' => $subject->description,
                    'career' => [
                        'id' => $subject->career->id,
                        'name' => $subject->career->name,
                        'slug' => $subject->career->slug,
                        'university' => [
                            'id' => $subject->career->university->id,
                            'name' => $subject->career->university->name,
                            'slug' => $subject->career->university->slug
                        ]
                    ]
                ];
            })
        ]);
    }
} 