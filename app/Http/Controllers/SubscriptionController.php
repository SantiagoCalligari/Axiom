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
        return response()->json([
            'universities' => $user->subscribedUniversities,
            'careers' => $user->subscribedCareers,
            'subjects' => $user->subscribedSubjects
        ]);
    }
} 