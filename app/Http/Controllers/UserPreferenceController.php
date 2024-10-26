<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    protected array $validationRules = [
        'publisher' => 'string',
        'categories' => 'array',
        'categories.*' => 'exists:categories,name',
        'authors' => 'array',
        'authors.*' => 'string',
    ];

    /**
     * Display a listing of the resource.
     * TODO:: Create an admin role so only admins can see all other users preference
     */
    public function index()
    {
        return UserPreference::all();
    }

    /**
     * Store a newly created resource in storage.
     * TODO:: Create an admin role so only admins can create preference for other users
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules +
            ['user_id' => 'required|exists:users,id|unique:user_preferences,user_id'],
            ['user_id' => 'User has already set the initial preferences.']
        );

        $userPreference = new UserPreference($validated);

        $userPreference->save();

        return new ApiResponse('Ok');
    }

    /**
     * Display the specified resource.
     * TODO:: Create an admin role so only admins can see all other users preference
     */
    public function show(string $id)
    {
        return UserPreference::findOne($id);
    }

    /**
     * Update the specified resource in storage.
     * TODO:: Create an admin role so only admins can update preference for other users
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate($this->validationRules);
        $userPreference = UserPreference::findOrFail($id);

        $userPreference->update($validated);

        return new ApiResponse('Ok');
    }

    /**
     * Remove the specified resource from storage.
     * TODO:: Create an admin role so only admins can delete preference for other users
     */
    public function destroy(string $id)
    {
        UserPreference::findOrFail($id)->delete();

        return new ApiResponse('Ok');
    }

    public function updateMyPreferences(Request $request)
    {
        $validated = $request->validate($this->validationRules);

        if (isset($validated['categories']))
            $validated['categories'] = json_encode($validated['categories']);

        if (isset($validated['authors']))
            $validated['authors'] = json_encode($validated['authors']);

        UserPreference::upsert($validated + ['user_id' => Auth::user()->id], ['user_id']);

        return new ApiResponse('Ok');
    }
}
