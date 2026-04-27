<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function getContact(int $idContact): Contact{
        $contact = Contact::where('id', $idContact)->first();
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'errors'=>[
                    'message'=>['not found']
                ]
            ],404));
        }
        $this->authorize('view', $contact);
        return $contact;
    }

    private function getAddress(Contact $contact, int $idAddress): Address{
        $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();
        if(!$address){
            throw new HttpResponseException(response()->json([
                'errors'=>[
                    'message'=>['not found']
                ]
            ],404));
        }
        return $address;
    }

    public function create(int $idContact, AddressCreateRequest $request): JsonResponse {
        $user = Auth::user();
        $contact = $this->getContact($idContact);

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))/*->additional(['errors' => null])*/->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress): JsonResponse {
        $contact = $this->getContact($idContact);
        $address = $this->getAddress($contact, $idAddress);
        $this->authorize('view', $address);

        return (new AddressResource($address))/*->additional(['errors' => null])*/->response();
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): JsonResponse {
        $contact = $this->getContact($idContact);
        $address = $this->getAddress($contact, $idAddress);
        $this->authorize('update', $address);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return (new AddressResource($address))/*->additional(['errors' => null])*/->response();
    }

    public function delete(int $idContact, int $idAddress): JsonResponse {
        $contact = $this->getContact($idContact);
        $address = $this->getAddress($contact, $idAddress);
        $this->authorize('delete', $address);

        $address->delete();

        return response()->json([
            'data' => true,
            // 'errors' => null
        ], 200);
    }

    public function list(int $idContact): JsonResponse {
        $contact = $this->getContact($idContact);

        $addresses = Address::where('contact_id', $contact->id)->get();

        return (AddressResource::collection($addresses))/*->additional(['errors' => null])*/->response();
    }
}
