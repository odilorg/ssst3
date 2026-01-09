<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateTourRequest;
use Illuminate\Http\JsonResponse;

class TourValidationController extends Controller
{
    /**
     * Validate tour payload without persisting to database.
     *
     * This endpoint is for internal use to validate tour data before
     * submitting to the actual create/update endpoints.
     *
     * @param ValidateTourRequest $request
     * @return JsonResponse
     */
    public function validate(ValidateTourRequest $request): JsonResponse
    {
        // If we reach here, validation passed (FormRequest handles failures)
        return response()->json([
            'ok' => true,
            'message' => 'Payload is valid',
            'schema_version' => $request->input('schema_version', '1.0'),
            'validated_locales' => array_keys($request->input('translations', [])),
        ]);
    }
}
