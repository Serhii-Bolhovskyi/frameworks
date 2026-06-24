<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AppointmentApiController extends Controller
{
    // GET /api/appointments - список усіх записів
    public function index(): JsonResponse
    {
        $appointments = Appointment::all();

        return response()->json($appointments);
    }

    // GET /api/appointments/{id} - один запис
    public function show(int $id): JsonResponse
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        return response()->json($appointment);
    }

    // POST /api/appointments - створення нового запису
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'service_name' => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'status' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::create($validated);

        return response()->json($appointment, 201);
    }

    // PATCH /api/appointments/{id} - часткове оновлення
    public function update(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        $validated = $request->validate([
            'client_name' => 'sometimes|string|max:255',
            'service_name' => 'sometimes|string|max:255',
            'appointment_date' => 'sometimes|date',
            'status' => 'sometimes|string|max:255',
            'notes' => 'sometimes|nullable|string',
        ]);

        $appointment->update($validated);

        return response()->json($appointment);
    }

    // DELETE /api/appointments/{id} - видалення запису
    public function destroy(int $id): JsonResponse
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted']);
    }
}
