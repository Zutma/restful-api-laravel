<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse {
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))/*->additional(['errors' => null])*/->response()->setStatusCode(201);
    }

    public function get(int $idContact): JsonResponse {
        $user = Auth::user();

        $contact = Contact::where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $this->authorize('view', $contact);

        return (new ContactResource($contact))/*->additional(['errors' => null])*/->response();
    }

    public function update(int $idContact, ContactUpdateRequest $request): JsonResponse {
        $user = Auth::user();

        $contact = Contact::where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $this->authorize('update', $contact);

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return (new ContactResource($contact))/*->additional(['errors' => null])*/->response();
    }

    public function delete(int $idContact): JsonResponse {
        $user = Auth::user();

        $contact = Contact::where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $this->authorize('delete', $contact);

        $contact->delete();
        return response()->json([
            'data' => true,
            // 'errors' => null
        ], 200);
    }

    public function search(Request $request): JsonResponse {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $contacts = Contact::query();

        if ($user->role !== 'admin') {
            $contacts->where('user_id', $user->id);
        }

        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input('name');
            if ($name) {
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orWhere('first_name', 'like', '%' . $name . '%');
                    $builder->orWhere('last_name', 'like', '%' . $name . '%');
                });
            }

            $email = $request->input('email');
            if ($email) {
                $builder->where('email', 'like', '%' . $email . '%');
            }

            $phone = $request->input('phone');
            if ($phone) {
                $builder->where('phone', 'like', '%' . $phone . '%');
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);
        return (new ContactCollection($contacts))/*->additional(['errors' => null])*/->response();
    }

}