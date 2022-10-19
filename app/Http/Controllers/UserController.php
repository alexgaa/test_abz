<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Helpers\TokenHelpers;
use App\Helpers\UserHelpers;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    const RULE_VALIDATION_USERS = [
        'name'=> 'required|string|min:1|max:60',
        'email'=> 'required|email',
        'phone' => 'required|string|min:13|max:13|regex:/^[\+]{0,1}380([0-9]{9})$/',
        'position_id' => 'required|integer|min:1',
        'photo' => 'required|file|max:5000|dimensions:min_width=70,min_height=70|mimes:jpg,jpeg'
    ];

    const RULE_VALIDATION_REQUEST_GET_ALL = [
        'page'=> 'integer|min:1',
        'offset' => 'integer|min:1',
        'count' => 'integer|min:1|max:100'
    ];

    const ERROR_ID_NOT_INT = [
           "success" => false,
           "message" => "Validation failed" ,
           "fails" => [
               "user_id"=>["The user_id must be an integer."]
           ],
       ];

    const ERROR_ID_NOT_FOUND = [
        "success" => false,
        "message" => "The user with the requested identifier does not exist",
        "fails" => [
            "user_id"=>["User not found"]
        ],
    ];

    const ERROR_USERS_NOT_VALID = [
        "success" => false,
        "message" => "Validation failed" ,
    ];

    const ERROR_USERS_NOT_UNIQUE_EMAIL_OR_PHONE = [
        "success" => false,
        "message" => "User with this phone or email already exist" ,
    ];

    const ERROR_API_TINIFY = [
        "success" => false,
        "message" => "Image optimization error." ,
    ];

    const ERROR_TOKEN_NOT_VALID = [
        "success" => false,
        "message" => "The token expired." ,
    ];

    const VALID_USER_MESSAGES = [
        "success" => true,
        "message" => "New user successfully registered."
    ];

    const DEFAULT_VALUE_COUNT = 5;
    const DEFAULT_VALUE_PAGE = 1;

    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        $positions = Position::all();
        return view('admin.index', compact('positions'));
    }

    /**
     * @param Request $request
     * @param TokenHelpers $tokenHelper
     * @param UserHelpers $userHelpers
     * @param ImageHelper $imageHelper
     * @return JsonResponse
     */
    public function store(
        Request $request,
        TokenHelpers $tokenHelper,
        UserHelpers $userHelpers,
        ImageHelper $imageHelper
    ): JsonResponse
    {
        $token = $request->header('token');
        if($token === null || $tokenHelper->checkToken($token) === false) {
            return response()->json(self::ERROR_TOKEN_NOT_VALID, 401);
        }
        $tokenHelper->deleteToken($token);

        $validator = Validator::make($request->all(), self::RULE_VALIDATION_USERS);
        if($validator->fails())
        {
            $result = self::ERROR_USERS_NOT_VALID;
            $result['fails'] = $validator->errors()->getMessages();
            return response()->json($result, 422);
        }

        if(!$userHelpers->checkForUniqueMailPhone($request->email, $request->phone)) {
            $result = self::ERROR_USERS_NOT_UNIQUE_EMAIL_OR_PHONE;
            return response()->json($result, 409);
        }

        $pathFile = $imageHelper->optimisation($request, 'photo', 'images/user');
        if($pathFile === '') {
            $result = self::ERROR_API_TINIFY;
            return response()->json($result, 500);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->position_id = $request->position_id;
        $user->photo = asset($pathFile);
        $user->save();

        $result = self::VALID_USER_MESSAGES;
        $result["user_id"] = $user->id;

        return response()->json($result, 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getById(Request $request, $id): JsonResponse
    {
        if(preg_match("/^[0-9]+$/",$id) !== 1) {
            return response()->json(self::ERROR_ID_NOT_INT, 400);
        }

        $user = [];
        $userSearchResult = User::query()->find($id);
        if($userSearchResult) {
            $success = true;
            $user["id"] = $userSearchResult->id;
            $user["name"] = $userSearchResult->name;
            $user["email"] = $userSearchResult->email;
            $user["phone"] = $userSearchResult->phone;
            $user["position_id"] = $userSearchResult->position_id;
            $user["position"] = $userSearchResult->position->name;
            $user["photo"] = $userSearchResult->photo;
            return response()->json(compact('success', 'user'), 200);
        } else {
            return response()->json(self::ERROR_ID_NOT_FOUND, 404);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getAll(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(), self::RULE_VALIDATION_REQUEST_GET_ALL);
        if($validator->fails())
        {
            $result = self::ERROR_USERS_NOT_VALID;
            $result['fails'] = $validator->errors();
            return response()->json($result, 422);
        }
        $validated = $validator->validated();
        if(!isset($validated['count'])) {
            $validated['count'] = self::DEFAULT_VALUE_COUNT;
        }
        if(!isset($validated['page'])) {
            $validated['page'] = self::DEFAULT_VALUE_PAGE;
        }
            if(!isset($validated['offset'])) {
            $result = $this->selectWithPage($validated);
        } else {
            $result = $this->selectWithOffset($validated);
        }

        return response()->json($result, 200);
    }

    /**
     * @param array $validated
     * @return array
     */
    private function selectWithOffset(array $validated): array
    {
        $usersSearchResult = User::query()
            ->where('id','>', $validated['offset'])
            ->paginate($validated['count']);
        $nextPageUrl = $usersSearchResult->nextPageUrl();
        $previousPageUrl = null;

        if($nextPageUrl !== null) {
            $nextPageUrl = $usersSearchResult->path() . "?offset=" . $validated['offset'] + $validated['count'] . "&count="
                . $validated['count'];
        }
        if($validated['offset'] - $validated['count'] >= 0){
            $previousPageUrl = $usersSearchResult->path() . "?offset=" . $validated['offset'] - $validated['count']
                . "&count=" . $validated['count'];
        }

        $result['success'] = true;
        $result['offset'] = (int) $validated['offset'];
        $result = $this->createBodyResultResponseForGetAll($result, $usersSearchResult, $nextPageUrl, $previousPageUrl);
        return $result;
    }

    /**
     * @param array $validated
     * @return array
     */
    private function selectWithPage(array $validated): array
    {
        $usersSearchResult = User::query()->paginate($validated['count'], ['*'], 'page', $validated['page']);
        $nextPageUrl = $usersSearchResult->nextPageUrl();
        $previousPageUrl = $usersSearchResult->previousPageUrl();
        if($nextPageUrl !== null) {
            $nextPageUrl = $nextPageUrl .  "&count=" . $validated['count'];
        }
        if($previousPageUrl) {
            $previousPageUrl = $previousPageUrl . "&count=" . $validated['count'];
        }
        $result['success'] = true;
        $result['page'] = $usersSearchResult->currentPage();
        $result = $this->createBodyResultResponseForGetAll($result, $usersSearchResult,$nextPageUrl, $previousPageUrl);
        return $result;
    }

    /**
     * @param array $result
     * @param LengthAwarePaginator $usersSearchResult
     * @param string|null $nextPageUrl
     * @param string|null $previousPageUrl
     * @return array
     */
    private function createBodyResultResponseForGetAll(
        array $result,
        LengthAwarePaginator $usersSearchResult,
        string|null $nextPageUrl,
        string|null $previousPageUrl
    ): array
    {
        $result['total_pages'] = $usersSearchResult->lastPage();
        $result['total_users'] = $usersSearchResult->total();
        $result['count'] = $usersSearchResult->perPage();
        $result['links']['next_url'] = $nextPageUrl;
        $result['links']['prev_url'] = $previousPageUrl;
        $users = [];
        $i = 0;
        foreach ($usersSearchResult as $user) {
            $users[$i]["id"] = $user->id;
            $users[$i]["name"] = $user->name;
            $users[$i]["email"] = $user->email;
            $users[$i]["phone"] = $user->phone;
            $users[$i]["position_id"] = $user->position_id;
            $users[$i]["position"] = $user->position->name;
            $users[$i]["photo"] = $user->photo;
            $users[$i]['registration_timestamp'] = $user->created_at->timestamp;
            $i++;
        }
        $result['users'] = $users;
        return $result;
    }

}

